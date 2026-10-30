<?php

namespace Filament\Upgrade\Rector;

use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Imports\Events\ImportChunkProcessed;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Concerns\HasAffixes;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Filament\Forms\Components\Slider;
use Filament\Infolists\Components\Concerns\HasIcon;
use Filament\Navigation\NavigationItem;
use Filament\Notifications\Notification;
use Filament\Pages\BasePage;
use Filament\Resources\ParentResourceRegistration;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\StateCasts\BooleanStateCast;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Concerns\CanBeHidden;
use Filament\Support\Commands\Concerns\CanAskForSchema;
use Filament\Support\Concerns\HasReorderAnimationDuration;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Concerns\HasColumns;
use Filament\Upgrade\Support\ParameterRenameGuard;
use Filament\Upgrade\Support\PropertyRenameGuard;
use Filament\Upgrade\Support\TraitConsumerResolver;
use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\AssignRef;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Rector\BetterPhpDocParser\ValueObject\PhpDocAttributeKey;
use Rector\Comments\NodeDocBlock\DocBlockUpdater;
use Rector\PHPStan\ScopeFetcher;
use Rector\Rector\AbstractRector;

class RenameApiParametersRector extends AbstractRector
{
    /**
     * @var array<class-string, array<string, array<string, string>>>
     */
    private const RENAMES = [
        HasActions::class => ['getAction' => ['name' => 'actions']],
        ImportChunkProcessed::class => ['__construct' => ['processedRows' => 'processedRowsCount', 'successfulRows' => 'successfulRowsCount']],
        RichEditor::class => ['plugins' => ['extensions' => 'plugins']],
        Slider::class => [
            'minValue' => ['minValue' => 'value'],
            'maxValue' => ['maxValue' => 'value'],
            'minDifference' => ['minDifference' => 'difference'],
        ],
        Block::class => ['maxItems' => ['maxItems' => 'count']],
        HasAffixes::class => [
            'inlinePrefix' => ['isInline' => 'condition'],
            'inlineSuffix' => ['isInline' => 'condition'],
        ],
        TextInputColumn::class => [
            'inlinePrefix' => ['isInline' => 'condition'],
            'inlineSuffix' => ['isInline' => 'condition'],
        ],
        RichContentCustomBlock::class => [
            'toHtml' => ['config' => 'configuration'],
            'getPreviewLabel' => ['config' => 'configuration'],
            'toPreviewHtml' => ['config' => 'configuration'],
            'shouldApplyProseStylingToPreview' => ['config' => 'configuration'],
        ],
        RichContentRenderer::class => ['getCustomBlockHtml' => ['config' => 'configuration']],
        NavigationItem::class => ['parentItem' => ['group' => 'parentItem']],
        ParentResourceRegistration::class => ['getInverseRelationship' => ['parentRecord' => 'childRecord']],
        CanBeHidden::class => ['hidden' => ['hidden' => 'condition']],
        Actions::class => ['fullWidth' => ['isFullWidth' => 'condition']],
        Get::class => [
            'string' => ['key' => 'path'],
            'integer' => ['key' => 'path'],
            'float' => ['key' => 'path'],
            'boolean' => ['key' => 'path'],
            'array' => ['key' => 'path'],
            'date' => ['key' => 'path'],
            'enum' => ['key' => 'path'],
            'filled' => ['key' => 'path'],
            'blank' => ['key' => 'path'],
        ],
        HasIcon::class => ['iconPosition' => ['iconPosition' => 'position']],
        \Filament\Tables\Columns\Concerns\HasIcon::class => ['iconPosition' => ['iconPosition' => 'position']],
        BooleanStateCast::class => ['__construct' => ['isStoredAsInt' => 'isStoredAsInteger']],
        CanAskForSchema::class => ['askForSchema' => ['intialQuestion' => 'initialQuestion']],
        HasReorderAnimationDuration::class => ['reorderAnimationDuration' => ['animation' => 'duration']],
        Notification::class => ['sendToDatabase' => ['isEventDispatched' => 'shouldDispatchEvent']],
        HasColumns::class => ['updateTableColumnState' => ['column' => 'name', 'record' => 'recordKey', 'input' => 'state']],
        BaseFileUpload::class => ['disk' => ['name' => 'disk']],
        BasePage::class => ['halt' => ['shouldRollbackDatabaseTransaction' => 'shouldRollBackDatabaseTransaction']],
    ];

    public function __construct(
        private PhpDocInfoFactory $phpDocInfoFactory,
        private DocBlockUpdater $docBlockUpdater,
        private TraitConsumerResolver $traitConsumerResolver,
        private PropertyRenameGuard $propertyRenameGuard,
        private ParameterRenameGuard $parameterRenameGuard,
    ) {}

    public function getNodeTypes(): array
    {
        return [ClassMethod::class, MethodCall::class, NullsafeMethodCall::class, StaticCall::class, New_::class];
    }

    /**
     * @param  ClassMethod | MethodCall | NullsafeMethodCall | StaticCall | New_  $node
     */
    public function refactor(Node $node): ?Node
    {
        if ((! $node instanceof New_) && (! $node->name instanceof Identifier)) {
            return null;
        }

        $method = $node instanceof New_ ? '__construct' : $this->getName($node->name);

        if (($method === null) || (! $this->hasLegacyParameterName($node))) {
            return null;
        }

        $type = $this->receiverType($node);
        $originalMethod = $this->resolveTraitAlias($type, $method);

        foreach (self::RENAMES as $owner => $methods) {
            $renames = array_change_key_case($methods)[strtolower($originalMethod)] ?? null;

            if ((! $renames) || (! $this->hasLegacyParameterName($node, $renames)) || (! $this->supports($type, $owner))) {
                continue;
            }

            $renames = array_filter($renames, fn (string $newName, string $oldName): bool => $this->canRename($type, $owner, $method, [$oldName => $newName]), ARRAY_FILTER_USE_BOTH);

            if (! $renames) {
                return null;
            }

            if ($node instanceof ClassMethod) {
                return $this->renameParameters($node, $renames);
            }

            if ($node->isFirstClassCallable()) {
                return null;
            }

            $changed = false;

            foreach ($node->getArgs() as $argument) {
                if ($argument->name && isset($renames[$argument->name->toString()])) {
                    $argument->name = new Identifier($renames[$argument->name->toString()]);
                    $changed = true;
                }

                if ($argument->unpack && ($argument->value instanceof Array_)) {
                    foreach ($argument->value->items as $item) {
                        if (($item->key instanceof String_) && isset($renames[$item->key->value])) {
                            $item->key = new String_($renames[$item->key->value]);
                            $changed = true;
                        }
                    }
                }
            }

            return $changed ? $node : null;
        }

        return null;
    }

    /**
     * @param  array<string, string> | null  $names
     */
    private function hasLegacyParameterName(ClassMethod | MethodCall | NullsafeMethodCall | StaticCall | New_ $node, ?array $names = null): bool
    {
        if ($names === null) {
            $names = [];

            foreach (self::RENAMES as $methods) {
                foreach ($methods as $renames) {
                    $names += $renames;
                }
            }
        }

        if ($node instanceof ClassMethod) {
            foreach ($node->params as $parameter) {
                if (($parameter->var instanceof Variable) && is_string($parameter->var->name) && isset($names[$parameter->var->name])) {
                    return true;
                }
            }

            return false;
        }

        if ($node->isFirstClassCallable()) {
            return false;
        }

        foreach ($node->getArgs() as $argument) {
            if ($argument->name && isset($names[$argument->name->toString()])) {
                return true;
            }

            if ($argument->unpack && ($argument->value instanceof Array_)) {
                foreach ($argument->value->items as $item) {
                    if (($item->key instanceof String_) && isset($names[$item->key->value])) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    private function resolveTraitAlias(Type $type, string $method): string
    {
        $originalMethods = [];

        foreach ($type->getObjectClassReflections() as $class) {
            $originalMethods[] = $this->resolveClassTraitAlias($class, $method);
        }

        return count(array_unique($originalMethods)) === 1 ? $originalMethods[0] : $method;
    }

    private function resolveClassTraitAlias(ClassReflection $class, string $method): string
    {
        if (! $class->hasNativeMethod($method)) {
            return $method;
        }

        $reflection = $class->getNativeMethod($method);
        $trait = method_exists($reflection, 'getDeclaringTrait') ? $reflection->getDeclaringTrait() : null;

        if (! $trait) {
            return $method;
        }

        foreach ([$class, ...$class->getParents(), ...$class->getTraits(true)] as $ancestor) {
            $aliases = array_change_key_case($ancestor->getNativeReflection()->getTraitAliases());

            if (isset($aliases[strtolower($method)])) {
                [$traitName, $originalMethod] = explode('::', $aliases[strtolower($method)]);

                if (($traitName === $trait->getName()) || $trait->hasTraitUse($traitName)) {
                    return $originalMethod;
                }
            }
        }

        return $method;
    }

    /**
     * @param  class-string  $owner
     */
    private function hasAmbiguousTraitAlias(string $owner, string $method): bool
    {
        $classes = array_filter(
            $this->traitConsumerResolver->getSourceClasses($this->getFile()->getFilePath()),
            static fn (ClassReflection $class): bool => (! $class->isTrait())
                && ((new ObjectType($owner))->isSuperTypeOf(new ObjectType($class->getName()))->yes() || $class->hasTraitUse($owner)),
        );

        foreach ($classes as $class) {
            foreach ([$class, ...$class->getParents(), ...$class->getTraits(true)] as $ancestor) {
                foreach ($ancestor->getNativeReflection()->getTraitAliases() as $alias => $original) {
                    if (strcasecmp(explode('::', $original)[1], $method) !== 0) {
                        continue;
                    }

                    foreach ($classes as $candidate) {
                        if ($candidate->hasNativeMethod($alias) && (strcasecmp($this->resolveClassTraitAlias($candidate, $alias), $method) !== 0)) {
                            // A union/base-typed call can reach either signature. Keep
                            // this parameter family together for manual reconciliation.
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    private function receiverType(ClassMethod | MethodCall | NullsafeMethodCall | StaticCall | New_ $node): Type
    {
        if ($node instanceof ClassMethod) {
            $context = ScopeFetcher::fetch($node)->getClassReflection();

            return new ObjectType($context?->getName() ?? '');
        }

        if (($node instanceof StaticCall) && ($node->class instanceof Name) && $this->isNames($node->class, ['parent', 'self', 'static'])) {
            $context = ScopeFetcher::fetch($node)->getClassReflection();

            return new ObjectType(($this->isName($node->class, 'parent') ? $context?->getParentClass()?->getName() : $context?->getName()) ?? '');
        }

        $type = $this->getType(($node instanceof MethodCall || $node instanceof NullsafeMethodCall) ? $node->var : $node->class);

        return TypeCombinator::removeNull($type->isClassString()->yes() ? $type->getClassStringObjectType() : $type);
    }

    /**
     * @param  class-string  $owner
     */
    private function supports(Type $type, string $owner): bool
    {
        if ((new ObjectType($owner))->isSuperTypeOf($type)->yes()) {
            return true;
        }

        $classes = $type->getObjectClassReflections();

        foreach ($classes as $class) {
            if ($class->hasTraitUse($owner)) {
                continue;
            }

            $consumers = $class->isTrait() ? $this->traitConsumerResolver->getConsumers($class, $this->getFile()->getFilePath()) : [];

            if (! $consumers) {
                return false;
            }

            foreach ($consumers as $consumer) {
                if ((! (new ObjectType($owner))->isSuperTypeOf(new ObjectType($consumer->getName()))->yes()) && (! $consumer->hasTraitUse($owner))) {
                    return false;
                }
            }
        }

        return count($classes) > 0;
    }

    /**
     * @param  class-string  $owner
     * @param  array<string, string>  $renames
     */
    private function canRename(Type $type, string $owner, string $method, array $renames): bool
    {
        $originalMethod = $this->resolveTraitAlias($type, $method);

        if ($this->hasAmbiguousTraitAlias($owner, $originalMethod)) {
            return false;
        }

        foreach ($renames as $oldName => $newName) {
            if (! $this->parameterRenameGuard->canRename($owner, $originalMethod, $oldName, $newName, $this->getFile()->getFilePath())) {
                return false;
            }
        }

        foreach ($type->getObjectClassReflections() as $class) {
            if (! $class->hasNativeMethod($method)) {
                continue;
            }

            $reflection = $class->getNativeMethod($method);
            $parameters = array_map(static fn ($parameter): string => $parameter->getName(), $reflection->getVariants()[0]->getParameters());

            foreach ($renames as $oldName => $newName) {
                if (($method === '__construct') && (! $this->propertyRenameGuard->canRename($class, $owner, $oldName, $newName, $this->getFile()->getFilePath()))) {
                    return false;
                }

                if (! in_array($oldName, $parameters, true)) {
                    continue;
                }

                if (in_array($newName, $parameters, true)) {
                    return false;
                }

                $trait = method_exists($reflection, 'getDeclaringTrait') ? $reflection->getDeclaringTrait() : null;

                if ($trait && (! $this->supports(new ObjectType($trait->getName()), $owner))) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param  array<string, string>  $renames
     */
    private function renameParameters(ClassMethod $method, array $renames): ?ClassMethod
    {
        $aliases = [];
        $changed = false;
        $phpDoc = $this->phpDocInfoFactory->createFromNode($method);

        foreach ($method->params as $parameter) {
            if ((! $parameter->var instanceof Variable) || (! is_string($parameter->var->name))) {
                continue;
            }

            $oldName = $parameter->var->name;
            $newName = $renames[$oldName] ?? null;

            if ((! $newName) || ($newName === $oldName)) {
                continue;
            }

            $parameter->var->name = $newName;
            $changed = true;

            // Keep application bodies, closure captures and `compact()` keys intact.
            // Keep mutations visible to `func_get_args()` as well.
            if ($method->stmts) {
                $aliases[] = new Expression(new AssignRef(new Variable($oldName), new Variable($newName)));
            }

            if ($tag = $phpDoc?->getParamTagValueByName($oldName)) {
                $tag->parameterName = '$' . $newName;
                $tag->setAttribute(PhpDocAttributeKey::ORIG_NODE, null);
            }
        }

        if (! $changed) {
            return null;
        }

        if ($method->stmts !== null) {
            $method->stmts = [...$aliases, ...$method->stmts];
        }

        if ($phpDoc) {
            $this->docBlockUpdater->updateRefactoredNodeWithPhpDocInfo($method);
        }

        return $method;
    }
}
