<?php

namespace Filament\Upgrade\Rector;

use Filament\Upgrade\Support\TraitConsumerResolver;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\Coalesce;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PhpParser\Node\Expr\Ternary;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\NullableType;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\Stmt\TraitUse;
use PhpParser\Node\VarLikeIdentifier;
use PhpParser\NodeFinder;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\ObjectType;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\PhpParser\AstResolver;
use Rector\PHPStan\ScopeFetcher;
use Rector\Rector\AbstractRector;

class MigrateLegacyConfigurationHooksRector extends AbstractRector
{
    /** @var list<class-string> */
    private const FRAMEWORK_HOOK_OWNERS = [
        'Filament\\Pages\\Page', 'Filament\\Resources\\Resource',
        'Filament\\Widgets\\StatsOverviewWidget', 'Filament\\Resources\\Pages\\CreateRecord',
        'Filament\\Resources\\Pages\\EditRecord', 'Filament\\Pages\\SettingsPage',
        'Filament\\Auth\\Pages\\EditProfile',
    ];

    public function __construct(
        private AstResolver $astResolver,
        private TraitConsumerResolver $traitConsumerResolver,
    ) {}

    public function getNodeTypes(): array
    {
        return [Class_::class, TraitUse::class];
    }

    /**
     * @param  Class_ | TraitUse  $node
     */
    public function refactor(Node $node): ?Node
    {
        if ($node instanceof TraitUse) {
            return $this->replaceActionInfolistTrait($node);
        }

        if ((! $node->extends) || (! $node->name)) {
            return null;
        }

        $isResource = $this->isObjectType($node, new ObjectType('Filament\\Resources\\Resource'));
        $isStats = $this->isObjectType($node, new ObjectType('Filament\\Widgets\\StatsOverviewWidget'));
        $isPage = $this->isObjectType($node, new ObjectType('Filament\\Pages\\Page'));
        $isWidget = $this->isObjectType($node, new ObjectType('Filament\\Widgets\\Widget'));

        if ((! $isResource) && (! $isPage) && (! $isWidget)) {
            return null;
        }

        $changed = false;

        if ($node->getTraitUses() || ($isWidget && ScopeFetcher::fetch($node)->getClassReflection()?->hasTraitUse('Filament\\Widgets\\Concerns\\InteractsWithPageFilters'))) {
            foreach ($node->getTraitUses() as $traitUse) {
                foreach ($traitUse->traits as $trait) {
                    if ((! $isWidget) || $traitUse->adaptations || (! $this->isName($trait, 'Filament\\Widgets\\Concerns\\InteractsWithPageFilters'))) {
                        return null;
                    }
                }
            }

            $changed = $this->migrateWidgetFilterReads($node) !== null;
        }

        if ($isResource) {
            return $this->migrateResourceLabels($node);
        }

        $hooks = $isStats ? ['getCards' => ['getStats', null]] : [];

        if ($isPage) {
            $hooks['getActions'] = ['getHeaderActions', null];
        }

        if ($this->isObjectType($node, new ObjectType('Filament\\Resources\\Pages\\CreateRecord'))) {
            $hooks['getCreatedNotificationMessage'] = ['getCreatedNotificationTitle', 'filament-panels::resources/pages/create-record.notifications.created.title'];
        } elseif ($this->isObjectType($node, new ObjectType('Filament\\Resources\\Pages\\EditRecord'))) {
            $hooks['getSavedNotificationMessage'] = ['getSavedNotificationTitle', 'filament-panels::resources/pages/edit-record.notifications.saved.title'];
        } elseif ($this->isObjectType($node, new ObjectType('Filament\\Pages\\SettingsPage'))) {
            $hooks['getSavedNotificationMessage'] = ['getSavedNotificationTitle', 'filament-spatie-laravel-settings-plugin::pages/settings-page.notifications.saved.title'];
        } elseif ($this->isObjectType($node, new ObjectType('Filament\\Auth\\Pages\\EditProfile'))) {
            $hooks['backAction'] = ['getCancelFormAction', null];
        }

        foreach ($hooks as $oldName => [$newName, $translation]) {
            $method = $node->getMethod($oldName);

            if ((! $method) || (! $this->canMigrateHierarchy($node, $oldName, $newName, $translation !== null, returnType: $oldName === 'backAction' ? 'Filament\\Actions\\Action' : 'array'))) {
                continue;
            }

            $hasApplicationParent = $this->getApplicationParentHookOwner(ScopeFetcher::fetch($node)->getClassReflection(), $oldName) !== null;

            if ($hasApplicationParent && ($translation !== null)) {
                continue;
            }

            // Only framework defaults are empty. Application parents retain their logic.
            $this->traverseNodesWithCallable($method->stmts ?? [], function (Node $candidate) use ($oldName, $newName, $translation, $hasApplicationParent): ?Node {
                if ($candidate instanceof StaticCall && $candidate->class instanceof Name && $this->isName($candidate->class, 'parent') && $this->isName($candidate->name, $oldName)) {
                    if ($hasApplicationParent || ($oldName === 'backAction')) {
                        return new StaticCall(new Name('parent'), $newName);
                    }

                    return $translation === null ? new Array_ : new ConstFetch(new Name('null'));
                }

                return null;
            });

            $value = null;

            // Keep raw virtual dispatch inside application hierarchies: a child
            // can inspect `null` before the framework applies its default.
            if (($translation !== null) && $this->hasLegacyChildHook($node, $oldName)) {
                $node->stmts[] = clone $method;
                $value = new MethodCall(new Variable('this'), $oldName);
            }

            if ($translation !== null) {
                // Finish the original body (including local destruction and
                // finally blocks) before resolving its translated fallback.
                $method->stmts = [new Return_(new Coalesce(
                    $value ?? $this->wrapBody($method),
                    new FuncCall(new FullyQualified('__'), [new Arg(new String_($translation))]),
                ))];

                if ($this->isObjectType($node, new ObjectType('Filament\\Pages\\SettingsPage'))) {
                    $method->flags = ($method->flags & ~Class_::VISIBILITY_MODIFIER_MASK) | Class_::MODIFIER_PUBLIC;
                    $method->attrGroups[] = new AttributeGroup([new Attribute(new FullyQualified('Filament\\Support\\Components\\Attributes\\Unexposed'))]);
                }
            }

            if (($oldName === 'backAction') && $method->isPublic()) {
                $method->attrGroups[] = new AttributeGroup([new Attribute(new FullyQualified('Filament\\Support\\Components\\Attributes\\Unexposed'))]);
            }

            $method->name = new Identifier($newName);
            $changed = true;
        }

        return $changed ? $node : null;
    }

    private function getApplicationParentHookOwner(?ClassReflection $class, string $oldName): ?ClassReflection
    {
        $parent = $class?->getParentClass();

        if ((! $parent) || (! $parent->hasNativeMethod($oldName))) {
            return null;
        }

        $owner = $parent->getNativeMethod($oldName)->getDeclaringClass();

        return in_array($owner->getName(), self::FRAMEWORK_HOOK_OWNERS, true) ? null : $owner;
    }

    private function canMigrateHierarchy(Class_ $class, string $oldName, string $newName, bool $nullable, bool $static = false, string $returnType = 'array'): bool
    {
        $context = ScopeFetcher::fetch($class)->getClassReflection();

        if (! $context) {
            return false;
        }

        $root = $context;

        while ($parent = $this->getApplicationParentHookOwner($root, $oldName)) {
            $root = $parent;
        }

        $classes = $this->traitConsumerResolver->getSourceClasses($this->getFile()->getFilePath());

        if (! isset($classes[$root->getName()])) {
            return false;
        }

        foreach ($classes as $candidate) {
            if ($candidate->isTrait() || (! (new ObjectType($root->getName()))->isSuperTypeOf(new ObjectType($candidate->getName()))->yes())) {
                continue;
            }

            // A custom canonical hook may already bypass the legacy override.
            if ($candidate->hasNativeMethod($newName) && (! in_array($candidate->getNativeMethod($newName)->getDeclaringClass()->getName(), self::FRAMEWORK_HOOK_OWNERS, true))) {
                return false;
            }

            if ($candidate->hasNativeMethod($oldName)) {
                $reflection = $candidate->getNativeMethod($oldName);

                if (method_exists($reflection, 'getDeclaringTrait') && ($trait = $reflection->getDeclaringTrait()) && ($trait->getName() !== 'Filament\\Pages\\Concerns\\InteractsWithHeaderActions')) {
                    return false;
                }
            }

            $declaration = $this->astResolver->resolveClassFromClassReflection($candidate);

            // Inherited calls and configuration constrain the whole hierarchy too.
            if ((! $declaration instanceof Class_) || (! $this->canMigrateHookReferences($declaration, $oldName, $newName))) {
                return false;
            }

            foreach ($declaration->getTraitUses() as $traitUse) {
                foreach ($traitUse->traits as $trait) {
                    if ($traitUse->adaptations || (! $this->isObjectType($declaration, new ObjectType('Filament\\Widgets\\Widget'))) || (! $this->isName($trait, 'Filament\\Widgets\\Concerns\\InteractsWithPageFilters'))) {
                        return false;
                    }
                }
            }

            if ($static && array_filter(['label', 'pluralLabel', 'modelLabel', 'pluralModelLabel'], static fn (string $property): bool => $declaration->getProperty($property) !== null)) {
                return false;
            }

            $method = $declaration->getMethod($oldName);

            // Never migrate only half a hierarchy when a related override needs review.
            if ($method && ($declaration->getMethod($newName) || (! $this->canMoveMethod($method, $nullable, $static, $returnType)))) {
                return false;
            }
        }

        return true;
    }

    private function hasLegacyChildHook(Class_ $class, string $oldName): bool
    {
        $context = ScopeFetcher::fetch($class)->getClassReflection();

        foreach ($this->traitConsumerResolver->getSourceClasses($this->getFile()->getFilePath()) as $candidate) {
            if (($candidate->getName() === $context?->getName()) || (! (new ObjectType($context->getName()))->isSuperTypeOf(new ObjectType($candidate->getName()))->yes())) {
                continue;
            }

            $method = $this->astResolver->resolveClassFromClassReflection($candidate)?->getMethod($oldName);

            if ($method) {
                return true;
            }
        }

        return false;
    }

    private function canMoveMethod(ClassMethod $method, bool $nullable, bool $static = false, string $returnType = 'array'): bool
    {
        if (($method->isStatic() !== $static) || $method->isPrivate() || $method->params || $method->byRef || $method->attrGroups || (! $method->stmts)) {
            return false;
        }

        if ($nullable ? ((! $this->isName($method->returnType, 'string')) && ((! $method->returnType instanceof NullableType) || (! $this->isName($method->returnType->type, 'string')))) : (! $this->isName($method->returnType, $returnType))) {
            return false;
        }

        return ! (new NodeFinder)->findFirst($method->stmts ?? [], static function (Node $candidate) use ($nullable): bool {
            return $candidate instanceof ClassLike || $candidate instanceof Expr\Yield_ || $candidate instanceof Expr\YieldFrom || $candidate instanceof Node\Scalar\MagicConst\Method || $candidate instanceof Node\Scalar\MagicConst\Function_ || ($nullable && ($candidate instanceof Node\Stmt\Static_));
        });
    }

    private function canMigrateHookReferences(Class_ $class, string $oldName, string $newName): bool
    {
        $hookStaticCalls = (new NodeFinder)->findInstanceOf($class->getMethod($oldName)->stmts ?? [], StaticCall::class);

        return ! (new NodeFinder)->findFirst($class->stmts, function (Node $candidate) use ($oldName, $newName, $hookStaticCalls): bool {
            if ($candidate instanceof ClassLike) {
                return true;
            }

            if ($candidate instanceof Return_ && (! $candidate->expr)) {
                return true;
            }

            if ($candidate instanceof String_ && in_array(strtolower($candidate->value), [strtolower($oldName), strtolower($newName)], true)) {
                return true;
            }

            if ((! $candidate instanceof MethodCall) && (! $candidate instanceof NullsafeMethodCall) && (! $candidate instanceof StaticCall)) {
                return false;
            }

            if (! $candidate->name instanceof Identifier) {
                return true;
            }

            if ($this->isName($candidate->name, $newName)) {
                return true;
            }

            if (! $this->isName($candidate->name, $oldName)) {
                return false;
            }

            return (! $candidate instanceof StaticCall) || (! $candidate->class instanceof Name) || (! $this->isName($candidate->class, 'parent')) || $candidate->isFirstClassCallable() || (bool) $candidate->args || (! in_array($candidate, $hookStaticCalls, true));
        });
    }

    private function migrateResourceLabels(Class_ $class): ?Class_
    {
        $changed = false;

        if ((! $class->getProperty('label')) && (! $class->getProperty('modelLabel')) && (! $class->getProperty('pluralLabel')) && (! $class->getProperty('pluralModelLabel'))) {
            foreach (['getLabel' => 'getModelLabel', 'getPluralLabel' => 'getPluralModelLabel'] as $oldName => $newName) {
                $method = $class->getMethod($oldName);

                if ((! $method) || (! $method->isPublic()) || (! $this->canMigrateHierarchy($class, $oldName, $newName, nullable: true, static: true))) {
                    continue;
                }

                $variableName = 'modelLabel';
                $variables = (new NodeFinder)->findInstanceOf($method->stmts ?? [], Variable::class);

                while (array_filter($variables, static fn (Variable $variable): bool => $variable->name === $variableName)) {
                    $variableName .= 'Value';
                }

                $hasApplicationParent = $this->getApplicationParentHookOwner(ScopeFetcher::fetch($class)->getClassReflection(), $oldName) !== null;

                if ($hasApplicationParent) {
                    continue;
                }

                $this->traverseNodesWithCallable($method->stmts ?? [], function (Node $candidate) use ($oldName): ?Node {
                    if ($candidate instanceof StaticCall && $candidate->class instanceof Name && $this->isName($candidate->class, 'parent') && $this->isName($candidate->name, $oldName)) {
                        return new ConstFetch(new Name('null'));
                    }

                    return null;
                });

                $hasLegacyChildHook = $this->hasLegacyChildHook($class, $oldName);

                if ($hasLegacyChildHook) {
                    $class->stmts[] = clone $method;
                }

                $value = $hasLegacyChildHook ? new StaticCall(new Name('static'), $oldName) : $this->wrapBody($method);
                $fallback = new StaticCall(new Name('parent'), $newName);
                $method->stmts = [new Return_(
                    $oldName === 'getLabel' ? new Coalesce($value, $fallback) : new Ternary(
                        new FuncCall(new FullyQualified('filled'), [new Arg(new Assign(new Variable($variableName), $value))]),
                        new Variable($variableName),
                        $fallback,
                    ),
                )];
                $method->name = new Identifier($newName);
                $method->returnType = new Identifier('string');
                $changed = true;
            }
        }

        foreach (['label' => 'modelLabel', 'pluralLabel' => 'pluralModelLabel'] as $oldName => $newName) {
            $property = $class->getProperty($oldName);

            if ((! $property) || (! $property->isStatic()) || $property->isPrivate() || (count($property->props) !== 1) || $class->getProperty($newName)) {
                continue;
            }

            if (! $this->canMigrateLabelProperty($class, $oldName, $newName)) {
                continue;
            }

            if ((new NodeFinder)->findFirst($class->stmts, function (Node $candidate) use ($oldName, $newName): bool {
                if ($candidate instanceof ClassMethod && $this->isNames($candidate->name, ['getLabel', 'getPluralLabel', 'getModelLabel', 'getPluralModelLabel'])) {
                    return true;
                }

                return $candidate instanceof StaticPropertyFetch && ((! $candidate->name instanceof VarLikeIdentifier) || in_array($candidate->name->toString(), [$oldName, $newName], true));
            })) {
                continue;
            }

            $property->props[0]->name = new VarLikeIdentifier($newName);
            $changed = true;
        }

        return $changed ? $class : null;
    }

    private function canMigrateLabelProperty(Class_ $class, string $oldName, string $newName): bool
    {
        $context = ScopeFetcher::fetch($class)->getClassReflection();
        $root = $context;

        foreach ($context->getParents() as $parent) {
            if ($parent->getName() === 'Filament\\Resources\\Resource') {
                break;
            }

            $root = $parent;
        }

        foreach ($this->traitConsumerResolver->getSourceClasses($this->getFile()->getFilePath()) as $candidate) {
            if (! (new ObjectType($root->getName()))->isSuperTypeOf(new ObjectType($candidate->getName()))->yes()) {
                continue;
            }

            if ($candidate->hasNativeProperty($newName) && ($candidate->getNativeProperty($newName)->getDeclaringClass()->getName() !== 'Filament\\Resources\\Resource')) {
                return false;
            }

            $declaration = $this->astResolver->resolveClassFromClassReflection($candidate);
            $property = $declaration?->getProperty($oldName);

            if ($declaration?->getTraitUses()) {
                return false;
            }

            if ($property && ((! $property->isStatic()) || $property->isPrivate() || (count($property->props) !== 1))) {
                return false;
            }

            if ($declaration && (new NodeFinder)->findFirst($declaration->stmts, function (Node $node) use ($oldName, $newName): bool {
                if ($node instanceof ClassMethod && $this->isNames($node->name, ['getLabel', 'getPluralLabel', 'getModelLabel', 'getPluralModelLabel'])) {
                    return true;
                }

                return $node instanceof StaticPropertyFetch && ((! $node->name instanceof VarLikeIdentifier) || in_array($node->name->toString(), [$oldName, $newName], true));
            })) {
                return false;
            }

            foreach ([$candidate, ...$candidate->getParents()] as $ancestor) {
                foreach (['getLabel', 'getPluralLabel'] as $method) {
                    if ($ancestor->hasNativeMethod($method) && ($ancestor->getNativeMethod($method)->getDeclaringClass()->getName() !== 'Filament\\Resources\\Resource')) {
                        return false;
                    }
                }

                foreach ($ancestor->getTraits(true) as $trait) {
                    if (($trait->getName() !== 'Filament\\Resources\\Resource\\Concerns\\HasLabels') && $trait->hasNativeProperty($oldName)) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    private function wrapBody(ClassMethod $method): FuncCall
    {
        return new FuncCall(new Expr\Closure([
            'static' => $method->isStatic(),
            'returnType' => $method->returnType,
            'stmts' => $method->stmts,
        ]));
    }

    private function migrateWidgetFilterReads(Class_ $class): ?Class_
    {
        $context = ScopeFetcher::fetch($class)->getClassReflection();

        if ($context?->hasNativeProperty('filters')) {
            return null;
        }

        if ($context?->hasNativeMethod('__get')) {
            $method = $context->getNativeMethod('__get');

            if ((! method_exists($method, 'getDeclaringTrait')) || ($method->getDeclaringTrait()?->getName() !== 'Filament\\Widgets\\Concerns\\InteractsWithPageFilters')) {
                return null;
            }
        }

        foreach ($class->getTraitUses() as $traitUse) {
            if ($traitUse->adaptations) {
                return null;
            }

            foreach ($traitUse->traits as $trait) {
                if (! $this->isName($trait, 'Filament\\Widgets\\Concerns\\InteractsWithPageFilters')) {
                    return null;
                }
            }
        }

        if ($class->getProperty('filters') || $class->getProperty('pageFilters') || $class->getMethod('__get') || $class->getMethod('__set')) {
            return null;
        }

        if ((new NodeFinder)->findFirst($class->stmts, function (Node $candidate): bool {
            $targets = match (true) {
                $candidate instanceof Assign, $candidate instanceof Expr\AssignOp, $candidate instanceof Expr\AssignRef => [$candidate->var],
                $candidate instanceof Expr\PreInc, $candidate instanceof Expr\PreDec, $candidate instanceof Expr\PostInc, $candidate instanceof Expr\PostDec => [$candidate->var],
                $candidate instanceof Expr\Isset_, $candidate instanceof Node\Stmt\Unset_ => $candidate->vars,
                $candidate instanceof Node\Stmt\Foreach_ => array_filter([$candidate->keyVar, $candidate->valueVar]),
                $candidate instanceof Arg => [$candidate->value],
                default => [],
            };

            return $candidate instanceof ClassLike || ($targets && (new NodeFinder)->findFirst($targets, static fn (Node $target): bool => $target instanceof PropertyFetch && $target->var instanceof Variable && ($target->var->name === 'this') && $target->name instanceof Identifier && ($target->name->toString() === 'filters')));
        })) {
            return null;
        }

        $reads = [];

        foreach ((new NodeFinder)->findInstanceOf($class->stmts, PropertyFetch::class) as $fetch) {
            if ((! $fetch->var instanceof Variable) || ($fetch->var->name !== 'this')) {
                continue;
            }

            if (! $fetch->name instanceof Identifier) {
                return null;
            }

            if ($fetch->name->toString() !== 'filters') {
                continue;
            }

            foreach ([AttributeKey::IS_BEING_ASSIGNED, AttributeKey::IS_ASSIGN_OP_VAR, AttributeKey::IS_ASSIGN_REF_EXPR, AttributeKey::IS_BYREF_VAR, AttributeKey::IS_INSIDE_BYREF_FUNCTION_LIKE, AttributeKey::IS_UNSET_VAR, AttributeKey::IS_ISSET_VAR, AttributeKey::IS_INCREMENT_OR_DECREMENT] as $attribute) {
                if ($fetch->getAttribute($attribute)) {
                    return null;
                }
            }

            $reads[] = $fetch;
        }

        // Ordinary filter reads most often select a key from the array. Handle
        // these too, but not array writes, references, or property probes.
        if (! $reads) {
            return null;
        }

        foreach ($reads as $fetch) {
            $fetch->name = new Identifier('pageFilters');
        }

        return $class;
    }

    private function replaceActionInfolistTrait(TraitUse $traitUse): ?TraitUse
    {
        if ($traitUse->adaptations) {
            return null;
        }

        foreach ($traitUse->traits as $index => $trait) {
            if (! $this->isName($trait, 'Filament\\Actions\\Concerns\\HasInfolist')) {
                continue;
            }

            $traitUse->traits[$index] = new FullyQualified('Filament\\Actions\\Concerns\\HasSchema');

            return $traitUse;
        }

        return null;
    }
}
