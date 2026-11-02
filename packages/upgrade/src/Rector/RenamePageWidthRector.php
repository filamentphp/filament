<?php

namespace Filament\Upgrade\Rector;

use Filament\Auth\Pages\EditProfile;
use Filament\Pages\BasePage;
use Filament\Pages\SimplePage;
use Filament\Upgrade\Support\TraitConsumerResolver;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\TraitUse;
use PhpParser\Node\Stmt\TraitUseAdaptation\Alias;
use PhpParser\Node\VarLikeIdentifier;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\ObjectType;
use PHPStan\Type\TypeCombinator;
use Rector\PhpParser\Parser\RectorParser;
use Rector\PHPStan\ScopeFetcher;
use Rector\Rector\AbstractRector;

class RenamePageWidthRector extends AbstractRector
{
    /**
     * @var array<string, bool>
     */
    private array $legacyWidthUsage = [];

    public function __construct(
        private TraitConsumerResolver $traitConsumerResolver,
        private RectorParser $rectorParser,
    ) {}

    public function getNodeTypes(): array
    {
        return [Property::class, ClassMethod::class, PropertyFetch::class, MethodCall::class, NullsafeMethodCall::class, StaticCall::class, TraitUse::class];
    }

    /**
     * @param  Property | ClassMethod | PropertyFetch | MethodCall | NullsafeMethodCall | StaticCall | TraitUse  $node
     */
    public function refactor(Node $node): Node | int | null
    {
        $context = ScopeFetcher::fetch($node)->getClassReflection();

        if ($node instanceof Property || $node instanceof ClassMethod || $node instanceof TraitUse) {
            if ((! $context) || (! $this->canRename($context))) {
                return null;
            }

            if ($node instanceof Property) {
                foreach ($node->props as $property) {
                    if ($property->name->toString() === 'maxWidth') {
                        $property->name = new VarLikeIdentifier('maxContentWidth');

                        return $node;
                    }
                }

                return null;
            }

            if ($node instanceof ClassMethod) {
                if (! $this->isName($node->name, 'getMaxWidth')) {
                    return null;
                }

                $node->name = new Identifier('getMaxContentWidth');

                return $node;
            }

            if ($node->adaptations) {
                $changed = false;

                foreach ($node->adaptations as $adaptation) {
                    if ($this->isName($adaptation->method, 'getMaxWidth')) {
                        $adaptation->method = new Identifier('getMaxContentWidth');
                        $changed = true;
                    }

                    if ($adaptation instanceof Alias && $adaptation->newName && $this->isName($adaptation->newName, 'getMaxWidth')) {
                        $adaptation->newName = new Identifier('getMaxContentWidth');
                        $changed = true;
                    }
                }

                return $changed ? $node : null;
            }

            foreach ($node->traits as $index => $trait) {
                if ($this->isName($trait, 'Filament\\Pages\\Concerns\\HasMaxWidth')) {
                    unset($node->traits[$index]);
                    $node->traits = array_values($node->traits);

                    return $node->traits ? $node : NodeTraverser::REMOVE_NODE;
                }
            }

            return null;
        }

        if ((! $node->name instanceof Identifier) || (! $this->isName($node->name, $node instanceof PropertyFetch ? 'maxWidth' : 'getMaxWidth'))) {
            return null;
        }

        $receiver = $node instanceof StaticCall ? $node->class : $node->var;

        if (($receiver instanceof Name && $this->isNames($receiver, ['parent', 'self', 'static']))
            || ($context?->isTrait() && $receiver instanceof Variable && ($receiver->name === 'this'))) {
            $classes = $context ? [$context] : [];
        } else {
            $type = TypeCombinator::removeNull($this->getType($receiver));

            if ($node instanceof StaticCall && $type->isClassString()->yes()) {
                $type = $type->getClassStringObjectType();
            }

            if (! TypeCombinator::union(new ObjectType(SimplePage::class), new ObjectType(EditProfile::class))->isSuperTypeOf($type)->yes()) {
                return null;
            }

            $classes = $type->getObjectClassReflections();
        }

        if (! $classes) {
            return null;
        }

        foreach ($classes as $class) {
            if (! $this->canRename($class)) {
                return null;
            }
        }

        $node->name = new Identifier($node instanceof PropertyFetch ? 'maxContentWidth' : 'getMaxContentWidth');

        return $node;
    }

    private function canRename(ClassReflection $class): bool
    {
        $path = $this->getFile()->getFilePath();
        $consumers = $class->isTrait() ? $this->traitConsumerResolver->getConsumers($class, $path) : [$class];
        $pageType = TypeCombinator::union(new ObjectType(SimplePage::class), new ObjectType(EditProfile::class));

        if (! $consumers) {
            return false;
        }

        // A page can connect otherwise separate width traits, so migrate whole connected families.
        $sourceClasses = $this->traitConsumerResolver->getSourceClasses($path);
        $candidates = array_filter(
            $sourceClasses,
            static fn (ClassReflection $candidate): bool => $pageType->isSuperTypeOf(new ObjectType($candidate->getName()))->yes(),
        );
        $family = [];

        foreach ($consumers as $consumer) {
            $family[$consumer->getName()] = $consumer;
        }

        do {
            $previousCount = count($family);

            foreach ($candidates as $candidate) {
                if ((! isset($family[$candidate->getName()]))
                    && array_filter($family, fn (ClassReflection $member): bool => $this->isRelated($member, $candidate))) {
                    $family[$candidate->getName()] = $candidate;
                }
            }
        } while (count($family) !== $previousCount);

        $consumers = $family;

        foreach ($consumers as $consumer) {
            if (! $pageType->isSuperTypeOf(new ObjectType($consumer->getName()))->yes()) {
                return false;
            }

            foreach ($this->getWidthOwners($consumer) as $owner) {
                if (! isset($sourceClasses[$owner->getName()])) {
                    return false;
                }
            }

            foreach ([$consumer, ...$consumer->getParents()] as $ancestor) {
                foreach ($ancestor->getTraits(true) as $trait) {
                    if ($this->usesLegacyWidth($trait)) {
                        $traitConsumers = $this->traitConsumerResolver->getConsumers($trait, $path);

                        if (! $traitConsumers) {
                            return false;
                        }

                        foreach ($traitConsumers as $traitConsumer) {
                            if (! $pageType->isSuperTypeOf(new ObjectType($traitConsumer->getName()))->yes()) {
                                return false;
                            }
                        }
                    }
                }
            }
        }

        // Keep conflicting property and getter families together for manual reconciliation.
        foreach ($consumers as $candidate) {
            if ((! $candidate->hasNativeProperty('maxWidth')) && (! $candidate->hasNativeMethod('getMaxWidth'))) {
                continue;
            }

            if ($candidate->hasNativeProperty('maxContentWidth') && ($candidate->getNativeProperty('maxContentWidth')->getDeclaringClass()->getName() !== BasePage::class)) {
                return false;
            }

            if ($candidate->hasNativeMethod('getMaxContentWidth') && ($candidate->getNativeMethod('getMaxContentWidth')->getDeclaringClass()->getName() !== BasePage::class)) {
                return false;
            }
        }

        return true;
    }

    private function isRelated(ClassReflection $consumer, ClassReflection $candidate): bool
    {
        $consumerType = new ObjectType($consumer->getName());
        $candidateType = new ObjectType($candidate->getName());

        return $consumerType->isSuperTypeOf($candidateType)->yes()
            || $candidateType->isSuperTypeOf($consumerType)->yes()
            || (bool) array_intersect_key($this->getWidthOwners($consumer), $this->getWidthOwners($candidate));
    }

    /**
     * @return array<string, ClassReflection>
     */
    private function getWidthOwners(ClassReflection $class): array
    {
        $owners = [];

        foreach ([$class, ...$class->getParents()] as $ancestor) {
            if ($this->usesLegacyWidth($ancestor)) {
                $owners[$ancestor->getName()] = $ancestor;
            }

            if ($ancestor->hasNativeProperty('maxWidth')) {
                $owner = $ancestor->getNativeProperty('maxWidth')->getDeclaringClass();
                $owners[$owner->getName()] = $owner;
            }

            if ($ancestor->hasNativeMethod('getMaxWidth')) {
                $owner = $ancestor->getNativeMethod('getMaxWidth')->getDeclaringClass();
                $owners[$owner->getName()] = $owner;
            }

            foreach ($ancestor->getTraits(true) as $trait) {
                if ($this->usesLegacyWidth($trait)) {
                    $owners[$trait->getName()] = $trait;
                }
            }
        }

        return $owners;
    }

    private function usesLegacyWidth(ClassReflection $class): bool
    {
        if (array_key_exists($class->getName(), $this->legacyWidthUsage)) {
            return $this->legacyWidthUsage[$class->getName()];
        }

        if ($class->isTrait() && ($class->hasNativeProperty('maxWidth') || $class->hasNativeMethod('getMaxWidth'))) {
            return $this->legacyWidthUsage[$class->getName()] = true;
        }

        $finder = new NodeFinder;
        $declarations = $class->getFileName() ? $finder->findInstanceOf($this->rectorParser->parseFile($class->getFileName()), ClassLike::class) : [];

        foreach ($declarations as $declaration) {
            if ((! isset($declaration->namespacedName)) || (! $this->isName($declaration->namespacedName, $class->getName()))) {
                continue;
            }

            return $this->legacyWidthUsage[$class->getName()] = (bool) $finder->findFirst($declaration->stmts, function (Node $node): bool {
                if ($node instanceof PropertyFetch || $node instanceof MethodCall || $node instanceof NullsafeMethodCall) {
                    return $node->var instanceof Variable && ($node->var->name === 'this')
                        && $this->isName($node->name, $node instanceof PropertyFetch ? 'maxWidth' : 'getMaxWidth');
                }

                return $node instanceof StaticCall && $node->class instanceof Name
                    && $this->isNames($node->class, ['parent', 'self', 'static']) && $this->isName($node->name, 'getMaxWidth');
            });
        }

        return $this->legacyWidthUsage[$class->getName()] = false;
    }
}
