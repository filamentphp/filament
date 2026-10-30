<?php

namespace Filament\Upgrade\Support;

use PhpParser\Node\Stmt\ClassLike;
use PhpParser\NodeFinder;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use Rector\Configuration\Option;
use Rector\Configuration\Parameter\SimpleParameterProvider;
use Rector\FileSystem\FilesystemTweaker;
use Rector\PhpParser\Parser\RectorParser;
use Rector\Skipper\Skipper\PathSkipper;
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

    private ?string $cachedSourceKey = null;

    public function __construct(
        private RectorParser $rectorParser,
        private ReflectionProvider $reflectionProvider,
        private PathSkipper $pathSkipper,
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
    public function getSourceClasses(string $currentFilePath): array
    {
        $this->collect($currentFilePath);

        return $this->sourceClasses;
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
            if ($this->pathSkipper->shouldSkip($path)) {
                continue;
            }

            $classes = (new NodeFinder)->findInstanceOf($this->rectorParser->parseFile($path), ClassLike::class);

            foreach ($classes as $declaration) {
                $name = isset($declaration->namespacedName) ? $declaration->namespacedName->toString() : null;

                if ((! $name) || (! $this->reflectionProvider->hasClass($name))) {
                    continue;
                }

                $class = $this->reflectionProvider->getClass($name);
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
