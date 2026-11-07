<?php

namespace Filament\Upgrade\Rector;

use Filament\Commands\FileGenerators\Resources\Concerns\CanGenerateResourceTables;
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
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\Stmt\TraitUse;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\UseItem;
use PhpParser\Node\VarLikeIdentifier;
use PhpParser\NodeFinder;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\ObjectType;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\PhpParser\AstResolver;
use Rector\PhpParser\Node\BetterNodeFinder;
use Rector\PhpParser\Parser\RectorParser;
use Rector\PHPStan\ScopeFetcher;
use Rector\Rector\AbstractRector;

class MigrateLegacyConfigurationHooksRector extends AbstractRector
{
    /** @var list<Use_> */
    private array $traitImports = [];

    private ?string $traitImportFile = null;

    /** @var list<class-string> */
    private const FRAMEWORK_HOOK_OWNERS = [
        'Filament\\Pages\\Page', 'Filament\\Resources\\Resource',
        'Filament\\Widgets\\StatsOverviewWidget', 'Filament\\Resources\\Pages\\CreateRecord',
        'Filament\\Resources\\Pages\\EditRecord', 'Filament\\Pages\\SettingsPage',
        'Filament\\Auth\\Pages\\EditProfile',
        'Filament\\Actions\\Exports\\Exporter',
    ];

    public function __construct(
        private AstResolver $astResolver,
        private BetterNodeFinder $betterNodeFinder,
        private TraitConsumerResolver $traitConsumerResolver,
        private ReflectionProvider $reflectionProvider,
        private RectorParser $rectorParser,
    ) {}

    public function getNodeTypes(): array
    {
        return [Class_::class, TraitUse::class];
    }

    /**
     * @param  Class_ | TraitUse  $node
     * @return Node | list<Node> | null
     */
    public function refactor(Node $node): Node | array | null
    {
        if ($node instanceof TraitUse) {
            return $this->replaceActionInfolistTrait($node);
        }

        if (! $node->name) {
            return null;
        }

        $isResource = $this->isObjectType($node, new ObjectType('Filament\\Resources\\Resource'));
        $isStats = $this->isObjectType($node, new ObjectType('Filament\\Widgets\\StatsOverviewWidget'));
        $isPage = $this->isObjectType($node, new ObjectType('Filament\\Pages\\Page'));
        $isWidget = $this->isObjectType($node, new ObjectType('Filament\\Widgets\\Widget'));
        $isExporter = $this->isObjectType($node, new ObjectType('Filament\\Actions\\Exports\\Exporter'));
        $isGenerator = ScopeFetcher::fetch($node)->getClassReflection()?->hasTraitUse(CanGenerateResourceTables::class) ?? false;

        if ((! $isResource) && (! $isPage) && (! $isWidget) && (! $isExporter) && (! $isGenerator)) {
            return null;
        }

        if ($this->traitImportFile !== $this->getFile()->getFilePath()) {
            $this->traitImportFile = $this->getFile()->getFilePath();
            $this->traitImports = [];
        }

        $importOffset = count($this->traitImports);
        $changed = false;

        if ($isWidget && ScopeFetcher::fetch($node)->getClassReflection()?->hasTraitUse('Filament\\Widgets\\Concerns\\InteractsWithPageFilters')) {
            $changed = $this->migrateWidgetFilterReads($node) !== null;
        }

        if ($isResource) {
            return $this->migrateResourceLabels($node) ? [...array_slice($this->traitImports, $importOffset), $node] : null;
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

        if ($isExporter) {
            $hooks['getFileDisk'] = ['getFileDiskName', null];
        } elseif ($isGenerator) {
            $hooks += [
                'getTableActions' => ['getTableRecordActions', null],
                'outputTableActions' => ['outputTableRecordActions', null],
                'outputTableMethodBulkActions' => ['outputTableBulkActions', null],
            ];
        }

        foreach ($hooks as $oldName => [$newName, $translation]) {
            $context = ScopeFetcher::fetch($node)->getClassReflection();
            $method = $node->getMethod($oldName) ?? $this->getTraitHook($context, $oldName);
            $retainsLegacy = $isExporter || $isGenerator;
            $returnType = match ($oldName) {
                'backAction' => 'Filament\\Actions\\Action',
                'getFileDisk', 'outputTableActions', 'outputTableMethodBulkActions' => 'string',
                default => 'array',
            };

            if ((! $method) || (! $this->canMigrateHierarchy($node, $oldName, $newName, $translation !== null, returnType: $returnType, retainsLegacy: $retainsLegacy))) {
                continue;
            }

            $hasApplicationParent = $this->getApplicationParentHookOwner($context, $oldName) !== null;
            $hasTraitHook = $this->hasTraitHookInHierarchy($context, $oldName);

            if ($hasApplicationParent && (($translation !== null) || $hasTraitHook || $retainsLegacy)) {
                continue;
            }

            // Only framework defaults are empty. Application parents retain their logic.
            $this->traverseNodesWithCallable($retainsLegacy ? [...$node->stmts, $method] : ($method->stmts ?? []), function (Node $candidate) use ($oldName, $newName, $translation, $hasApplicationParent, $retainsLegacy): ?Node {
                if ($candidate instanceof StaticCall && $candidate->class instanceof Name && $this->isName($candidate->class, 'parent') && $this->isName($candidate->name, $oldName)) {
                    if ($hasApplicationParent || ($oldName === 'backAction') || $retainsLegacy) {
                        return new StaticCall(new Name('parent'), $newName);
                    }

                    return $translation === null ? new Array_ : new ConstFetch(new Name('null'));
                }

                return null;
            });

            if ($retainsLegacy) {
                if ($method !== $node->getMethod($oldName)) {
                    $this->aliasTraitFunctionsAndConstants($method, $node);
                    $node->stmts[] = $method;
                }

                $node->stmts[] = new ClassMethod($newName, [
                    'flags' => Class_::MODIFIER_PUBLIC,
                    'returnType' => new Identifier($returnType),
                    'stmts' => [new Return_(new MethodCall(new Variable('this'), $oldName))],
                ]);
                $changed = true;

                continue;
            }

            $value = null;

            // Keep raw virtual dispatch inside application hierarchies: a child
            // can inspect `null` before the framework applies its default.
            if ($hasTraitHook || (($translation !== null) && $this->hasLegacyChildHook($node, $oldName))) {
                if ($method !== $node->getMethod($oldName)) {
                    $this->aliasTraitFunctionsAndConstants($method, $node);
                }

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
            } elseif ($value) {
                $method->stmts = [new Return_($value)];
            }

            if (($oldName === 'backAction') && $method->isPublic()) {
                $method->attrGroups[] = new AttributeGroup([new Attribute(new FullyQualified('Filament\\Support\\Components\\Attributes\\Unexposed'))]);
            }

            $method->name = new Identifier($newName);

            if (! $node->getMethod($newName)) {
                $node->stmts[] = $method;
            }

            $changed = true;
        }

        return $changed ? [...array_slice($this->traitImports, $importOffset), $node] : null;
    }

    private function getApplicationParentHookOwner(?ClassReflection $class, string $oldName): ?ClassReflection
    {
        $parent = $class?->getParentClass();

        if ((! $parent) || (! $parent->hasNativeMethod($oldName))) {
            return null;
        }

        return $this->isFrameworkHook($parent, $oldName) ? null : $parent->getNativeMethod($oldName)->getDeclaringClass();
    }

    private function isFrameworkHook(ClassReflection $class, string $name): bool
    {
        $method = $class->getNativeMethod($name);

        return in_array($method->getDeclaringClass()->getName(), self::FRAMEWORK_HOOK_OWNERS, true)
            || (method_exists($method, 'getDeclaringTrait') && ($method->getDeclaringTrait()?->getName() === CanGenerateResourceTables::class));
    }

    private function getTraitHook(ClassReflection $class, string $oldName): ?ClassMethod
    {
        if ((! $class->hasNativeMethod($oldName)) || ($class->getNativeMethod($oldName)->getDeclaringClass()->getName() !== $class->getName())) {
            return null;
        }

        $reflection = $class->getNativeMethod($oldName);
        $trait = method_exists($reflection, 'getDeclaringTrait') ? $reflection->getDeclaringTrait() : null;

        if ((! $trait) || in_array($trait->getName(), ['Filament\\Pages\\Concerns\\InteractsWithHeaderActions', CanGenerateResourceTables::class], true)) {
            return null;
        }

        if (! $this->canCopyTraitMember($trait, $class)) {
            return null;
        }

        $method = $this->astResolver->resolveClassFromClassReflection($trait)?->getMethod($oldName);

        if ((! $method) || (new NodeFinder)->findInstanceOf([$method], Node\Scalar\MagicConst::class)) {
            return null;
        }

        $copy = $this->copyTraitNode($method);

        return $this->qualifyTraitNames($copy) ? $copy : null;
    }

    private function canCopyTraitMember(ClassReflection $trait, ClassReflection $class): bool
    {
        $traitFile = $trait->getFileName();
        $classFile = $class->getFileName();

        if ((! $traitFile) || (! $classFile)) {
            return false;
        }

        $statements = $this->rectorParser->parseFile($classFile);
        $declarations = [];

        if (count(array_filter($statements, static fn (Node $node): bool => $node instanceof Namespace_)) > 1) {
            return false;
        }

        foreach ($statements as $statement) {
            $declarations = [...$declarations, ...($statement instanceof Namespace_ ? $statement->stmts : [$statement])];
        }

        if (! array_filter($declarations, static fn (Node $node): bool => ($node instanceof Class_) && (($node->namespacedName?->toString() ?? $node->name?->toString()) === $class->getName()))) {
            return false;
        }

        $strictTypes = $this->getStrictTypes($traitFile);

        return ($strictTypes !== null) && ($strictTypes === $this->getStrictTypes($classFile));
    }

    private function getStrictTypes(string $file): ?bool
    {
        $strictTypes = false;

        foreach ((new NodeFinder)->findInstanceOf($this->rectorParser->parseFile($file), Node\Stmt\Declare_::class) as $statement) {
            foreach ($statement->declares as $declare) {
                // Tick events depend on the lexical declaration around each member.
                if (strtolower($declare->key->toString()) === 'ticks') {
                    return null;
                }

                if ((strtolower($declare->key->toString()) === 'strict_types') && ($declare->value instanceof Node\Scalar\Int_)) {
                    $strictTypes = $declare->value->value === 1;
                }
            }
        }

        return $strictTypes;
    }

    private function getTraitLabelProperty(ClassReflection $class, string $oldName): ?Property
    {
        foreach ($this->getLocalTraits($class) as $trait) {
            $property = $this->astResolver->resolveClassFromClassReflection($trait)?->getProperty($oldName);

            if (! $property) {
                continue;
            }

            if ((! $this->canCopyTraitMember($trait, $class)) || (new NodeFinder)->findInstanceOf([$property], Node\Scalar\MagicConst::class)) {
                return null;
            }

            $copy = $this->copyTraitNode($property);

            return $this->qualifyTraitNames($copy) ? $copy : null;
        }

        return null;
    }

    /** @return array<string, ClassReflection> */
    private function getLocalTraits(ClassReflection $class): array
    {
        $traits = $class->getTraits();

        foreach ($traits as $trait) {
            $traits += $trait->getTraits(true);
        }

        return $traits;
    }

    private function qualifyTraitNames(Node $member): bool
    {
        $canCopy = true;

        $this->traverseNodesWithCallable([$member], function (Node $node) use (&$canCopy): ?Node {
            $node->setAttribute(AttributeKey::ORIGINAL_NODE, null);

            if (($node instanceof FuncCall) && ($node->name instanceof Name)) {
                $name = $this->reflectionProvider->resolveFunctionName($node->name, ScopeFetcher::fetch($node));

                if ($name === null) {
                    $canCopy = false;
                } else {
                    $node->name = new FullyQualified($name);
                }
            } elseif (($node instanceof ConstFetch) && (! $this->isNames($node->name, ['null', 'true', 'false']))) {
                $name = $this->reflectionProvider->resolveConstantName($node->name, ScopeFetcher::fetch($node));

                if ($name === null) {
                    $canCopy = false;
                } else {
                    $node->name = new FullyQualified($name);
                }
            } elseif (($node instanceof Name) && (! $this->isNames($node, ['parent', 'self', 'static', 'null', 'true', 'false']))) {
                return new FullyQualified($node instanceof FullyQualified ? $node->toString() : ScopeFetcher::fetch($node)->resolveName($node));
            }

            return null;
        });

        return $canCopy;
    }

    private function aliasTraitFunctionsAndConstants(Node $member, Class_ $consumer): void
    {
        $root = $this->getFile()->getUseImportsRootNode();
        $finder = new NodeFinder;

        foreach ([...$finder->findInstanceOf([$member], FuncCall::class), ...$finder->findInstanceOf([$member], ConstFetch::class)] as $reference) {
            if ((! $reference->name instanceof FullyQualified) || (! str_contains($reference->name->toString(), '\\'))) {
                continue;
            }

            $name = $reference->name->toString();
            $type = $reference instanceof FuncCall ? Use_::TYPE_FUNCTION : Use_::TYPE_CONSTANT;
            $alias = null;

            $precedingStatements = array_filter($root->stmts, static fn (Node $node): bool => $node->getStartFilePos() < $consumer->getStartFilePos());

            foreach ([...$precedingStatements, ...$this->traitImports] as $statement) {
                if ((! $statement instanceof Use_) || ($statement->type !== $type)) {
                    continue;
                }

                foreach ($statement->uses as $use) {
                    if (($use->name->toString() === $name) && $use->alias) {
                        $alias = $use->alias->toString();
                    }
                }
            }

            if ($alias === null) {
                $alias = 'filamentUpgrade' . ucfirst($reference->name->getLast());
                $statements = [...$root->stmts, ...$this->traitImports];
                $names = [...$finder->findInstanceOf($statements, Name::class), ...$finder->findInstanceOf($statements, Identifier::class)];
                $imports = $finder->findInstanceOf($statements, UseItem::class);

                while (array_filter($names, static fn (Name | Identifier $node): bool => strcasecmp($node instanceof Name ? $node->getFirst() : $node->toString(), $alias) === 0) || array_filter($imports, static fn (UseItem $use): bool => strcasecmp($use->getAlias()->toString(), $alias) === 0)) {
                    $alias .= 'Value';
                }

                $this->traitImports[] = new Use_([new UseItem(new Name($name), new Identifier($alias))], $type);
            }

            $reference->name = new Name($alias);
        }
    }

    /**
     * @template TNode of Node
     *
     * @param  TNode  $node
     * @return TNode
     */
    private function copyTraitNode(Node $node): Node
    {
        $copy = clone $node;
        $copy->setAttribute(AttributeKey::ORIGINAL_NODE, null);
        $copy->setAttribute(AttributeKey::ORIGINAL_NAME, null);

        foreach ($node->getSubNodeNames() as $name) {
            $value = $node->{$name};

            if ($value instanceof Node) {
                $copy->{$name} = $this->copyTraitNode($value);
            } elseif (is_array($value)) {
                $copy->{$name} = array_map(fn (mixed $value): mixed => $value instanceof Node ? $this->copyTraitNode($value) : $value, $value);
            }
        }

        return $copy;
    }

    private function hasTraitHookInHierarchy(ClassReflection $class, string $oldName): bool
    {
        $root = $class;

        while ($parent = $this->getApplicationParentHookOwner($root, $oldName)) {
            $root = $parent;
        }

        foreach ($this->traitConsumerResolver->getSourceClasses($this->getFile()->getFilePath()) as $candidate) {
            if ((! $candidate->isTrait()) && (new ObjectType($root->getName()))->isSuperTypeOf(new ObjectType($candidate->getName()))->yes() && $this->getTraitHook($candidate, $oldName)) {
                return true;
            }
        }

        return false;
    }

    private function canMigrateHierarchy(Class_ $class, string $oldName, string $newName, bool $nullable, bool $static = false, string $returnType = 'array', bool $retainsLegacy = false): bool
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
            if ($candidate->hasNativeMethod($newName) && (! $this->isFrameworkHook($candidate, $newName))) {
                return false;
            }

            $declaration = $this->astResolver->resolveClassFromClassReflection($candidate);

            // Inherited calls and configuration constrain the whole hierarchy too.
            if ((! $declaration instanceof Class_) || (! $this->canMigrateHookReferences($declaration, $oldName, $newName, $retainsLegacy))) {
                return false;
            }

            foreach ([$candidate, ...$this->getLocalTraits($candidate)] as $owner) {
                $ownerDeclaration = $this->astResolver->resolveClassFromClassReflection($owner);

                if ((! $ownerDeclaration) || array_filter($ownerDeclaration->getTraitUses(), static fn (TraitUse $traitUse): bool => (bool) $traitUse->adaptations)) {
                    return false;
                }

                if ($owner->isTrait() && (! in_array($owner->getName(), ['Filament\\Widgets\\Concerns\\InteractsWithPageFilters', CanGenerateResourceTables::class, 'Filament\\Tables\\Commands\\FileGenerators\\Concerns\\CanGenerateModelTables'], true)) && (! $this->canMigrateHookReferences($ownerDeclaration, $oldName, $newName, $retainsLegacy))) {
                    return false;
                }

                if ($static && array_filter(['label', 'pluralLabel', 'modelLabel', 'pluralModelLabel'], static fn (string $property): bool => $ownerDeclaration->getProperty($property) !== null)) {
                    return false;
                }
            }

            $method = $declaration->getMethod($oldName) ?? $this->getTraitHook($candidate, $oldName);

            if ($candidate->hasNativeMethod($oldName) && ($candidate->getNativeMethod($oldName)->getDeclaringClass()->getName() === $candidate->getName()) && (! $method) && (! $this->isFrameworkHook($candidate, $oldName))) {
                return false;
            }

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

            $method = $this->astResolver->resolveClassFromClassReflection($candidate)?->getMethod($oldName) ?? $this->getTraitHook($candidate, $oldName);

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

    private function canMigrateHookReferences(ClassLike $class, string $oldName, string $newName, bool $retainsLegacy = false): bool
    {
        $method = $class->getMethod($oldName);

        if ((! $retainsLegacy) && $method && $this->betterNodeFinder->findFirstInFunctionLikeScoped($method, static fn (Node $candidate): bool => $candidate instanceof Return_ && (! $candidate->expr))) {
            return false;
        }

        $hookStaticCalls = (new NodeFinder)->findInstanceOf($class->getMethod($oldName)->stmts ?? [], StaticCall::class);

        return ! (new NodeFinder)->findFirst($class->stmts, function (Node $candidate) use ($class, $oldName, $newName, $hookStaticCalls, $retainsLegacy): bool {
            if ($candidate instanceof ClassLike) {
                return true;
            }

            if ((! $retainsLegacy) && $candidate instanceof String_ && in_array(strtolower($candidate->value), [strtolower($oldName), strtolower($newName)], true)) {
                return true;
            }

            if ((! $candidate instanceof MethodCall) && (! $candidate instanceof NullsafeMethodCall) && (! $candidate instanceof StaticCall)) {
                return false;
            }

            if (! $candidate->name instanceof Identifier) {
                return true;
            }

            if ($this->isName($candidate->name, $newName)) {
                return ! $retainsLegacy;
            }

            if (! $this->isName($candidate->name, $oldName)) {
                return false;
            }

            if ($retainsLegacy && ((! $candidate instanceof StaticCall) || ($candidate->class instanceof Name && $this->isNames($candidate->class, ['self', 'static'])))) {
                return false;
            }

            return (! $candidate instanceof StaticCall) || (! $candidate->class instanceof Name) || (! $this->isName($candidate->class, 'parent')) || $candidate->isFirstClassCallable() || (bool) $candidate->args || ((! ($retainsLegacy && $class instanceof Class_)) && (! in_array($candidate, $hookStaticCalls, true)));
        });
    }

    private function migrateResourceLabels(Class_ $class): ?Class_
    {
        $changed = false;

        if ((! $class->getProperty('label')) && (! $class->getProperty('modelLabel')) && (! $class->getProperty('pluralLabel')) && (! $class->getProperty('pluralModelLabel'))) {
            foreach (['getLabel' => 'getModelLabel', 'getPluralLabel' => 'getPluralModelLabel'] as $oldName => $newName) {
                $context = ScopeFetcher::fetch($class)->getClassReflection();
                $method = $class->getMethod($oldName) ?? $this->getTraitHook($context, $oldName);

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

                $hasLegacyChildHook = $this->hasTraitHookInHierarchy($context, $oldName) || $this->hasLegacyChildHook($class, $oldName);

                if ($hasLegacyChildHook) {
                    if ($method !== $class->getMethod($oldName)) {
                        $this->aliasTraitFunctionsAndConstants($method, $class);
                    }

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

                if (! $class->getMethod($newName)) {
                    $class->stmts[] = $method;
                }

                $changed = true;
            }
        }

        foreach (['label' => 'modelLabel', 'pluralLabel' => 'pluralModelLabel'] as $oldName => $newName) {
            $isTraitProperty = $class->getProperty($oldName) === null;
            $property = $class->getProperty($oldName) ?? $this->getTraitLabelProperty(ScopeFetcher::fetch($class)->getClassReflection(), $oldName);

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

            if ($isTraitProperty) {
                $this->aliasTraitFunctionsAndConstants($property, $class);
                $class->stmts[] = $property;
            }

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
            $property = $declaration?->getProperty($oldName) ?? $this->getTraitLabelProperty($candidate, $oldName);

            if ((! $property) && (($candidate->hasNativeProperty($oldName) && ($candidate->getNativeProperty($oldName)->getDeclaringClass()->getName() === $candidate->getName())) || array_filter($this->getLocalTraits($candidate), static fn (ClassReflection $trait): bool => $trait->hasNativeProperty($oldName)))) {
                return false;
            }

            if ($property && ((! $property->isStatic()) || $property->isPrivate() || (count($property->props) !== 1))) {
                return false;
            }

            foreach ([$candidate, ...$this->getLocalTraits($candidate)] as $owner) {
                $ownerDeclaration = $this->astResolver->resolveClassFromClassReflection($owner);

                if ((! $ownerDeclaration) || array_filter($ownerDeclaration->getTraitUses(), static fn (TraitUse $traitUse): bool => (bool) $traitUse->adaptations)) {
                    return false;
                }

                if ((new NodeFinder)->findFirst($ownerDeclaration->stmts, function (Node $node) use ($oldName, $newName): bool {
                    if ($node instanceof ClassMethod && $this->isNames($node->name, ['getLabel', 'getPluralLabel', 'getModelLabel', 'getPluralModelLabel'])) {
                        return true;
                    }

                    return $node instanceof StaticPropertyFetch && ((! $node->name instanceof VarLikeIdentifier) || in_array($node->name->toString(), [$oldName, $newName], true));
                })) {
                    return false;
                }
            }

            foreach ([$candidate, ...$candidate->getParents()] as $ancestor) {
                foreach (['getLabel', 'getPluralLabel'] as $method) {
                    if ($ancestor->hasNativeMethod($method) && ($ancestor->getNativeMethod($method)->getDeclaringClass()->getName() !== 'Filament\\Resources\\Resource')) {
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
