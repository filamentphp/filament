<?php

namespace Filament\Upgrade\Support;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\NodeFinder;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use Rector\Configuration\Option;
use Rector\Configuration\Parameter\SimpleParameterProvider;
use Rector\FileSystem\FilesystemTweaker;
use Rector\NodeTypeResolver\NodeScopeAndMetadataDecorator;
use Rector\NodeTypeResolver\NodeTypeResolver;
use Rector\PhpParser\Parser\RectorParser;
use Rector\PHPStan\ScopeFetcher;
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

    /** @var list<string> */
    private array $sourcePaths = [];

    /** @var array<string, list<Type>> */
    private array $methodReceiverTypes = [];

    private ?string $cachedSourceKey = null;

    public function __construct(
        private RectorParser $rectorParser,
        private ReflectionProvider $reflectionProvider,
        private Skipper $skipper,
        private FilesystemTweaker $filesystemTweaker,
        private NodeScopeAndMetadataDecorator $nodeScopeAndMetadataDecorator,
        private NodeTypeResolver $nodeTypeResolver,
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

    /**
     * @return array<string, ClassReflection>
     */
    public function getRelatedClasses(ClassReflection $class, string $methodName, string $currentFilePath): array
    {
        $classes = $this->getSourceClasses($currentFilePath);
        $related = $class->isTrait() ? $this->getConsumers($class, $currentFilePath) : [$class->getName() => $class];
        $receiverTypes = $this->getMethodReceiverTypes($methodName);

        do {
            $previousCount = count($related);
            $traits = [];

            foreach ($related as $member) {
                foreach ([$member, ...$member->getParents()] as $ancestor) {
                    foreach ($ancestor->getTraits(true) as $trait) {
                        if (isset($this->sourceTraits[$trait->getName()]) && $trait->hasNativeMethod($methodName)) {
                            $traits[$trait->getName()] = true;
                        }
                    }
                }
            }

            foreach ($classes as $candidate) {
                if ($candidate->isTrait() || $candidate->isInterface() || isset($related[$candidate->getName()])) {
                    continue;
                }

                foreach ($related as $member) {
                    if (($candidate->getName() === $member->getName()) || $candidate->isSubclassOf($member->getName()) || $member->isSubclassOf($candidate->getName())) {
                        $related[$candidate->getName()] = $candidate;

                        continue 2;
                    }
                }

                foreach (array_keys($traits) as $traitName) {
                    if ($candidate->hasTraitUse($traitName)) {
                        $related[$candidate->getName()] = $candidate;

                        continue 2;
                    }
                }

                // A polymorphic caller links every implementation it can reach,
                // even when those implementations otherwise have separate owners.
                foreach ($receiverTypes as $receiverType) {
                    if (! $receiverType->isSuperTypeOf(new ObjectType($candidate->getName()))->yes()) {
                        continue;
                    }

                    foreach ($related as $member) {
                        if ($receiverType->isSuperTypeOf(new ObjectType($member->getName()))->yes()) {
                            $related[$candidate->getName()] = $candidate;

                            continue 3;
                        }
                    }
                }
            }
        } while (count($related) !== $previousCount);

        return $related;
    }

    /** @return list<Type> */
    private function getMethodReceiverTypes(string $methodName): array
    {
        $methodName = strtolower($methodName);

        if (isset($this->methodReceiverTypes[$methodName])) {
            return $this->methodReceiverTypes[$methodName];
        }

        $receivers = [];
        $finder = new NodeFinder;
        $isMatchingCall = static fn (Node $node): bool => ($node instanceof MethodCall || $node instanceof NullsafeMethodCall || $node instanceof StaticCall
            || (($node instanceof StaticPropertyFetch) && in_array($methodName, ['islazy', 'isbadgedeferred'], true)))
            && ($node->name instanceof Identifier) && (strtolower($node->name->toString()) === $methodName);

        foreach ($this->sourcePaths as $path) {
            $nodes = $this->rectorParser->parseFile($path);

            if (! $finder->findFirst($nodes, $isMatchingCall)) {
                continue;
            }

            $nodes = $this->nodeScopeAndMetadataDecorator->decorateNodesFromFile($path, $nodes);

            foreach ($finder->find($nodes, $isMatchingCall) as $call) {
                /** @var MethodCall | NullsafeMethodCall | StaticCall | StaticPropertyFetch $call */
                $receiver = ($call instanceof StaticCall || $call instanceof StaticPropertyFetch) ? $call->class : $call->var;
                $context = ScopeFetcher::fetch($call)->getClassReflection();

                if ($methodName === 'configuretopreviewmethod') {
                    $type = TypeCombinator::removeNull($this->nodeTypeResolver->getType($receiver));
                    $receiverClasses = ($type->isClassString()->yes() ? $type->getClassStringObjectType() : $type)->getObjectClassReflections();
                    $hasPublicHook = $receiverClasses && (! array_filter($receiverClasses, static fn (ClassReflection $receiver): bool => (! $receiver->hasNativeMethod('configureToPreviewMethod')) || (! $receiver->getNativeMethod('configureToPreviewMethod')->isPublic())));

                    if (! $hasPublicHook) {
                        $contexts = $context?->isTrait() ? $this->getConsumers($context, $path) : [$context];

                        if (! $contexts) {
                            continue;
                        }

                        foreach ($contexts as $scopeClass) {
                            if ((! $scopeClass) || (! (new ObjectType('Filament\\Forms\\Commands\\FileGenerators\\RichContentCustomBlockClassGenerator'))->isSuperTypeOf(new ObjectType($scopeClass->getName()))->yes())) {
                                continue 2;
                            }
                        }
                    }
                }

                if (($receiver instanceof Name) || (($receiver instanceof Variable) && ($receiver->name === 'this'))) {
                    // Contextual calls and backing-property references shared by a trait
                    // must migrate with all consumers, even without a hook declaration.
                    if ((! $context?->isTrait()) || (($receiver instanceof Name) && (! in_array(strtolower($receiver->toString()), ['parent', 'self', 'static'], true)))) {
                        continue;
                    }

                    $classes = $this->getConsumers($context, $path);
                } else {
                    $type = TypeCombinator::removeNull($this->nodeTypeResolver->getType($receiver));
                    $type = $type->isClassString()->yes() ? $type->getClassStringObjectType() : $type;
                    $classes = $type->getObjectClassReflections();
                }

                if ($classes) {
                    $receivers[] = TypeCombinator::union(...array_values(array_map(static fn (ClassReflection $receiver): ObjectType => new ObjectType($receiver->getName()), $classes)));
                }
            }
        }

        return $this->methodReceiverTypes[$methodName] = $receivers;
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
        $this->sourcePaths = [];
        $this->methodReceiverTypes = [];
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

            if (! $isSkipped) {
                $this->sourcePaths[] = $path;
            }

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
