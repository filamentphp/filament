<?php

namespace Filament\Upgrade\Rector;

use PhpParser\Node;
use PhpParser\Node\Arg;
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
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\VarLikeIdentifier;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\NeverType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use Rector\PhpParser\AstResolver;
use Rector\PHPStan\ScopeFetcher;
use Rector\Rector\AbstractRector;

class RenameDeprecatedLazyLoadingApisRector extends AbstractRector
{
    /** @var array<class-string, array<string, string>> */
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

    /** @var array<class-string, array<string, string>> */
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

    public function getNodeTypes(): array
    {
        return [ClassMethod::class, Property::class, MethodCall::class, NullsafeMethodCall::class, StaticCall::class, PropertyFetch::class, NullsafePropertyFetch::class, StaticPropertyFetch::class];
    }

    /** @param ClassMethod | Property | MethodCall | NullsafeMethodCall | StaticCall | PropertyFetch | NullsafePropertyFetch | StaticPropertyFetch $node */
    public function refactor(Node $node): ?Node
    {
        if ($node instanceof ClassMethod || $node instanceof Property) {
            return $this->renameDeclarations($node);
        }

        $isProperty = $node instanceof PropertyFetch || $node instanceof NullsafePropertyFetch || $node instanceof StaticPropertyFetch;

        if (! $node->name instanceof Identifier) {
            return null;
        }

        $oldName = $isProperty ? $node->name->toString() : strtolower($node->name->toString());

        if (! array_filter($isProperty ? self::PROPERTIES : self::METHODS, static fn (array $renames): bool => isset($renames[$oldName]))) {
            return null;
        }

        $receiver = ($node instanceof StaticCall || $node instanceof StaticPropertyFetch) ? $node->class : $node->var;
        $receiverType = TypeCombinator::removeNull($this->getType($receiver));

        // PHPStan cannot infer a removed setter's result in a nullsafe chain.
        if (($receiver instanceof MethodCall || $receiver instanceof NullsafeMethodCall)
            && $receiver->name instanceof Identifier
            && in_array(strtolower($receiver->name->toString()), ['lazy', 'deferloading', 'deferbadge'], true)) {
            $fluentType = TypeCombinator::removeNull($this->getType($receiver->var));

            if ($this->replacement($fluentType, strtolower($receiver->name->toString()), false)) {
                $receiverType = $fluentType;
            }
        }

        if ($receiver instanceof Name && in_array(strtolower($receiver->toString()), ['parent', 'self', 'static'])) {
            $context = ScopeFetcher::fetch($node)->getClassReflection();
            $receiverClass = strtolower($receiver->toString()) === 'parent' ? $context?->getParentClass() : $context;

            if (! $receiverClass) {
                return null;
            }

            $receiverType = new ObjectType($receiverClass->getName());
        }

        $newName = $this->replacement($receiverType, $oldName, $isProperty);

        if (! $newName) {
            return null;
        }

        foreach ($receiverType->getObjectClassReflections() as $classReflection) {
            if (! $this->canRename($classReflection, $oldName, $newName, $isProperty)) {
                return null;
            }
        }

        if ((! $isProperty) && ($newName === 'live')) {
            // Unusual argument-bearing calls and callable integrations need manual migration.
            if ($node->isFirstClassCallable() || $node->args) {
                return null;
            }

            $node->args = [new Arg(new ConstFetch(new Name('true')), name: new Identifier('onBlur'))];
        }

        $node->name = $node instanceof StaticPropertyFetch ? new VarLikeIdentifier($newName) : new Identifier($newName);

        return $node;
    }

    private function renameDeclarations(ClassMethod | Property $node): ?Node
    {
        $classReflection = ScopeFetcher::fetch($node)->getClassReflection();

        if ((! $classReflection) || $classReflection->isTrait()) {
            return null;
        }

        $isProperty = $node instanceof Property;

        if ($isProperty && (count($node->props) !== 1)) {
            return null;
        }

        $oldName = $isProperty ? $node->props[0]->name->toString() : strtolower($node->name->toString());
        $newName = $this->replacement(new ObjectType($classReflection->getName()), $oldName, $isProperty);

        if ((! $newName) || ($newName === 'live') || (! $this->canRename($classReflection, $oldName, $newName, $isProperty))) {
            return null;
        }

        if ($isProperty) {
            $node->props[0]->name = new VarLikeIdentifier($newName);

            if ($node->isStatic() && in_array($oldName, ['isLazy', 'isBadgeDeferred'], true)) {
                $node->type = new NullableType(new Identifier('bool'));
            }
        } else {
            $node->name = new Identifier($newName);
        }

        return $node;
    }

    private function replacement(Type $type, string $oldName, bool $isProperty): ?string
    {
        $newName = null;
        $supportedType = new NeverType;

        foreach ($type->getObjectClassReflections() as $classReflection) {
            $replacement = null;

            foreach ($isProperty ? self::PROPERTIES : self::METHODS as $className => $renames) {
                if (isset($renames[$oldName]) && (new ObjectType($className))->isSuperTypeOf(new ObjectType($classReflection->getName()))->yes()) {
                    $replacement = $renames[$oldName];

                    break;
                }
            }

            if ((! $replacement) || (($newName !== null) && ($newName !== $replacement))) {
                return null;
            }

            $newName = $replacement;
            $supportedType = TypeCombinator::union($supportedType, new ObjectType($classReflection->getName()));
        }

        return $supportedType->isSuperTypeOf($type)->yes() ? $newName : null;
    }

    private function canRename(ClassReflection $classReflection, string $oldName, string $newName, bool $isProperty): bool
    {
        foreach ([$oldName, $newName] as $name) {
            if ($isProperty ? (! $classReflection->hasNativeProperty($name)) : (! $classReflection->hasNativeMethod($name))) {
                continue;
            }

            $member = $isProperty ? $classReflection->getNativeProperty($name) : $classReflection->getNativeMethod($name);
            $owner = $member->getDeclaringClass();

            if (isset(self::METHODS[$owner->getName()]) || in_array($owner->getName(), [
                'Filament\\Support\\Concerns\\CanBeLazy',
                'Filament\\Schemas\\Concerns\\HasStateBindingModifiers',
                'Filament\\Tables\\Table\\Concerns\\CanBeLazyLoaded',
                'Filament\\Tables\\Table\\Concerns\\CanDeferLoading',
                'Filament\\Widgets\\AccountWidget',
                'Filament\\Widgets\\FilamentInfoWidget',
            ], true)) {
                continue;
            }

            if (($name === $newName) || ($newName === 'live') || ($owner->getFileName() !== $this->getFile()->getFilePath())) {
                return false;
            }

            $declaration = $this->astResolver->resolveClassFromClassReflection($owner);

            if ((! $declaration instanceof Class_) || ($isProperty && ($property = $declaration->getProperty($oldName)) && (count($property->props) !== 1))) {
                return false;
            }
        }

        return true;
    }
}
