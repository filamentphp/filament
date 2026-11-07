<?php

namespace Filament\Upgrade\Rector;

use Filament\Upgrade\Support\TraitConsumerResolver;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\Node\Stmt\TraitUseAdaptation\Alias;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use Rector\Caching\Detector\ChangedFilesDetector;
use Rector\PhpParser\AstResolver;
use Rector\PHPStan\ScopeFetcher;
use Rector\PHPStanStaticTypeMapper\Enum\TypeKind;
use Rector\Rector\AbstractRector;
use Rector\StaticTypeMapper\StaticTypeMapper;
use Symfony\Component\Console\Input\ArgvInput;

class WidenAvatarProviderParameterTypeRector extends AbstractRector
{
    public function __construct(
        private StaticTypeMapper $staticTypeMapper,
        private TraitConsumerResolver $traitConsumerResolver,
        private AstResolver $astResolver,
        private ChangedFilesDetector $changedFilesDetector,
    ) {}

    public function getNodeTypes(): array
    {
        return [Class_::class, Interface_::class, Trait_::class];
    }

    /**
     * @param  Class_ | Interface_ | Trait_  $node
     */
    public function refactor(Node $node): ?Node
    {
        $parameter = $node->getMethod('get')?->params[0] ?? null;

        if (! $parameter?->type) {
            return null;
        }

        $parameterType = $this->staticTypeMapper->mapPhpParserNodePHPStanType($parameter->type);
        $authenticatableType = new ObjectType('Illuminate\\Contracts\\Auth\\Authenticatable');

        if ($parameterType->isSuperTypeOf($authenticatableType)->yes()) {
            return null;
        }

        if ((! $this->isOldContract($parameterType)) || (! $this->canWidenFamily($node))) {
            return null;
        }

        $parameter->type = $this->staticTypeMapper->mapPHPStanTypeToPhpParserNode(
            TypeCombinator::union($parameterType, $authenticatableType),
            TypeKind::PARAM,
        );

        return $node;
    }

    private function isOldContract(Type $type): bool
    {
        // Custom restrictions and intersections still require manual review.
        return TypeCombinator::removeNull($type)->equals(new ObjectType('Illuminate\\Database\\Eloquent\\Model'));
    }

    private function canWidenFamily(ClassLike $node): bool
    {
        $context = ScopeFetcher::fetch($node)->getClassReflection();

        if (! $context) {
            return false;
        }

        $classes = $this->traitConsumerResolver->getSourceClasses($this->getFile()->getFilePath(), includeSkipped: true);
        $families = [];

        foreach ($classes as $class) {
            $owners = [];
            $implementationTraits = [];

            foreach ([$class, ...$class->getParents()] as $ancestor) {
                if (! $ancestor->hasNativeMethod('get')) {
                    continue;
                }

                $reflection = $ancestor->getNativeMethod('get');
                $trait = method_exists($reflection, 'getDeclaringTrait') ? $reflection->getDeclaringTrait() : null;

                if ($trait) {
                    $implementationTraits[$trait->getName()] = true;
                }
            }

            foreach ($this->getRelatedClasses($class) as $ancestor) {
                if ($ancestor->getName() === 'Filament\\AvatarProviders\\Contracts\\AvatarProvider') {
                    continue;
                }

                $declaration = $this->astResolver->resolveClassFromClassReflection($ancestor);
                $method = $declaration?->getMethod('get');

                if ((! $method) || $method->isPrivate()) {
                    continue;
                }

                // A concrete trait method hidden by a class declaration does
                // not supply or constrain that declaration's signature.
                if ($ancestor->isTrait() && (! $method->isAbstract()) && ($class->getName() !== $ancestor->getName()) && (! isset($implementationTraits[$ancestor->getName()]))) {
                    continue;
                }

                $owners[$ancestor->getName()] = $ancestor;
            }

            $families[] = [$class, $owners];
        }

        $connected = [$context->getName() => $context];
        $affected = [];

        do {
            $previousCount = count($connected);

            foreach ($families as [$class, $owners]) {
                if (! array_intersect_key($owners, $connected)) {
                    continue;
                }

                $connected += $owners;
                $affected[$class->getName()] = $class;
            }
        } while (count($connected) !== $previousCount);

        $providerType = new ObjectType('Filament\\AvatarProviders\\Contracts\\AvatarProvider');

        if (! array_filter($affected, static fn (ClassReflection $class): bool => $providerType->isSuperTypeOf(new ObjectType($class->getName()))->yes())) {
            return false;
        }

        // Anonymous overrides cannot be migrated as named declarations.
        if (array_intersect_key(array_change_key_case($affected), $this->traitConsumerResolver->getAnonymousClassDependencies($this->getFile()->getFilePath()))) {
            return false;
        }

        // Shared traits and parents must move with every related override, not
        // leave a narrower child behind or modify an out-of-scope declaration.
        foreach ($connected as $owner) {
            $method = $this->astResolver->resolveClassFromClassReflection($owner)?->getMethod('get');
            $parameter = $method?->params[0] ?? null;

            if (! $parameter) {
                return false;
            }

            if (! $parameter->type) {
                continue;
            }

            $type = $this->staticTypeMapper->mapPhpParserNodePHPStanType($parameter->type);

            if ($type->isSuperTypeOf(new ObjectType('Illuminate\\Contracts\\Auth\\Authenticatable'))->yes()) {
                continue;
            }

            $filePath = $owner->getFileName();

            // A filtered or limited run can safely change a family contained
            // in this file, but cannot guarantee its other files will run.
            if (($filePath !== $this->getFile()->getFilePath()) && (new ArgvInput)->hasParameterOption(['--filter', '--only-suffix', '--max-changes'])) {
                return false;
            }

            if ((! isset($classes[$owner->getName()])) || (! $this->isOldContract($type)) || (! $filePath)
                || $this->skipper->shouldSkipFilePath($filePath)
                || $this->skipper->shouldSkipElementAndFilePath($this, $filePath)
                || (! $this->changedFilesDetector->hasFileChanged($filePath))) {
                return false;
            }
        }

        foreach ($affected as $class) {
            foreach ($this->getRelatedClasses($class) as $owner) {
                foreach ($this->astResolver->resolveClassFromClassReflection($owner)?->getTraitUses() ?? [] as $traitUse) {
                    foreach ($traitUse->adaptations as $adaptation) {
                        if ((strtolower($adaptation->method->toString()) === 'get') || (($adaptation instanceof Alias) && (strtolower($adaptation->newName?->toString() ?? '') === 'get'))) {
                            return false;
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * @return array<string, ClassReflection>
     */
    private function getRelatedClasses(ClassReflection $class): array
    {
        $related = [$class->getName() => $class] + $class->getAncestors();

        foreach ($related as $ancestor) {
            $related += $ancestor->getTraits(true);
        }

        return $related;
    }
}
