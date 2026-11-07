<?php

namespace Filament\Upgrade\Support;

use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\NodeFinder;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use Rector\Configuration\Option;
use Rector\Configuration\Parameter\SimpleParameterProvider;
use Rector\FileSystem\FilesystemTweaker;
use Rector\PhpParser\Parser\RectorParser;
use Rector\Skipper\Skipper\Skipper;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class TraitConsumerResolver
{
    /**
     * @var array<string, array<string, ClassReflection>>
     */
    private array $traitConsumers = [];

    /**
     * @var array<string, true>
     */
    private array $sourceTraits = [];

    /**
     * @var array<string, ClassReflection>
     */
    private array $sourceClasses = [];

    /** @var array<string, ClassReflection> */
    private array $allSourceClasses = [];

    /** @var array<string, true> */
    private array $anonymousClassDependencies = [];

    private ?string $cachedSourceKey = null;

    public function __construct(
        private RectorParser $rectorParser,
        private ReflectionProvider $reflectionProvider,
        private Skipper $skipper,
        private FilesystemTweaker $filesystemTweaker,
    ) {}

    /**
     * @return array<string, ClassReflection>
     */
    public function getConsumers(ClassReflection $trait, string $currentFilePath): array
    {
        $this->collect($currentFilePath);

        // Do not migrate callers of a trait whose declaration is outside the upgrade paths.
        return isset($this->sourceTraits[$trait->getName()]) ? ($this->traitConsumers[$trait->getName()] ?? []) : [];
    }

    /**
     * @return array<string, ClassReflection>
     */
    public function getSourceClasses(string $currentFilePath, bool $includeSkipped = false): array
    {
        $this->collect($currentFilePath);

        return $includeSkipped ? $this->allSourceClasses : $this->sourceClasses;
    }

    /**
     * @return array<string, true>
     */
    public function getAnonymousClassDependencies(string $currentFilePath): array
    {
        $this->collect($currentFilePath);

        return $this->anonymousClassDependencies;
    }

    private function collect(string $currentFilePath): void
    {
        $sources = SimpleParameterProvider::provideArrayParameter(Option::SOURCE)
            ?: SimpleParameterProvider::provideArrayParameter(Option::PATHS);
        $sources = $sources ?: [$currentFilePath];
        $sourceKey = implode("\0", $sources);

        if ($this->cachedSourceKey === $sourceKey) {
            return;
        }

        $this->cachedSourceKey = $sourceKey;
        $this->traitConsumers = [];
        $this->sourceTraits = [];
        $this->sourceClasses = [];
        $this->allSourceClasses = [];
        $this->anonymousClassDependencies = [];
        $paths = [];

        // Include unchanged source files too: a cached consumer still constrains its trait.
        foreach ($this->filesystemTweaker->resolveWithFnmatch($sources) as $source) {
            if (is_file($source)) {
                $paths[] = $source;

                continue;
            }

            if (! is_dir($source)) {
                continue;
            }

            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS)) as $file) {
                if ($file->isFile() && ($file->getExtension() === 'php')) {
                    $paths[] = $file->getPathname();
                }
            }
        }

        foreach (array_unique($paths) as $path) {
            $isSkipped = $this->skipper->shouldSkipFilePath($path);
            $classes = (new NodeFinder)->findInstanceOf($this->rectorParser->parseFile($path), ClassLike::class);

            foreach ($classes as $declaration) {
                if (($declaration instanceof Class_) && (! $declaration->name)) {
                    // Pure inheritance adds no signature constraint. Only an
                    // anonymous override or trait implementation needs review.
                    if ((! $declaration->getMethod('get')) && (! $declaration->getTraitUses())) {
                        continue;
                    }

                    $dependencies = [...$declaration->implements];

                    if ($declaration->extends) {
                        $dependencies[] = $declaration->extends;
                    }

                    foreach ($declaration->getTraitUses() as $traitUse) {
                        array_push($dependencies, ...$traitUse->traits);
                    }

                    foreach ($dependencies as $dependency) {
                        $name = $dependency->toString();
                        $this->anonymousClassDependencies[strtolower($name)] = true;

                        if (! $this->reflectionProvider->hasClass($name)) {
                            continue;
                        }

                        $class = $this->reflectionProvider->getClass($name);

                        foreach ([$class, ...$class->getAncestors()] as $ancestor) {
                            $this->anonymousClassDependencies[strtolower($ancestor->getName())] = true;

                            foreach ($ancestor->getTraits(true) as $trait) {
                                $this->anonymousClassDependencies[strtolower($trait->getName())] = true;
                            }
                        }
                    }

                    continue;
                }

                $name = isset($declaration->namespacedName) ? $declaration->namespacedName->toString() : null;

                if ((! $name) || (! $this->reflectionProvider->hasClass($name))) {
                    continue;
                }

                $class = $this->reflectionProvider->getClass($name);
                $this->allSourceClasses[$name] = $class;

                if ($isSkipped) {
                    continue;
                }

                $this->sourceClasses[$name] = $class;

                if ($class->isTrait()) {
                    $this->sourceTraits[$name] = true;

                    continue;
                }

                if ($class->isInterface()) {
                    continue;
                }

                foreach ([$class, ...$class->getParents()] as $ancestor) {
                    foreach ($ancestor->getTraits(true) as $trait) {
                        $this->traitConsumers[$trait->getName()][$class->getName()] = $class;
                    }
                }
            }
        }
    }
}
