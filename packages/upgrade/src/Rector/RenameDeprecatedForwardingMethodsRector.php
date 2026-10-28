<?php

namespace Filament\Upgrade\Rector;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Imports\Events\ImportChunkProcessed;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\ReplicateAction;
use Filament\Clusters\Cluster;
use Filament\Commands\FileGenerators\Resources\Pages\ResourceManageRelatedRecordsPageClassGenerator;
use Filament\Commands\FileGenerators\Resources\RelationManagerClassGenerator;
use Filament\Commands\FileGenerators\Resources\ResourceClassGenerator;
use Filament\Commands\FileGenerators\Resources\Schemas\ResourceTableClassGenerator;
use Filament\Facades\Filament;
use Filament\FilamentManager;
use Filament\Forms\Commands\FileGenerators\RichContentCustomBlockClassGenerator;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Concerns\HasAffixes;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\MorphToSelect\Type as MorphToSelectType;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\MentionProvider;
use Filament\Forms\Components\RichEditor\RichContentAttribute;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Infolists\Components\Entry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Navigation\NavigationItem;
use Filament\Notifications\Livewire\Notifications;
use Filament\Pages\BasePage;
use Filament\Pages\Dashboard;
use Filament\Pages\Dashboard\Concerns\HasFilters;
use Filament\Panel;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Concerns\EntanglesStateWithSingularRelationship;
use Filament\Schemas\Components\Concerns\HasLabel;
use Filament\Schemas\Components\FusedGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Components\ViewComponent;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Concerns\CanSortRecords;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Filament\Upgrade\ConfigurationClassAnalyzer;
use Filament\Upgrade\Support\UnnormalizedUnionTypeResolver;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersSchema;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\NodeFinder;
use PHPStan\Type\NullType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StaticType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use Rector\PHPStan\ScopeFetcher;
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
            'fillForm' => 'fillSchema',
            'beforeFormFilled' => 'beforeSchemaFilled',
            'afterFormFilled' => 'afterSchemaFilled',
            'beforeFormValidated' => 'beforeSchemaValidated',
            'afterFormValidated' => 'afterSchemaValidated',
            'callBeforeFormFilled' => 'callBeforeSchemaFilled',
            'callAfterFormFilled' => 'callAfterSchemaFilled',
            'callBeforeFormValidated' => 'callBeforeSchemaValidated',
            'callAfterFormValidated' => 'callAfterSchemaValidated',
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
            'hasCustomLivewireClickHandler' => 'hasLivewireClickHandlerEnabledConfiguration',
            'getJavaScriptClickHandler' => 'getJsClickHandler',
        ],
        ActionGroup::class => [
            'disableLabel' => 'hiddenLabel',
            'indicator' => 'badge',
            'indicatorColor' => 'badgeColor',
        ],
        ReplicateAction::class => [
            'afterReplicaSaved' => 'after',
        ],
        CreateAction::class => [
            'preserveFormDataWhenCreatingAnother' => 'preserveDataWhenCreatingAnother',
        ],
        ExportAction::class => [
            'getFileDisk' => 'getFileDiskName',
            'isEnablingVisibleTableColumnsByDefault' => 'shouldEnableVisibleTableColumnsByDefault',
        ],
        ExportBulkAction::class => [
            'getFileDisk' => 'getFileDiskName',
            'isEnablingVisibleTableColumnsByDefault' => 'shouldEnableVisibleTableColumnsByDefault',
        ],
        ExportColumn::class => [
            'queriesRelationships' => 'hasRelationship',
            'getGetStateUsingCallback' => 'getStateUsingCallback',
        ],
        Exporter::class => [
            'getFileDisk' => 'getFileDiskName',
        ],
        ImportColumn::class => [
            'guess' => 'guesses',
        ],
        ImportChunkProcessed::class => [
            'getProcessedRows' => 'getProcessedRowsCount',
            'getSuccessfulRows' => 'getSuccessfulRowsCount',
        ],
        Cluster::class => [
            'unshiftClusterBreadcrumbs' => 'prependClusterBreadcrumbs',
        ],
        BasePage::class => [
            'alignFormActionsLeft' => 'alignFormActionsStart',
            'alignFormActionsRight' => 'alignFormActionsEnd',
        ],
        NavigationItem::class => [
            'isActiveWhen' => 'activeWhen',
            'isChildItemsActive' => 'hasActiveChildItems',
        ],
        Panel::class => [
            'passwordResetRequestRouteSlug' => 'requestPasswordResetRouteSlug',
            'passwordResetRouteSlug' => 'resetPasswordRouteSlug',
            'databaseNotificationsPolling' => 'databaseNotificationsPollingInterval',
            'hasDarkModeForced' => 'isDarkModeForced',
        ],
        FilamentManager::class => [
            'currentDomain' => 'setCurrentDomain',
            'hasDarkModeForced' => 'isDarkModeForced',
        ],
        ResourceClassGenerator::class => [
            'getTableActions' => 'getTableRecordActions',
            'outputTableActions' => 'outputTableRecordActions',
            'outputTableMethodBulkActions' => 'outputTableBulkActions',
        ],
        ResourceTableClassGenerator::class => [
            'getTableActions' => 'getTableRecordActions',
            'outputTableActions' => 'outputTableRecordActions',
            'outputTableMethodBulkActions' => 'outputTableBulkActions',
        ],
        RelationManagerClassGenerator::class => [
            'getTableActions' => 'getTableRecordActions',
            'outputTableActions' => 'outputTableRecordActions',
            'outputTableMethodBulkActions' => 'outputTableBulkActions',
        ],
        ResourceManageRelatedRecordsPageClassGenerator::class => [
            'getTableActions' => 'getTableRecordActions',
            'outputTableActions' => 'outputTableRecordActions',
            'outputTableMethodBulkActions' => 'outputTableBulkActions',
        ],
        RichContentCustomBlockClassGenerator::class => [
            'configureToPreviewMethod' => 'configureToPreviewHtmlMethod',
        ],
        Dashboard::class => [
            'normalizeTableFilterValuesFromQueryString' => 'normalizeFilterValuesFromQueryString',
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
            'getRawItemState' => 'getItemStateSnapshot',
        ],
        Repeater::class => [
            'createItemButtonLabel' => 'addActionLabel',
            'orderable' => 'orderColumn',
            'getRawItemState' => 'getItemStateSnapshot',
        ],
        Field::class => [
            'getExtraFieldWrapperAttributesBag' => 'getExtraFieldWrapperAttributeBag',
        ],
        FusedGroup::class => [
            'getExtraFieldWrapperAttributesBag' => 'getExtraFieldWrapperAttributeBag',
        ],
        Select::class => [
            'manageOptionForm' => 'manageOptionSchema',
            'createOptionForm' => 'createOptionSchema',
            'editOptionForm' => 'editOptionSchema',
            'getCreateOptionActionForm' => 'getCreateOptionActionSchema',
            'getEditOptionActionForm' => 'getEditOptionActionSchema',
            'hasCreateOptionActionFormSchema' => 'hasCreateOptionActionSchema',
            'hasEditOptionActionFormSchema' => 'hasEditOptionActionSchema',
        ],
        KeyValue::class => [
            'addButtonLabel' => 'addActionLabel',
            'deleteButtonLabel' => 'deleteActionLabel',
            'reorderButtonLabel' => 'reorderActionLabel',
        ],
        MarkdownEditor::class => [
            'getUploadedAttachmentUrlUsing' => 'getFileAttachmentUrlUsing',
            'saveUploadedFileAttachmentsUsing' => 'saveUploadedFileAttachmentUsing',
        ],
        RichEditor::class => [
            'getUploadedAttachmentUrlUsing' => 'getFileAttachmentUrlUsing',
            'saveUploadedFileAttachmentsUsing' => 'saveUploadedFileAttachmentUsing',
        ],
        MentionProvider::class => [
            'hasSearchResultsUsing' => 'hasSearchResultsUsingCallback',
        ],
        RichContentAttribute::class => [
            'getCustomBlocksConfig' => 'getCustomBlocksConfiguration',
        ],
        SpatieMediaLibraryFileUpload::class => [
            'getConversionsDisk' => 'getConversionsDiskName',
        ],
        MorphToSelectType::class => [
            'titleColumnName' => 'titleAttribute',
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
            'getParentTargetSizes' => 'getImageEditorViewportScaleFactor',
        ],
        Entry::class => [
            'getExtraEntryWrapperAttributesBag' => 'getExtraEntryWrapperAttributeBag',
        ],
        RepeatableEntry::class => [
            'getItemState' => 'getItemStateWithExistence',
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
            'reactive' => 'live',
        ],
        Component::class => [
            'getGetCallback' => 'makeGetUtility',
            'getSetCallback' => 'makeSetUtility',
            'getChildComponentContainer' => 'getChildSchema',
            'getChildComponentContainers' => 'getChildSchemas',
            'clearCachedDefaultChildSchemas' => 'clearCachedChildSchemas',
            'reactive' => 'live',
        ],
        Step::class => [
            'afterValidated' => 'afterValidation',
        ],
        Section::class => [
            'formBefore' => 'contentBefore',
            'isFormBefore' => 'isContentBefore',
        ],
        Get::class => [
            'skipComponentsChildContainersWhileSearching' => 'skipComponentsChildSchemasWhileSearching',
        ],
        Set::class => [
            'skipComponentsChildContainersWhileSearching' => 'skipComponentsChildSchemasWhileSearching',
        ],
        Notifications::class => [
            'pullNotificationsFromSession' => 'pullNotifications',
        ],
        ChartWidget::class => [
            'resetFiltersForm' => 'resetFilters',
        ],
        ViewComponent::class => [
            'disableLabel' => 'hiddenLabel',
            'postfix' => 'suffix',
        ],
        Tab::class => [
            'indicator' => 'badge',
            'indicatorColor' => 'badgeColor',
        ],
        Column::class => [
            'disableClick' => 'disabledClick',
            'queriesRelationships' => 'hasRelationship',
            'getGetStateUsingCallback' => 'getStateUsingCallback',
        ],
        IconColumn::class => [
            'options' => 'icons',
        ],
        Summarizer::class => [
            'hasQueryModification' => 'hasQueryModificationCallback',
        ],
        BaseFilter::class => [
            'form' => 'schema',
            'getFormSchema' => 'getSchemaComponents',
        ],
        SelectFilter::class => [
            'column' => 'attribute',
            'getColumn' => 'getAttribute',
            'queriesRelationships' => 'hasRelationship',
            'getRelationshipKey' => 'getQualifiedRelatedKeyNameForRelationship',
        ],
        Group::class => [
            'getDescriptionUsing' => 'getDescriptionFromRecordUsing',
        ],
        HasTable::class => [
            'updatedTableSortDirection' => 'updatedTableSort',
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
            'groupsInDropdownOnDesktop' => 'groupingSettingsInDropdownOnDesktop',
            'getInverseRelationship' => 'getInverseRelationshipName',
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

    public function __construct(
        private UnnormalizedUnionTypeResolver $unnormalizedUnionTypeResolver,
        private ConfigurationClassAnalyzer $configurationClassAnalyzer,
    ) {}

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
        return [MethodCall::class, NullsafeMethodCall::class, StaticCall::class];
    }

    /**
     * @param  MethodCall | NullsafeMethodCall | StaticCall  $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $node->name instanceof Identifier) {
            return null;
        }

        $methodName = strtolower($node->name->toString());
        $isGroupingDropdownAlias = $methodName === 'groupsindropdownondesktop';
        $isImageEditorScaleAlias = $methodName === 'getparenttargetsizes';

        foreach (self::METHOD_RENAMES as $componentClass => $methodRenames) {
            $methodRenames = array_change_key_case($methodRenames);

            if (! isset($methodRenames[$methodName])) {
                continue;
            }

            if ($node instanceof StaticCall) {
                // Review relative dispatch manually when a parent wrapper can
                // dispatch back into a canonical subclass override.
                if (! $node->class instanceof Name) {
                    continue;
                }

                if ($this->isNames($node->class, ['self', 'static', 'parent'])) {
                    $context = ScopeFetcher::fetch($node)->getClassReflection();

                    if (($componentClass !== BasePage::class) || (! $context) || (! $this->canRenameMethods(new ObjectType($context->getName()), $componentClass, $methodName, $methodRenames[$methodName]))) {
                        continue;
                    }

                    if ($this->configurationClassAnalyzer->hasDescendants($context->getName()) || (new NodeFinder)->findFirst($this->getFile()->getNewStmts(), fn (Node $candidate): bool => $candidate instanceof Node\Stmt\Class_ && $candidate->extends && $this->isName($candidate->extends, $context->getName()))) {
                        continue;
                    }

                    $receiver = $this->isName($node->class, 'parent') ? $context->getParentClass() : $context;

                    if (! $receiver) {
                        continue;
                    }

                    $receiverType = new ObjectType($receiver->getName());
                } elseif (($componentClass === FilamentManager::class) && $this->isName($node->class, Filament::class)) {
                    $receiverType = new ObjectType(FilamentManager::class);
                } elseif (in_array($componentClass, [Cluster::class, BasePage::class], true)) {
                    $receiverType = $this->getType($node->class);
                } else {
                    continue;
                }
            } else {
                // Preserve the older table migrations' receiver inference boundary.
                $receiverType = (($componentClass === Table::class) && (! $isGroupingDropdownAlias))
                    ? TypeCombinator::removeNull($this->getType($node->var))
                    : $this->resolveReceiverType($node->var);
            }

            if (! (new ObjectType($componentClass))->isSuperTypeOf($receiverType)->yes()) {
                continue;
            }

            // These protected helpers are only migrated through `$this`, not external receivers.
            if ((in_array($componentClass, [RichContentCustomBlockClassGenerator::class, Dashboard::class, RepeatableEntry::class], strict: true) || $isImageEditorScaleAlias || ($methodName === 'getjavascriptclickhandler')) && (($node instanceof StaticCall) || (! $node->var instanceof Variable) || ($node->var->name !== 'this'))) {
                return null;
            }

            $replacement = $methodRenames[$methodName];

            if ((! $this->canRenameMethods($receiverType, $componentClass, $methodName, $replacement)) || $this->hasOverridingUnion($componentClass, $methodName, $replacement) || (! $this->canForwardArguments($node, $componentClass, $methodName))) {
                return null;
            }

            // Filter `form()` already used the canonical `$schema` parameter name.
            $renamedArgument = (($componentClass === BaseFilter::class) && ($methodName === 'form'))
                ? null
                : (self::ARGUMENT_RENAMES[$methodName] ?? null);

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
    private function hasOverridingUnion(string $componentClass, string $methodName, string $replacement): bool
    {
        // PHPStan can normalize `Base | CustomBase` to `Base`, hiding overrides.
        // Conservatively retain related calls in a file declaring that union.
        foreach ($this->unnormalizedUnionTypeResolver->resolve($this->getFile()->getNewStmts()) as $types) {
            $ownerType = new ObjectType($componentClass);

            foreach ($types as $type) {
                if ((! $ownerType->isSuperTypeOf($type)->yes()) || (! array_filter($types, static fn (Type $otherType): bool => (! $otherType->equals($type)) && $otherType->isSuperTypeOf($type)->yes()))) {
                    continue;
                }

                if (! $this->canRenameMethods($type, $componentClass, $methodName, $replacement)) {
                    return true;
                }
            }

        }

        return false;
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

        // Record-action output also dispatches through the getter extension hook.
        if ($methodName === 'outputtableactions') {
            $candidateMethodNames[] = 'getTableActions';
            $candidateMethodNames[] = 'getTableRecordActions';
        }

        // Retain the older form, image, and table rules' reflection boundaries.
        $requiresPublicReplacement = ($methodName === 'groupsindropdownondesktop') || (! in_array($componentClass, [BaseFileUpload::class, Builder::class, Repeater::class, KeyValue::class, FileUpload::class, ImageEntry::class, ImageColumn::class, KeyValueEntry::class, Table::class], strict: true));

        foreach ($receiverType->getObjectClassReflections() as $classReflection) {
            foreach ($candidateMethodNames as $candidateMethodName) {
                if (! $classReflection->hasNativeMethod($candidateMethodName)) {
                    if ((! $requiresPublicReplacement) || ($candidateMethodName === $methodName) || ($candidateMethodName === 'getTableActions')) {
                        continue;
                    }

                    return false;
                }

                $methodReflection = $classReflection->getNativeMethod($candidateMethodName);

                if ($requiresPublicReplacement && (! $methodReflection->isPublic()) && (! ((in_array($componentClass, [RichContentCustomBlockClassGenerator::class, Dashboard::class, RepeatableEntry::class], strict: true) || ($methodName === 'getjavascriptclickhandler')) && (! $methodReflection->isPrivate())))) {
                    return false;
                }

                if (in_array($componentClass, [ViewComponent::class, HasTable::class, Dashboard::class, ChartWidget::class], strict: true)) {
                    $declaringTrait = match ($methodName) {
                        'postfix' => HasAffixes::class,
                        'updatedtablesortdirection' => CanSortRecords::class,
                        'normalizetablefiltervaluesfromquerystring' => HasFilters::class,
                        'resetfiltersform' => HasFiltersSchema::class,
                        default => HasLabel::class,
                    };

                    if ((! method_exists($methodReflection, 'getDeclaringTrait')) || ($methodReflection->getDeclaringTrait()?->getName() !== $declaringTrait)) {
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

                if (($componentClass === Action::class) && ($candidateMethodName === 'infolist') && method_exists($methodReflection, 'getDeclaringTrait') && ($methodReflection->getDeclaringTrait()?->getName() === 'Filament\\Actions\\Concerns\\HasInfolist')) {
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
    private function canForwardArguments(MethodCall | NullsafeMethodCall | StaticCall $node, string $componentClass, string $methodName): bool
    {
        // `reactive()` ignored positional arguments that `live()` would consume.
        if ($methodName === 'reactive') {
            return (! $node->isFirstClassCallable()) && (! count($node->getArgs()));
        }

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
