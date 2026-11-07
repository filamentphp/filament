<?php

namespace Filament\Upgrade\Rector;

use Filament\Auth\MultiFactor\Email\Concerns\InteractsWithEmailAuthentication;
use Filament\Auth\MultiFactor\Email\Contracts\HasEmailAuthentication;
use Filament\Forms\Commands\FileGenerators\RichContentCustomBlockClassGenerator;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Concerns\CanBeLazyLoaded;
use Filament\Support\Contracts\TranslatableContentDriver;
use Filament\Support\Exceptions\Cancel;
use Filament\Support\Exceptions\Halt;
use Filament\Upgrade\Support\TraitConsumerResolver;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersForm;
use Filament\Widgets\Widget;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\TraitUse;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use Rector\PhpParser\AstResolver;
use Rector\PHPStan\ScopeFetcher;
use Rector\Rector\AbstractRector;

class RenameReconciledContractMethodsRector extends AbstractRector
{
    /**
     * @var array<string, array{string, array<class-string>}>
     */
    private const METHODS = [
        'filtersschema' => ['filtersForm', [ChartWidget::class]],
        'getfiltersschema' => ['getFiltersForm', [ChartWidget::class]],
        'toggleemailauthentication' => ['saveEmailAuthentication', [HasEmailAuthentication::class]],
        'getrecordattributestoarray' => ['recordAttributesToArray', [TranslatableContentDriver::class]],
        'shouldrollbackdatabasetransaction' => ['shouldRollBackDatabaseTransaction', [Halt::class, Cancel::class]],
        'islazy' => ['isLazyLoaded', [Widget::class, RelationManager::class]],
        'isbadgedeferred' => ['isBadgeLazyLoaded', [RelationManager::class]],
        'configuretopreviewmethod' => ['configureToPreviewHtmlMethod', [RichContentCustomBlockClassGenerator::class]],
    ];

    /**
     * @var array<class-string>
     */
    private const CANONICAL_METHOD_OWNERS = [
        HasFiltersForm::class,
        HasEmailAuthentication::class,
        InteractsWithEmailAuthentication::class,
        TranslatableContentDriver::class,
        Halt::class,
        Cancel::class,
        CanBeLazyLoaded::class,
        RelationManager::class,
        RichContentCustomBlockClassGenerator::class,
    ];

    public function __construct(
        private TraitConsumerResolver $traitConsumerResolver,
        private AstResolver $astResolver,
    ) {}

    public function getNodeTypes(): array
    {
        return [ClassMethod::class, MethodCall::class, NullsafeMethodCall::class, StaticCall::class, TraitUse::class];
    }

    /**
     * @param  ClassMethod | MethodCall | NullsafeMethodCall | StaticCall | TraitUse  $node
     */
    public function refactor(Node $node): ?Node
    {
        if ($node instanceof TraitUse) {
            return $this->renameTraitAdaptations($node);
        }

        $name = $node->name;

        if (! $name instanceof Identifier) {
            return null;
        }

        $oldName = $name->toString();
        $mapping = self::METHODS[strtolower($oldName)] ?? null;

        if ((! $mapping) || ($oldName === $mapping[0])) {
            return null;
        }

        [$newName, $owners] = $mapping;
        $context = ScopeFetcher::fetch($node)->getClassReflection();

        if (($newName === 'configureToPreviewHtmlMethod') && (! $node instanceof ClassMethod)) {
            $receivers = $this->getReceiverType($node)->getObjectClassReflections();
            $hasPublicHook = $receivers && (! array_filter($receivers, static fn (ClassReflection $receiver): bool => (! $receiver->hasNativeMethod($oldName)) || (! $receiver->getNativeMethod($oldName)->isPublic())));

            if (! $hasPublicHook) {
                $contexts = $context?->isTrait() ? $this->traitConsumerResolver->getConsumers($context, $this->getFile()->getFilePath()) : [$context];

                if (! $contexts) {
                    return null;
                }

                foreach ($contexts as $scopeClass) {
                    if ((! $scopeClass) || (! $this->supportsType(new ObjectType($scopeClass->getName()), $owners))) {
                        return null;
                    }
                }
            }
        }

        $isContextualHookCall = in_array($newName, ['isLazyLoaded', 'isBadgeLazyLoaded', 'configureToPreviewHtmlMethod'], true)
            && $context && (! $node instanceof ClassMethod)
            && ((($node instanceof StaticCall) && ($node->class instanceof Name) && $this->isNames($node->class, ['parent', 'self', 'static']))
                || ((! $node instanceof StaticCall) && ($node->var instanceof Variable) && ($node->var->name === 'this')));

        if ($node instanceof ClassMethod) {
            if ((! $context) || (! $this->supportsClass($context, $owners, $oldName, $newName))) {
                return null;
            }
        } elseif ($isContextualHookCall || ($context?->isTrait() && (
            (($node instanceof StaticCall) && ($node->class instanceof Name) && $this->isNames($node->class, ['self', 'static', 'parent']))
            || ((! $node instanceof StaticCall) && ($node->var instanceof Variable) && ($node->var->name === 'this'))
        ))) {
            if (! $this->supportsClass($context, $owners, $oldName, $newName)) {
                return null;
            }
        } else {
            $receiverType = $this->getReceiverType($node);

            if (! $this->supportsType($receiverType, $owners)) {
                return null;
            }

            foreach ($receiverType->getObjectClassReflections() as $receiver) {
                // A literal framework call cannot dispatch to application subclasses.
                if (($node instanceof StaticCall) && ($node->class instanceof Name)
                    && in_array($newName, ['isLazyLoaded', 'isBadgeLazyLoaded'], true)
                    && in_array($receiver->getName(), $owners, true)) {
                    continue;
                }

                if (! $this->supportsClass($receiver, $owners, $oldName, $newName)) {
                    return null;
                }
            }
        }

        $node->name = new Identifier($newName);

        return $node;
    }

    private function getReceiverType(MethodCall | NullsafeMethodCall | StaticCall $node): Type
    {
        if (($node instanceof StaticCall) && ($node->class instanceof Name) && $this->isNames($node->class, ['parent', 'self', 'static'])) {
            $context = ScopeFetcher::fetch($node)->getClassReflection();
            $receiver = $this->isName($node->class, 'parent') ? $context?->getParentClass() : $context;

            if ($receiver) {
                return new ObjectType($receiver->getName());
            }
        }

        $type = TypeCombinator::removeNull($this->getType($node instanceof StaticCall ? $node->class : $node->var));

        return (($node instanceof StaticCall) && $type->isClassString()->yes()) ? $type->getClassStringObjectType() : $type;
    }

    /**
     * @param  array<class-string>  $owners
     */
    private function supportsType(Type $type, array $owners): bool
    {
        return TypeCombinator::union(...array_map(static fn (string $owner): ObjectType => new ObjectType($owner), $owners))
            ->isSuperTypeOf($type)->yes();
    }

    /**
     * @param  array<class-string>  $owners
     */
    private function supportsClass(ClassReflection $class, array $owners, string $oldName, string $newName): bool
    {
        if ($this->hasConflictingDeclaration($class, $owners, $oldName, $newName)) {
            return false;
        }

        if ($class->isTrait()) {
            $consumers = $this->traitConsumerResolver->getConsumers($class, $this->getFile()->getFilePath());

            if (! $consumers) {
                return false;
            }

            foreach ($consumers as $consumer) {
                if ((! $this->supportsType(new ObjectType($consumer->getName()), $owners)) || $this->hasConflictingDeclaration($consumer, $owners, $oldName, $newName)) {
                    return false;
                }
            }

            return true;
        }

        if (! $this->supportsType(new ObjectType($class->getName()), $owners)) {
            return false;
        }

        if ($class->hasNativeMethod($oldName)) {
            $method = $class->getNativeMethod($oldName);
            $trait = method_exists($method, 'getDeclaringTrait') ? $method->getDeclaringTrait() : null;

            if ($trait && ((! in_array($newName, ['isLazyLoaded', 'isBadgeLazyLoaded', 'configureToPreviewHtmlMethod'], true)) || (! in_array($trait->getName(), self::CANONICAL_METHOD_OWNERS, true)))) {
                return $this->supportsClass($trait, $owners, $oldName, $newName);
            }
        }

        return true;
    }

    /**
     * @param  array<class-string>  $owners
     */
    private function hasConflictingDeclaration(ClassReflection $class, array $owners, string $oldName, string $newName): bool
    {
        // PHP treats a case-only rename as the same method, not a competing hook.
        if (strcasecmp($oldName, $newName) === 0) {
            return false;
        }

        $isOverrideHook = in_array($newName, ['isLazyLoaded', 'isBadgeLazyLoaded', 'configureToPreviewHtmlMethod'], true);
        $classes = $isOverrideHook
            ? $this->traitConsumerResolver->getRelatedClasses($class, $oldName, $this->getFile()->getFilePath())
            : [$class->getName() => $class, ...$this->traitConsumerResolver->getSourceClasses($this->getFile()->getFilePath())];

        foreach ($classes as $candidate) {
            if ((! $isOverrideHook) && (! $candidate->hasNativeMethod($oldName))) {
                continue;
            }

            if (! $this->supportsType(new ObjectType($candidate->getName()), $owners)) {
                if ($isOverrideHook) {
                    return true;
                }

                continue;
            }

            if ($isOverrideHook && $candidate->hasNativeMethod($oldName)) {
                $legacyMethod = $candidate->getNativeMethod($oldName);
                $legacyOwner = (method_exists($legacyMethod, 'getDeclaringTrait') ? $legacyMethod->getDeclaringTrait() : null) ?? $legacyMethod->getDeclaringClass();

                if ((! in_array($legacyOwner->getName(), self::CANONICAL_METHOD_OWNERS, true))
                    && (! isset($this->traitConsumerResolver->getSourceClasses($this->getFile()->getFilePath())[$legacyOwner->getName()]))) {
                    return true;
                }
            }

            if (in_array($newName, ['isLazyLoaded', 'isBadgeLazyLoaded'], true)) {
                if ($candidate->hasNativeProperty($newName)
                    && (! in_array($candidate->getNativeProperty($newName)->getDeclaringClass()->getName(), [Widget::class, RelationManager::class], true))) {
                    return true;
                }

                $oldPropertyName = ($newName === 'isLazyLoaded') ? 'isLazy' : 'isBadgeDeferred';

                foreach ([$candidate, ...$candidate->getParents()] as $ancestor) {
                    foreach ($ancestor->getTraits(true) as $trait) {
                        if ($trait->hasNativeProperty($oldPropertyName) && (! in_array($trait->getName(), [CanBeLazyLoaded::class, 'Filament\\Support\\Concerns\\CanBeLazy'], true))) {
                            return true;
                        }
                    }
                }

                if ($candidate->hasNativeProperty($oldPropertyName)) {
                    $propertyOwner = $candidate->getNativeProperty($oldPropertyName)->getDeclaringClass();

                    if (! in_array($propertyOwner->getName(), [Widget::class, RelationManager::class, CanBeLazyLoaded::class], true)) {
                        $declaration = $this->astResolver->resolveClassFromClassReflection($propertyOwner);

                        if ((! isset($this->traitConsumerResolver->getSourceClasses($this->getFile()->getFilePath())[$propertyOwner->getName()]))
                            || (($property = $declaration?->getProperty($oldPropertyName)) && (count($property->props) !== 1))) {
                            return true;
                        }
                    }
                }
            }

            if (! $candidate->hasNativeMethod($newName)) {
                continue;
            }

            $canonicalMethod = $candidate->getNativeMethod($newName);
            $canonicalTrait = method_exists($canonicalMethod, 'getDeclaringTrait') ? $canonicalMethod->getDeclaringTrait() : null;
            $canonicalOwner = $canonicalTrait ?? $canonicalMethod->getDeclaringClass();

            // An inherited Filament replacement is expected; an application replacement
            // competes with the old hook even when the two declarations are in different classes.
            if (in_array($canonicalOwner->getName(), self::CANONICAL_METHOD_OWNERS, true)) {
                continue;
            }

            // Contract-typed calls can reach unrelated implementations too. Keep every
            // implementation and caller of this contract together for manual reconciliation.
            return true;
        }

        return false;
    }

    private function renameTraitAdaptations(TraitUse $node): ?TraitUse
    {
        $context = ScopeFetcher::fetch($node)->getClassReflection();

        if (! $context) {
            return null;
        }

        $changed = false;

        foreach ($node->adaptations as $adaptation) {
            $oldName = $adaptation->method->toString();
            $mapping = self::METHODS[strtolower($oldName)] ?? null;

            if ($mapping && ($oldName !== $mapping[0]) && $this->supportsClass($context, $mapping[1], $oldName, $mapping[0])) {
                $adaptation->method = new Identifier($mapping[0]);
                $changed = true;
            }
        }

        return $changed ? $node : null;
    }
}
