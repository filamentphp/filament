<?php

namespace Filament\Upgrade\Rector;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\ArrayItem;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\AssignOp;
use PhpParser\Node\Expr\AssignRef;
use PhpParser\Node\Expr\BinaryOp\Coalesce;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\NullsafePropertyFetch;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\UnionType;
use PhpParser\Node\VarLikeIdentifier;
use PhpParser\NodeFinder;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\NeverType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\PhpParser\AstResolver;
use Rector\PHPStan\ScopeFetcher;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class RenameDeprecatedLazyLoadingApisRector extends AbstractRector
{
    /**
     * @var array<class-string, array<string, string>>
     */
    private const METHODS = [
        'Filament\\Schemas\\Components\\Livewire' => ['lazy' => 'lazyLoaded', 'islazy' => 'isLazyLoaded'],
        'Filament\\Forms\\Components\\LivewireField' => ['lazy' => 'lazyLoaded', 'islazy' => 'isLazyLoaded'],
        'Filament\\Schemas\\Schema' => ['deferloading' => 'lazyLoaded', 'isloadingdeferred' => 'isLazyLoaded', 'lazy' => 'live', 'islazy' => 'isLiveOnBlur'],
        'Filament\\Tables\\Table' => ['deferloading' => 'lazyLoaded', 'isloadingdeferred' => 'isLazyLoaded'],
        'Filament\\Resources\\RelationManagers\\RelationGroup' => ['deferbadge' => 'lazyLoadedBadge', 'isbadgedeferred' => 'isBadgeLazyLoaded'],
        'Filament\\Schemas\\Components\\Tabs\\Tab' => ['deferbadge' => 'lazyLoadedBadge', 'isbadgedeferred' => 'isBadgeLazyLoaded'],
        'Filament\\Resources\\RelationManagers\\RelationManager' => ['islazy' => 'isLazyLoaded', 'isbadgedeferred' => 'isBadgeLazyLoaded'],
        'Filament\\Widgets\\Widget' => ['islazy' => 'isLazyLoaded'],
        'Filament\\Schemas\\Components\\Tabs' => ['getdeferredtabbadges' => 'getLazyLoadedTabBadges', 'hasdeferredbadges' => 'hasLazyLoadedBadges'],
        'Filament\\Schemas\\Components\\Component' => ['lazy' => 'live', 'islazy' => 'isLiveOnBlur'],
    ];

    /**
     * @var array<class-string, array<string, string>>
     */
    private const PROPERTIES = [
        'Filament\\Schemas\\Schema' => ['isLoadingDeferred' => 'isLazyLoaded'],
        'Filament\\Tables\\Table' => ['isLoadingDeferred' => 'isLazyLoaded'],
        'Filament\\Schemas\\Components\\Livewire' => ['isLazy' => 'isLazyLoaded'],
        'Filament\\Forms\\Components\\LivewireField' => ['isLazy' => 'isLazyLoaded'],
        'Filament\\Resources\\RelationManagers\\RelationGroup' => ['isBadgeDeferred' => 'isBadgeLazyLoaded'],
        'Filament\\Schemas\\Components\\Tabs\\Tab' => ['isBadgeDeferred' => 'isBadgeLazyLoaded'],
        'Filament\\Resources\\RelationManagers\\RelationManager' => ['isLazy' => 'isLazyLoaded', 'isBadgeDeferred' => 'isBadgeLazyLoaded'],
        'Filament\\Widgets\\Widget' => ['isLazy' => 'isLazyLoaded'],
    ];

    public function __construct(private AstResolver $astResolver) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Migrate deprecated Filament lazy loading and blur binding APIs', [
            new CodeSample('$table->deferLoading();', '$table->lazyLoaded();'),
            new CodeSample('$field->lazy();', '$field->live(onBlur: true);'),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [ClassMethod::class, Property::class, MethodCall::class, NullsafeMethodCall::class, StaticCall::class, PropertyFetch::class, NullsafePropertyFetch::class, StaticPropertyFetch::class];
    }

    /**
     * @param  ClassMethod | Property | MethodCall | NullsafeMethodCall | StaticCall | PropertyFetch | NullsafePropertyFetch | StaticPropertyFetch  $node
     */
    public function refactor(Node $node): ?Node
    {
        if ($node instanceof ClassMethod || $node instanceof Property) {
            return $this->refactorDeclaration($node);
        }

        $isProperty = $node instanceof PropertyFetch || $node instanceof NullsafePropertyFetch || $node instanceof StaticPropertyFetch;

        if ($node instanceof StaticPropertyFetch && $this->isUnsafePropertyContext($node)) {
            return null;
        }

        if (! $node->name instanceof Identifier) {
            return null;
        }

        $oldName = $isProperty ? $node->name->toString() : strtolower($node->name->toString());

        // Most application calls are unrelated. Avoid resolving their receiver
        // types when no supported owner renames this member in the first place.
        if (! array_filter($isProperty ? self::PROPERTIES : self::METHODS, static fn (array $renames): bool => isset($renames[$oldName]))) {
            return null;
        }

        if ($node instanceof StaticPropertyFetch && in_array($oldName, ['isLazy', 'isBadgeDeferred'], true) && $this->hasUnsafeStaticPropertyUse($oldName, $this->getFile()->getNewStmts())) {
            return null;
        }

        $receiver = ($node instanceof StaticCall || $node instanceof StaticPropertyFetch) ? $node->class : $node->var;
        $receiverType = TypeCombinator::removeNull($this->getType($receiver));

        if ($receiver instanceof Name && in_array(strtolower($receiver->toString()), ['parent', 'self', 'static'])) {
            $context = ScopeFetcher::fetch($node)->getClassReflection();
            $receiverClass = strtolower($receiver->toString()) === 'parent' ? $context?->getParentClass() : $context;

            if (! $receiverClass) {
                return null;
            }

            $receiverType = new ObjectType($receiverClass->getName());
        }

        $newName = null;

        foreach ($receiverType->getObjectClassReflections() as $classReflection) {
            $replacement = $this->replacement(new ObjectType($classReflection->getName()), $oldName, $isProperty);

            if ((! $replacement) || (($newName !== null) && ($replacement !== $newName))) {
                return null;
            }

            if (! $this->canRename($classReflection, $oldName, $replacement, $isProperty)) {
                return null;
            }

            if ($node instanceof StaticCall) {
                $methodName = $classReflection->hasNativeMethod($oldName) ? $oldName : $replacement;

                if ($classReflection->hasNativeMethod($methodName) && (! $classReflection->getNativeMethod($methodName)->isStatic())) {
                    $function = ScopeFetcher::fetch($node)->getFunction();

                    if ((! $receiver instanceof Name) || (! in_array(strtolower($receiver->toString()), ['parent', 'self', 'static'])) || (! $function instanceof MethodReflection) || $function->isStatic()) {
                        return null;
                    }
                }
            }

            $newName = $replacement;
        }

        // A mixed or partially unrelated union must not be inferred from its object alternatives alone.
        if ((! $newName) || (! $this->isEntirelySupported($receiverType, $oldName, $newName, $isProperty)) || $this->hasOverridingUnion($oldName, $newName, $isProperty)) {
            return null;
        }

        if ($receiver instanceof Name && in_array(strtolower($receiver->toString()), ['parent', 'self', 'static'])) {
            $context = ScopeFetcher::fetch($node)->getClassReflection();

            if ((! $context) || (! $this->canRename($context, $oldName, $newName, $isProperty))) {
                return null;
            }
        }

        if (($node instanceof MethodCall || $node instanceof NullsafeMethodCall || $node instanceof StaticCall) && ($newName === 'live')) {
            if ($node->isFirstClassCallable() || $node->getArgs()) {
                return null;
            }

            $node->args = [new Arg(new ConstFetch(new Name('true')), name: new Identifier('onBlur'))];
        }

        $node->name = $node instanceof StaticPropertyFetch ? new VarLikeIdentifier($newName) : new Identifier($newName);

        if ($node instanceof StaticPropertyFetch && in_array($oldName, ['isLazy', 'isBadgeDeferred'], true)) {
            return new Coalesce($node, new ConstFetch(new Name($oldName === 'isLazy' ? 'true' : 'false')));
        }

        return $node;
    }

    private function refactorDeclaration(ClassMethod | Property $node): ?Node
    {
        $classReflection = ScopeFetcher::fetch($node)->getClassReflection();
        $isProperty = $node instanceof Property;

        if ((! $classReflection) || $classReflection->isTrait() || ($isProperty && (count($node->props) !== 1))) {
            return null;
        }

        $oldName = $isProperty ? $node->props[0]->name->toString() : strtolower($node->name->toString());
        $newName = $this->replacement(new ObjectType($classReflection->getName()), $oldName, $isProperty);

        if ((! $newName) || (! $this->canRename($classReflection, $oldName, $newName, $isProperty)) || $this->hasOverridingUnion($oldName, $newName, $isProperty)) {
            return null;
        }

        if ($isProperty) {
            $node->props[0]->name = new VarLikeIdentifier($newName);

            if ($node->isStatic()) {
                $node->type = new NullableType(new Identifier('bool'));
            }
        } else {
            $node->name = new Identifier($newName);
        }

        return $node;
    }

    private function replacement(Type $type, string $oldName, bool $isProperty): ?string
    {
        foreach ($isProperty ? self::PROPERTIES : self::METHODS as $className => $renames) {
            if ((new ObjectType($className))->isSuperTypeOf($type)->yes() && isset($renames[$oldName])) {
                return $renames[$oldName];
            }
        }

        return null;
    }

    private function isEntirelySupported(Type $type, string $oldName, string $newName, bool $isProperty): bool
    {
        $supportedType = new NeverType;

        foreach ($type->getObjectClassReflections() as $classReflection) {
            if ($this->replacement(new ObjectType($classReflection->getName()), $oldName, $isProperty) !== $newName) {
                return false;
            }

            $supportedType = TypeCombinator::union($supportedType, new ObjectType($classReflection->getName()));
        }

        return $supportedType->isSuperTypeOf($type)->yes();
    }

    private function hasOverridingUnion(string $oldName, string $newName, bool $isProperty): bool
    {
        // Native unions retain subtype names that PHPStan can normalize away.
        return (new NodeFinder)->findFirst($this->getFile()->getNewStmts(), function (Node $node) use ($oldName, $newName, $isProperty): bool {
            if (! $node instanceof UnionType) {
                return false;
            }

            $types = array_map(fn (Node $type): Type => $this->getType($type), $node->types);

            foreach ($isProperty ? self::PROPERTIES : self::METHODS as $className => $renames) {
                if (($renames[$oldName] ?? null) !== $newName) {
                    continue;
                }

                $ownerType = new ObjectType($className);

                foreach ($types as $type) {
                    if ((! $ownerType->isSuperTypeOf($type)->yes()) || (! array_filter($types, static fn (Type $otherType): bool => (! $otherType->equals($type)) && $otherType->isSuperTypeOf($type)->yes()))) {
                        continue;
                    }

                    foreach ($type->getObjectClassReflections() as $classReflection) {
                        // A migrated getter or setter can use a backing property
                        // implicitly through its parent. Keep the owner's mapped
                        // declarations and references together when any collide.
                        foreach ([false, true] as $memberIsProperty) {
                            $memberRenames = ($memberIsProperty ? self::PROPERTIES : self::METHODS)[$className] ?? [];

                            foreach ($memberRenames as $oldMember => $newMember) {
                                if (! $this->canRename($classReflection, $oldMember, $newMember, $memberIsProperty)) {
                                    return true;
                                }
                            }
                        }
                    }
                }
            }

            return false;
        }) !== null;
    }

    private function canRename(ClassReflection $classReflection, string $oldName, string $newName, bool $isProperty): bool
    {
        if ($isProperty && in_array($oldName, ['isLazy', 'isBadgeDeferred'], true)) {
            $declaration = $this->astResolver->resolveClassFromClassReflection($classReflection);

            if ($this->hasUnsafeStaticPropertyUse($oldName, [...$this->getFile()->getNewStmts(), ...($declaration->stmts ?? [])])) {
                return false;
            }
        }

        foreach ([$oldName, $newName] as $name) {
            $hasMember = $isProperty ? $classReflection->hasNativeProperty($name) : $classReflection->hasNativeMethod($name);

            if (! $hasMember) {
                continue;
            }

            $member = $isProperty ? $classReflection->getNativeProperty($name) : $classReflection->getNativeMethod($name);
            $declaringClass = $member->getDeclaringClass();

            if (isset(self::METHODS[$declaringClass->getName()]) || in_array($declaringClass->getName(), [
                'Filament\\Support\\Concerns\\CanBeLazy',
                'Filament\\Schemas\\Concerns\\HasStateBindingModifiers',
                'Filament\\Tables\\Table\\Concerns\\CanDeferLoading',
            ], true)) {
                continue;
            }

            // Only migrate custom declarations in this file, together with their references.
            if (($name === $newName) || ($declaringClass->getFileName() !== $this->getFile()->getFilePath())) {
                return false;
            }

            $declaration = $this->astResolver->resolveClassFromClassReflection($declaringClass);

            if (! $declaration instanceof Class_) {
                return false;
            }

            if ($isProperty) {
                $property = $declaration->getProperty($oldName);

                if ((! $property) || (count($property->props) !== 1)) {
                    return false;
                }
            } else {
                $method = $declaration->getMethod($oldName);

                // Binding `lazy()` cannot become an override of `live()` with its different signature.
                if ((! $method) || ($newName === 'live')) {
                    return false;
                }

                foreach (['isLazy' => 'isLazyLoaded', 'isBadgeDeferred' => 'isBadgeLazyLoaded'] as $oldProperty => $newProperty) {
                    if ((new NodeFinder)->findFirst($method->stmts ?? [], static fn (Node $node): bool => $node instanceof StaticPropertyFetch && $node->name instanceof VarLikeIdentifier && ($node->name->toString() === $oldProperty)) && (! $this->canRename($classReflection, $oldProperty, $newProperty, true))) {
                        return false;
                    }
                }

                // Explicit new configuration already takes precedence over legacy widget / manager overrides.
                if ($classReflection->hasNativeProperty($newName) && (! isset(self::METHODS[$classReflection->getNativeProperty($newName)->getDeclaringClass()->getName()]))) {
                    return false;
                }

                if ((new NodeFinder)->findFirst($method->stmts ?? [], static fn (Node $node): bool => ($node instanceof MethodCall || $node instanceof NullsafeMethodCall || $node instanceof StaticCall) && $node->name instanceof Identifier && (strtolower($node->name->toString()) === strtolower($newName)))) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param  array<Node>  $nodes
     */
    private function hasUnsafeStaticPropertyUse(string $propertyName, array $nodes): bool
    {
        return (new NodeFinder)->findFirst($nodes, function (Node $node) use ($propertyName): bool {
            $writeTargets = match (true) {
                $node instanceof Assign, $node instanceof AssignOp, $node instanceof AssignRef => [$node->var],
                $node instanceof Foreach_ => array_filter([$node->keyVar, $node->valueVar]),
                $node instanceof ArrayItem && $node->byRef => [$node->value],
                default => [],
            };

            if ($writeTargets && (new NodeFinder)->findFirst($writeTargets, static fn (Node $target): bool => $target instanceof StaticPropertyFetch && $target->name instanceof VarLikeIdentifier && ($target->name->toString() === $propertyName))) {
                return true;
            }

            return $node instanceof StaticPropertyFetch
                && $node->name instanceof VarLikeIdentifier
                && ($node->name->toString() === $propertyName)
                && $this->isUnsafePropertyContext($node);
        }) !== null;
    }

    private function isUnsafePropertyContext(StaticPropertyFetch $node): bool
    {
        foreach ([AttributeKey::IS_BEING_ASSIGNED, AttributeKey::IS_ASSIGN_OP_VAR, AttributeKey::IS_ASSIGN_REF_EXPR, AttributeKey::IS_BYREF_VAR, AttributeKey::IS_INSIDE_BYREF_FUNCTION_LIKE, AttributeKey::IS_ARG_VALUE, AttributeKey::IS_UNSET_VAR, AttributeKey::IS_ISSET_VAR, AttributeKey::IS_INCREMENT_OR_DECREMENT, AttributeKey::INSIDE_ARRAY_DIM_FETCH] as $attribute) {
            if ($node->getAttribute($attribute)) {
                return true;
            }
        }

        return false;
    }
}
