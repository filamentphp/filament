<?php

namespace Filament\Upgrade\Rector;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\ReplicateAction;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Infolists\Components\Entry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Concerns\EntanglesStateWithSingularRelationship;
use Filament\Schemas\Components\Concerns\HasLabel;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Components\ViewComponent;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Type\NullType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StaticType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class RenameDeprecatedForwardingMethodsRector extends AbstractRector
{
    /**
     * @var array<class-string, array<string, string>>
     */
    private const METHOD_RENAMES = [
        Action::class => [
            'emit' => 'dispatch',
            'emitSelf' => 'dispatchSelf',
            'emitTo' => 'dispatchTo',
            'disableForm' => 'disabledSchema',
            'disabledForm' => 'disabledSchema',
            'form' => 'schema',
            'getForm' => 'getSchema',
            'isFormDisabled' => 'isSchemaDisabled',
            'mutateFormDataUsing' => 'mutateDataUsing',
            'formData' => 'data',
            'resetFormData' => 'resetData',
            'getFormData' => 'getData',
            'getRawFormData' => 'getRawData',
            'modalActions' => 'modalFooterActions',
            'extraModalActions' => 'extraModalFooterActions',
            'modalButton' => 'modalSubmitActionLabel',
            'modalFooter' => 'modalContentFooter',
            'modalSubheading' => 'modalDescription',
            'makeExtraModalAction' => 'makeModalSubmitAction',
            'failureNotificationMessage' => 'failureNotificationTitle',
            'successNotificationMessage' => 'successNotificationTitle',
            'withAttributes' => 'extraAttributes',
            'hold' => 'halt',
            'infolist' => 'schema',
            'disableLabel' => 'hiddenLabel',
            'indicator' => 'badge',
            'indicatorColor' => 'badgeColor',
        ],
        ActionGroup::class => [
            'disableLabel' => 'hiddenLabel',
            'indicator' => 'badge',
            'indicatorColor' => 'badgeColor',
        ],
        ReplicateAction::class => [
            'afterReplicaSaved' => 'after',
        ],
        ExportColumn::class => [
            'queriesRelationships' => 'hasRelationship',
        ],
        BaseFileUpload::class => [
            'enableDownload' => 'downloadable',
            'enableOpen' => 'openable',
            'enableReordering' => 'reorderable',
            'moveFile' => 'moveFiles',
            'storeFile' => 'storeFiles',
        ],
        Builder::class => [
            'createItemBetweenButtonLabel' => 'addBetweenActionLabel',
            'createItemButtonLabel' => 'addActionLabel',
            'showBlockLabels' => 'blockLabels',
            'withBlockLabels' => 'blockLabels',
            'withBlockNumbers' => 'blockNumbers',
        ],
        Repeater::class => [
            'createItemButtonLabel' => 'addActionLabel',
            'orderable' => 'orderColumn',
        ],
        KeyValue::class => [
            'addButtonLabel' => 'addActionLabel',
            'deleteButtonLabel' => 'deleteActionLabel',
            'reorderButtonLabel' => 'reorderActionLabel',
        ],
        FileUpload::class => [
            'idleLabel' => 'placeholder',
            'imageResizeMode' => 'automaticallyResizeImagesMode',
            'imageResizeTargetHeight' => 'automaticallyResizeImagesToHeight',
            'imageResizeTargetWidth' => 'automaticallyResizeImagesToWidth',
            'imageResizeUpscale' => 'automaticallyUpscaleImagesWhenResizing',
            'orientImageFromExif' => 'orientImagesFromExif',
            'getImageCropAspectRatio' => 'getAutomaticallyCropImagesAspectRatio',
            'getImageResizeMode' => 'getAutomaticallyResizeImagesMode',
            'getImageResizeTargetHeight' => 'getAutomaticallyResizeImagesHeight',
            'getImageResizeTargetWidth' => 'getAutomaticallyResizeImagesWidth',
            'getImageResizeUpscale' => 'shouldAutomaticallyUpscaleImagesWhenResizing',
            'imageEditorAspectRatios' => 'imageEditorAspectRatioOptions',
            'getImageEditorAspectRatiosForJs' => 'getImageEditorAspectRatioOptionsForJs',
        ],
        ImageEntry::class => [
            'height' => 'imageHeight',
            'size' => 'imageSize',
            'width' => 'imageWidth',
            'getHeight' => 'getImageHeight',
            'getWidth' => 'getImageWidth',
        ],
        ImageColumn::class => [
            'height' => 'imageHeight',
            'rounded' => 'circular',
            'size' => 'imageSize',
            'getHeight' => 'getImageHeight',
            'isRounded' => 'isCircular',
        ],
        KeyValueEntry::class => [
            'emptyMessage' => 'placeholder',
        ],
        Schema::class => [
            'name' => 'key',
            'getName' => 'getKey',
            'context' => 'operation',
            'clearCachedDefaultChildSchemas' => 'clearCachedChildSchemas',
        ],
        Component::class => [
            'getGetCallback' => 'makeGetUtility',
            'getSetCallback' => 'makeSetUtility',
            'getChildComponentContainer' => 'getChildSchema',
            'getChildComponentContainers' => 'getChildSchemas',
            'clearCachedDefaultChildSchemas' => 'clearCachedChildSchemas',
        ],
        Step::class => [
            'afterValidated' => 'afterValidation',
        ],
        ViewComponent::class => [
            'disableLabel' => 'hiddenLabel',
        ],
        Tab::class => [
            'indicator' => 'badge',
            'indicatorColor' => 'badgeColor',
        ],
        Column::class => [
            'disableClick' => 'disabledClick',
            'queriesRelationships' => 'hasRelationship',
        ],
        IconColumn::class => [
            'options' => 'icons',
        ],
        Table::class => [
            'actions' => 'recordActions',
            'pushActions' => 'pushRecordActions',
            'actionsColumnLabel' => 'recordActionsColumnLabel',
            'actionsAlignment' => 'recordActionsAlignment',
            'actionsPosition' => 'recordActionsPosition',
            'getActions' => 'getRecordActions',
            'getActionsPosition' => 'getRecordActionsPosition',
            'getActionsAlignment' => 'getRecordActionsAlignment',
            'getActionsColumnLabel' => 'getRecordActionsColumnLabel',
            'bulkActions' => 'toolbarActions',
            'pushBulkActions' => 'pushToolbarActions',
            'getBulkActions' => 'getToolbarActions',
            'toggleColumnsTriggerAction' => 'columnManagerTriggerAction',
            'columnToggleFormColumns' => 'columnManagerColumns',
            'columnToggleFormMaxHeight' => 'columnManagerMaxHeight',
            'columnToggleFormWidth' => 'columnManagerWidth',
            'getColumnToggleFormColumns' => 'getColumnManagerColumns',
            'getColumnToggleFormMaxHeight' => 'getColumnManagerMaxHeight',
            'getColumnToggleFormWidth' => 'getColumnManagerWidth',
            'getToggleColumnsTriggerAction' => 'getColumnManagerTriggerAction',
        ],
    ];

    /**
     * @var array<string, array{string, string}>
     */
    private const ARGUMENT_RENAMES = [
        'form' => ['form', 'schema'],
        'infolist' => ['infolist', 'schema'],
        'modalsubheading' => ['subheading', 'description'],
        'failurenotificationmessage' => ['message', 'title'],
        'successnotificationmessage' => ['message', 'title'],
        'idlelabel' => ['label', 'placeholder'],
        'emptymessage' => ['message', 'placeholder'],
        'name' => ['name', 'key'],
        'context' => ['context', 'operation'],
        'options' => ['options', 'icons'],
        'indicator' => ['indicator', 'badge'],
    ];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace deprecated Filament forwarding method calls on their owning types', [
            new CodeSample(
                '$action->form(form: []);',
                '$action->schema(schema: []);',
            ),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [MethodCall::class, NullsafeMethodCall::class];
    }

    /**
     * @param  MethodCall | NullsafeMethodCall  $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $node->name instanceof Identifier) {
            return null;
        }

        $methodName = strtolower($node->name->toString());

        foreach (self::METHOD_RENAMES as $componentClass => $methodRenames) {
            $methodRenames = array_change_key_case($methodRenames);

            if (! isset($methodRenames[$methodName])) {
                continue;
            }

            // Preserve the table migration's existing receiver inference boundary.
            $receiverType = ($componentClass === Table::class)
                ? TypeCombinator::removeNull($this->getType($node->var))
                : $this->resolveReceiverType($node->var);

            if (! (new ObjectType($componentClass))->isSuperTypeOf($receiverType)->yes()) {
                continue;
            }

            $replacement = $methodRenames[$methodName];

            if ((! $this->canRenameMethods($receiverType, $componentClass, $methodName, $replacement)) || (! $this->canForwardArguments($node, $componentClass, $methodName))) {
                return null;
            }

            $renamedArgument = self::ARGUMENT_RENAMES[$methodName] ?? null;

            if ($renamedArgument && (! $node->isFirstClassCallable())) {
                foreach ($node->getArgs() as $argument) {
                    if ($argument->unpack) {
                        return null;
                    }
                }

                foreach ($node->getArgs() as $argument) {
                    if ($argument->name?->toString() === $renamedArgument[0]) {
                        $argument->name = new Identifier($renamedArgument[1]);
                    }
                }
            }

            $node->name = new Identifier($replacement);

            return $node;
        }

        return null;
    }

    /**
     * @param  class-string  $componentClass
     */
    private function canRenameMethods(Type $receiverType, string $componentClass, string $methodName, string $replacement): bool
    {
        $candidateMethodNames = [$methodName, $replacement];

        if ($methodName === 'showblocklabels') {
            $candidateMethodNames[] = 'withBlockLabels';
        }

        // Retain the older form, image, and table rules' reflection boundaries.
        $requiresPublicReplacement = ! in_array($componentClass, [BaseFileUpload::class, Builder::class, Repeater::class, KeyValue::class, FileUpload::class, ImageEntry::class, ImageColumn::class, KeyValueEntry::class, Table::class], strict: true);

        foreach ($receiverType->getObjectClassReflections() as $classReflection) {
            foreach ($candidateMethodNames as $candidateMethodName) {
                if (! $classReflection->hasNativeMethod($candidateMethodName)) {
                    if ((! $requiresPublicReplacement) || ($candidateMethodName === $methodName)) {
                        continue;
                    }

                    return false;
                }

                $methodReflection = $classReflection->getNativeMethod($candidateMethodName);

                if ($requiresPublicReplacement && (! $methodReflection->isPublic())) {
                    return false;
                }

                if ($componentClass === ViewComponent::class) {
                    // This concern also belongs to fields, entries, stats, and editor tools.
                    if ((! method_exists($methodReflection, 'getDeclaringTrait')) || ($methodReflection->getDeclaringTrait()?->getName() !== HasLabel::class)) {
                        return false;
                    }

                    continue;
                }

                $declaringClass = match (true) {
                    ($componentClass === ReplicateAction::class) && ($candidateMethodName === 'after') => Action::class,
                    ($componentClass === KeyValueEntry::class) && ($candidateMethodName === 'placeholder') => Entry::class,
                    default => $componentClass,
                };

                if ($methodReflection->getDeclaringClass()->getName() === $declaringClass) {
                    continue;
                }

                // The built-in relationship concern preserves this schema getter's contract.
                if (($componentClass === Component::class) && ($candidateMethodName === 'getChildSchema') && method_exists($methodReflection, 'getDeclaringTrait') && ($methodReflection->getDeclaringTrait()?->getName() === EntanglesStateWithSingularRelationship::class)) {
                    continue;
                }

                return false;
            }
        }

        return true;
    }

    /**
     * @param  class-string  $componentClass
     */
    private function canForwardArguments(MethodCall | NullsafeMethodCall $node, string $componentClass, string $methodName): bool
    {
        // `getName()` ignored extra positional arguments; `getKey()` would consume one.
        if (($componentClass === Schema::class) && ($methodName === 'getname')) {
            return (! $node->isFirstClassCallable()) && (! count($node->getArgs()));
        }

        // These action wrappers did not forward the replacement's optional parameters.
        if (($componentClass === Action::class) && in_array($methodName, ['hold', 'withattributes'], strict: true)) {
            if ($node->isFirstClassCallable() || (count($node->getArgs()) > (($methodName === 'hold') ? 0 : 1))) {
                return false;
            }

            foreach ($node->getArgs() as $argument) {
                if ($argument->unpack) {
                    return false;
                }
            }
        }

        // Actions narrow `badge()` to strings and closures, and no arguments select styling.
        // The old wrapper coerced numbers even when called from strict PHP files.
        if (($methodName === 'indicator') && in_array($componentClass, [Action::class, ActionGroup::class], strict: true)) {
            if ($node->isFirstClassCallable() || (count($node->getArgs()) !== 1)) {
                return false;
            }

            $argument = $node->getArgs()[0];

            return (! $argument->unpack) && TypeCombinator::union(new StringType, new ObjectType(Closure::class), new NullType)->isSuperTypeOf($this->getType($argument->value))->yes();
        }

        return true;
    }

    private function resolveReceiverType(Expr $receiver): Type
    {
        $receiverType = TypeCombinator::removeNull($this->getType($receiver));

        if (
            ((! $receiver instanceof MethodCall) && (! $receiver instanceof NullsafeMethodCall)) ||
            (! $receiver->name instanceof Identifier) ||
            $receiver->isFirstClassCallable()
        ) {
            return $receiverType;
        }

        $callerType = $this->resolveReceiverType($receiver->var);

        if ($callerType->isSuperTypeOf($receiverType)->yes()) {
            return $receiverType;
        }

        $classReflections = $callerType->getObjectClassReflections();

        if (! count($classReflections)) {
            return $receiverType;
        }

        foreach ($classReflections as $classReflection) {
            if (! $classReflection->hasNativeMethod($receiver->name->toString())) {
                return $receiverType;
            }

            $methodReflection = $classReflection->getNativeMethod($receiver->name->toString());

            if (! $methodReflection->getVariants()[0]->getReturnType() instanceof StaticType) {
                return $receiverType;
            }
        }

        // Inherited `static` returns retain the caller's class, not the declaring class.
        return $callerType;
    }
}
