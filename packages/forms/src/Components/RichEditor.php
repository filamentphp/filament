<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\Actions\AttachFilesAction;
use Filament\Forms\Components\RichEditor\Actions\CustomBlockAction;
use Filament\Forms\Components\RichEditor\Actions\LinkAction;
use Filament\Forms\Components\RichEditor\EditorCommand;
use Filament\Forms\Components\RichEditor\FileAttachmentProviders\Contracts\FileAttachmentProvider;
use Filament\Forms\Components\RichEditor\Models\Contracts\HasRichContent;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\RichContentPlugin;
use Filament\Forms\Components\RichEditor\RichContentAttribute;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Filament\Forms\Components\RichEditor\RichEditorTool;
use Filament\Forms\Components\RichEditor\StateCasts\RichEditorStateCast;
use Filament\Schemas\Components\StateCasts\Contracts\StateCast;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Tiptap\Editor;

class RichEditor extends Field implements Contracts\CanBeLengthConstrained
{
    use Concerns\CanBeLengthConstrained;
    use Concerns\HasExtraInputAttributes;
    use Concerns\HasFileAttachments;
    use Concerns\HasPlaceholder;
    use Concerns\InteractsWithToolbarButtons;
    use HasExtraAlpineAttributes;

    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.rich-editor';

    protected string | Closure | null $uploadingFileMessage = null;

    protected bool | Closure | null $isJson = null;

    /**
     * @var array<RichContentPlugin | Closure>
     */
    protected array $plugins = [];

    /**
     * @var array<RichEditorTool | Closure>
     */
    protected array $tools = [];

    /**
     * @var array<string> | Closure | null
     */
    protected array | Closure | null $mergeTags = null;

    /**
     * @var array<class-string<RichContentCustomBlock>> | Closure | null
     */
    protected array | Closure | null $customBlocks = null;

    protected string | Closure | null $noMergeTagSearchResultsMessage = null;

    protected ?Closure $getFileAttachmentUrlFromAnotherRecordUsing = null;

    protected ?Closure $saveFileAttachmentFromAnotherRecordUsing = null;

    protected string | Closure | null $activePanel = null;

    /**
     * @var array<string, class-string<RichContentCustomBlock>>
     */
    protected array $cachedCustomBlocks;

    /**
     * @var array<string | array<string>> | Closure | null
     */
    protected array | Closure | null $floatingToolbars = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tools([
            RichEditorTool::make('bold')
                ->label(__('filament-forms::components.rich_editor.tools.bold'))
                ->jsHandler('$getEditor()?.chain().focus().toggleBold().run()')
                ->icon(Heroicon::Bold)
                ->iconAlias('forms:components.rich-editor.toolbar.bold'),
            RichEditorTool::make('italic')
                ->label(__('filament-forms::components.rich_editor.tools.italic'))
                ->jsHandler('$getEditor()?.chain().focus().toggleItalic().run()')
                ->icon(Heroicon::Italic)
                ->iconAlias('forms:components.rich-editor.toolbar.italic'),
            RichEditorTool::make('underline')
                ->label(__('filament-forms::components.rich_editor.tools.underline'))
                ->jsHandler('$getEditor()?.chain().focus().toggleUnderline().run()')
                ->icon(Heroicon::Underline)
                ->iconAlias('forms:components.rich-editor.toolbar.underline'),
            RichEditorTool::make('strike')
                ->label(__('filament-forms::components.rich_editor.tools.strike'))
                ->jsHandler('$getEditor()?.chain().focus().toggleStrike().run()')
                ->icon(Heroicon::Strikethrough)
                ->iconAlias('forms:components.rich-editor.toolbar.strike'),
            RichEditorTool::make('subscript')
                ->label(__('filament-forms::components.rich_editor.tools.subscript'))
                ->jsHandler('$getEditor()?.chain().focus().toggleSubscript().run()')
                ->icon('fi-o-subscript')
                ->iconAlias('forms:components.rich-editor.toolbar.subscript'),
            RichEditorTool::make('superscript')
                ->label(__('filament-forms::components.rich_editor.tools.superscript'))
                ->jsHandler('$getEditor()?.chain().focus().toggleSuperscript().run()')
                ->icon('fi-o-superscript')
                ->iconAlias('forms:components.rich-editor.toolbar.superscript'),
            RichEditorTool::make('link')
                ->label(__('filament-forms::components.rich_editor.tools.link'))
                ->action(arguments: '{ url: $getEditor().getAttributes(\'link\')?.href, shouldOpenInNewTab: $getEditor().getAttributes(\'link\')?.target === \'_blank\' }')
                ->icon(Heroicon::Link)
                ->iconAlias('forms:components.rich-editor.toolbar.link'),
            RichEditorTool::make('h1')
                ->label(__('filament-forms::components.rich_editor.tools.h1'))
                ->jsHandler('$getEditor()?.chain().focus().toggleHeading({ level: 1 }).run()')
                ->activeOptions(['level' => 1])
                ->icon(Heroicon::H1)
                ->iconAlias('forms:components.rich-editor.toolbar.h1'),
            RichEditorTool::make('h2')
                ->label(__('filament-forms::components.rich_editor.tools.h2'))
                ->jsHandler('$getEditor()?.chain().focus().toggleHeading({ level: 2 }).run()')
                ->activeOptions(['level' => 2])
                ->icon(Heroicon::H2)
                ->iconAlias('forms:components.rich-editor.toolbar.h2'),
            RichEditorTool::make('h3')
                ->label(__('filament-forms::components.rich_editor.tools.h3'))
                ->jsHandler('$getEditor()?.chain().focus().toggleHeading({ level: 3 }).run()')
                ->activeOptions(['level' => 3])
                ->icon(Heroicon::H3)
                ->iconAlias('forms:components.rich-editor.toolbar.h3'),
            RichEditorTool::make('blockquote')
                ->label(__('filament-forms::components.rich_editor.tools.blockquote'))
                ->jsHandler('$getEditor()?.chain().focus().toggleBlockquote().run()')
                ->icon(Heroicon::ChatBubbleBottomCenterText)
                ->iconAlias('forms:components.rich-editor.toolbar.blockquote'),
            RichEditorTool::make('code')
                ->label(__('filament-forms::components.rich_editor.tools.code'))
                ->jsHandler('$getEditor()?.chain().focus().toggleCode().run()')
                ->icon('fi-o-code')
                ->iconAlias('forms:components.rich-editor.toolbar.code'),
            RichEditorTool::make('codeBlock')
                ->label(__('filament-forms::components.rich_editor.tools.code_block'))
                ->jsHandler('$getEditor()?.chain().focus().toggleCodeBlock().run()')
                ->icon('fi-o-code-block')
                ->iconAlias('forms:components.rich-editor.toolbar.code-block'),
            RichEditorTool::make('bulletList')
                ->label(__('filament-forms::components.rich_editor.tools.bullet_list'))
                ->jsHandler('$getEditor()?.chain().focus().toggleBulletList().run()')
                ->icon(Heroicon::ListBullet)
                ->iconAlias('forms:components.rich-editor.toolbar.bullet-list'),
            RichEditorTool::make('orderedList')
                ->label(__('filament-forms::components.rich_editor.tools.ordered_list'))
                ->jsHandler('$getEditor()?.chain().focus().toggleOrderedList().run()')
                ->icon(Heroicon::NumberedList)
                ->iconAlias('forms:components.rich-editor.toolbar.ordered-list'),
            RichEditorTool::make('table')
                ->label(__('filament-forms::components.rich_editor.tools.table'))
                ->jsHandler('$getEditor()?.commands.insertTable({ rows: 2, cols: 3, withHeaderRow: true })')
                ->icon('fi-o-table')
                ->iconAlias('forms:components.rich-editor.toolbar.table'),
            RichEditorTool::make('tableAddColumnBefore')
                ->label(__('filament-forms::components.rich_editor.tools.table_add_column_before'))
                ->jsHandler('$getEditor()?.chain().focus().addColumnBefore().run()')
                ->icon('fi-o-table-add-column-before')
                ->iconAlias('forms:components.rich-editor.toolbar.table_add_column_before'),
            RichEditorTool::make('tableAddColumnAfter')
                ->label(__('filament-forms::components.rich_editor.tools.table_add_column_after'))
                ->jsHandler('$getEditor()?.chain().focus().addColumnAfter().run()')
                ->icon('fi-o-table-add-column-after')
                ->iconAlias('forms:components.rich-editor.toolbar.table_add_column_after'),
            RichEditorTool::make('tableDeleteColumn')
                ->label(__('filament-forms::components.rich_editor.tools.table_delete_column'))
                ->jsHandler('$getEditor()?.chain().focus().deleteColumn().run()')
                ->icon('fi-o-table-delete-column')
                ->iconAlias('forms:components.rich-editor.toolbar.table_delete_column'),
            RichEditorTool::make('tableAddRowBefore')
                ->label(__('filament-forms::components.rich_editor.tools.table_add_row_before'))
                ->jsHandler('$getEditor()?.chain().focus().addRowBefore().run()')
                ->icon('fi-o-table-add-row-before')
                ->iconAlias('forms:components.rich-editor.toolbar.table_add_row_before'),
            RichEditorTool::make('tableAddRowAfter')
                ->label(__('filament-forms::components.rich_editor.tools.table_add_row_after'))
                ->jsHandler('$getEditor()?.chain().focus().addRowAfter().run()')
                ->icon('fi-o-table-add-row-after')
                ->iconAlias('forms:components.rich-editor.toolbar.table_add_row_after'),
            RichEditorTool::make('tableDeleteRow')
                ->label(__('filament-forms::components.rich_editor.tools.table_delete_row'))
                ->jsHandler('$getEditor()?.chain().focus().deleteRow().run()')
                ->icon('fi-o-table-delete-row')
                ->iconAlias('forms:components.rich-editor.toolbar.table_delete_row'),
            RichEditorTool::make('tableMergeCells')
                ->label(__('filament-forms::components.rich_editor.tools.table_merge_cells'))
                ->jsHandler('$getEditor()?.chain().focus().mergeCells().run()')
                ->icon('fi-o-table-merge-cells')
                ->iconAlias('forms:components.rich-editor.toolbar.table_merge_cells'),
            RichEditorTool::make('tableSplitCell')
                ->label(__('filament-forms::components.rich_editor.tools.table_split_cell'))
                ->jsHandler('$getEditor()?.chain().focus().splitCell().run()')
                ->icon('fi-o-table-split-cell')
                ->iconAlias('forms:components.rich-editor.toolbar.table_split_cell'),
            RichEditorTool::make('tableToggleHeaderRow')
                ->label(__('filament-forms::components.rich_editor.tools.table_toggle_header_row'))
                ->jsHandler('$getEditor()?.chain().focus().toggleHeaderRow().run()')
                ->icon('fi-o-table-toggle-header-row')
                ->iconAlias('forms:components.rich-editor.toolbar.table_toggle_header_row'),
            RichEditorTool::make('tableDelete')
                ->label(__('filament-forms::components.rich_editor.tools.table_delete'))
                ->jsHandler('$getEditor()?.chain().focus().deleteTable().run()')
                ->icon('fi-o-table-delete')
                ->iconAlias('forms:components.rich-editor.toolbar.table_delete'),
            RichEditorTool::make('attachFiles')
                ->label(__('filament-forms::components.rich_editor.tools.attach_files'))
                ->action(arguments: '{ alt: $getEditor().getAttributes(\'image\')?.alt, id: $getEditor().getAttributes(\'image\')?.id, src: $getEditor().getAttributes(\'image\')?.src }')
                ->activeKey('image')
                ->icon(Heroicon::PaperClip)
                ->iconAlias('forms:components.rich-editor.toolbar.attach-files'),
            RichEditorTool::make('customBlocks')
                ->label(__('filament-forms::components.rich_editor.tools.custom_blocks'))
                ->jsHandler('togglePanel(\'customBlocks\')')
                ->activeJsExpression('isPanelActive(\'customBlocks\')')
                ->icon(Heroicon::SquaresPlus)
                ->iconAlias('forms:components.rich-editor.toolbar.custom-blocks'),
            RichEditorTool::make('mergeTags')
                ->label(__('filament-forms::components.rich_editor.tools.merge_tags'))
                ->jsHandler('togglePanel(\'mergeTags\')')
                ->activeJsExpression('isPanelActive(\'mergeTags\')')
                ->icon('fi-o-merge-tag')
                ->iconAlias('forms:components.rich-editor.toolbar.merge-tags'),
            RichEditorTool::make('horizontalRule')
                ->label(__('filament-forms::components.rich_editor.tools.horizontal_rule'))
                ->jsHandler('$getEditor()?.chain().focus().setHorizontalRule().run()')
                ->icon(Heroicon::Minus)
                ->iconAlias('forms:components.rich-editor.toolbar.horizontal-rule'),
            RichEditorTool::make('highlight')
                ->label(__('filament-forms::components.rich_editor.tools.highlight'))
                ->jsHandler('$getEditor()?.chain().focus().toggleHighlight().run()')
                ->icon('fi-o-highlight')
                ->iconAlias('forms:components.rich-editor.toolbar.highlight'),
            RichEditorTool::make('small')
                ->label(__('filament-forms::components.rich_editor.tools.small'))
                ->jsHandler('$getEditor()?.chain().focus().toggleSmall().run()')
                ->icon('fi-o-small')
                ->iconAlias('forms:components.rich-editor.toolbar.small'),
            RichEditorTool::make('lead')
                ->label(__('filament-forms::components.rich_editor.tools.lead'))
                ->jsHandler('$getEditor()?.chain().focus().toggleLead().run()')
                ->icon('fi-o-lead')
                ->iconAlias('forms:components.rich-editor.toolbar.lead'),
            RichEditorTool::make('undo')
                ->label(__('filament-forms::components.rich_editor.tools.undo'))
                ->jsHandler('$getEditor()?.chain().focus().undo().run()')
                ->icon(Heroicon::ArrowUturnLeft)
                ->iconAlias('forms:components.rich-editor.toolbar.undo'),
            RichEditorTool::make('redo')
                ->label(__('filament-forms::components.rich_editor.tools.redo'))
                ->jsHandler('$getEditor()?.chain().focus().redo().run()')
                ->icon(Heroicon::ArrowUturnRight)
                ->iconAlias('forms:components.rich-editor.toolbar.redo'),
            RichEditorTool::make('alignStart')
                ->label(__('filament-forms::components.rich_editor.tools.align_start'))
                ->jsHandler('$getEditor()?.chain().focus().setTextAlign(\'start\').run()')
                ->icon('fi-o-align-start')
                ->iconAlias('forms:components.rich-editor.toolbar.align-start'),
            RichEditorTool::make('alignCenter')
                ->label(__('filament-forms::components.rich_editor.tools.align_center'))
                ->jsHandler('$getEditor()?.chain().focus().setTextAlign(\'center\').run()')
                ->icon('fi-o-align-center')
                ->iconAlias('forms:components.rich-editor.toolbar.align-center'),
            RichEditorTool::make('alignEnd')
                ->label(__('filament-forms::components.rich_editor.tools.align_end'))
                ->jsHandler('$getEditor()?.chain().focus().setTextAlign(\'end\').run()')
                ->icon('fi-o-align-end')
                ->iconAlias('forms:components.rich-editor.toolbar.align-end'),
            RichEditorTool::make('alignJustify')
                ->label(__('filament-forms::components.rich_editor.tools.align_justify'))
                ->jsHandler('$getEditor()?.chain().focus().setTextAlign(\'justify\').run()')
                ->icon('fi-o-align-justify')
                ->iconAlias('forms:components.rich-editor.toolbar.align-justify'),
            RichEditorTool::make('details')
                ->label(__('filament-forms::components.rich_editor.tools.details'))
                ->jsHandler('$getEditor()?.chain().focus().setDetails().run()')
                ->icon('fi-o-details')
                ->iconAlias('forms:components.rich-editor.toolbar.details'),
            RichEditorTool::make('clearFormatting')
                ->label(__('filament-forms::components.rich_editor.tools.clear_formatting'))
                ->jsHandler('$getEditor()?.chain().focus().clearNodes().unsetAllMarks().run()')
                ->icon('fi-o-clear-formatting')
                ->iconAlias('forms:components.rich-editor.toolbar.clear_formatting'),
        ]);

        $this->beforeStateDehydrated(function (RichEditor $component, ?array $rawState, ?Model $record): void {
            $fileAttachmentProvider = $component->getFileAttachmentProvider();

            if ($fileAttachmentProvider?->isExistingRecordRequiredToSaveNewFileAttachments() && (! $record)) {
                return;
            }

            $fileAttachmentIds = [];

            $component->rawState(
                $component->getTipTapEditor()
                    ->setContent($rawState ?? [
                        'type' => 'doc',
                        'content' => [],
                    ])
                    ->descendants(function (object &$node) use ($component, &$fileAttachmentIds): void {
                        if ($node->type !== 'image') {
                            return;
                        }

                        if (blank($node->attrs->id ?? null)) {
                            return;
                        }

                        $attachment = $component->getUploadedFileAttachment($node->attrs->id);

                        if ($attachment) {
                            $node->attrs->id = $component->saveUploadedFileAttachment($attachment);
                            $node->attrs->src = $component->getFileAttachmentUrl($node->attrs->id);

                            $fileAttachmentIds[] = $node->attrs->id;

                            return;
                        }

                        if (filled($component->getFileAttachmentUrl($node->attrs->id))) {
                            $fileAttachmentIds[] = $node->attrs->id;

                            return;
                        }

                        $fileAttachmentIdFromAnotherRecord = $component->saveFileAttachmentFromAnotherRecord($node->attrs->id);

                        if (blank($fileAttachmentIdFromAnotherRecord)) {
                            $fileAttachmentIds[] = $node->attrs->id;

                            return;
                        }

                        $node->attrs->id = $fileAttachmentIdFromAnotherRecord;
                        $node->attrs->src = $component->getFileAttachmentUrl($fileAttachmentIdFromAnotherRecord) ?? $node->attrs->src ?? null;
                    })
                    ->getDocument(),
            );

            $fileAttachmentProvider?->cleanUpFileAttachments(exceptIds: $fileAttachmentIds);
        }, shouldUpdateValidatedStateAfter: true);

        $this->saveRelationshipsUsing(function (RichEditor $component, ?array $rawState, Model $record): void {
            $fileAttachmentProvider = $component->getFileAttachmentProvider();

            if (! $fileAttachmentProvider) {
                return;
            }

            if (! $fileAttachmentProvider->isExistingRecordRequiredToSaveNewFileAttachments()) {
                return;
            }

            if (! $record->wasRecentlyCreated) {
                return;
            }

            $fileAttachmentIds = [];

            $component->rawState(
                $component->getTipTapEditor()
                    ->setContent($rawState ?? [
                        'type' => 'doc',
                        'content' => [],
                    ])
                    ->descendants(function (object &$node) use ($component, &$fileAttachmentIds): void {
                        if ($node->type !== 'image') {
                            return;
                        }

                        if (blank($node->attrs->id ?? null)) {
                            return;
                        }

                        $attachment = $component->getUploadedFileAttachment($node->attrs->id);

                        if ($attachment) {
                            $node->attrs->id = $component->saveUploadedFileAttachment($attachment);
                            $node->attrs->src = $component->getFileAttachmentUrl($node->attrs->id);

                            $fileAttachmentIds[] = $node->attrs->id;

                            return;
                        }

                        if (filled($component->getFileAttachmentUrl($node->attrs->id))) {
                            $fileAttachmentIds[] = $node->attrs->id;

                            return;
                        }

                        $fileAttachmentIdFromAnotherRecord = $component->saveFileAttachmentFromAnotherRecord($node->attrs->id);

                        if (blank($fileAttachmentIdFromAnotherRecord)) {
                            $fileAttachmentIds[] = $node->attrs->id;

                            return;
                        }

                        $node->attrs->id = $fileAttachmentIdFromAnotherRecord;
                        $node->attrs->src = $component->getFileAttachmentUrl($fileAttachmentIdFromAnotherRecord) ?? $node->attrs->src ?? null;
                    })
                    ->getDocument(),
            );

            $record->setAttribute($component->getContentAttribute()->getName(), $component->getState());
            $record->save();

            $fileAttachmentProvider->cleanUpFileAttachments(exceptIds: $fileAttachmentIds);
        });
    }

    public function isDehydrated(): bool
    {
        if ($this->getFileAttachmentProvider()?->isExistingRecordRequiredToSaveNewFileAttachments() && (! $this->getRecord())) {
            return false;
        }

        return parent::isDehydrated();
    }

    /**
     * @param  array<RichContentPlugin> | Closure  $extensions
     */
    public function plugins(array | Closure $extensions): static
    {
        $this->plugins = [
            ...$this->plugins,
            ...is_array($extensions) ? $extensions : [$extensions],
        ];

        return $this;
    }

    /**
     * @param  array<RichEditorTool> | Closure  $tools
     */
    public function tools(array | Closure $tools): static
    {
        $this->tools = [
            ...$this->tools,
            ...is_array($tools) ? $tools : [$tools],
        ];

        return $this;
    }

    /**
     * @return array<StateCast>
     */
    public function getDefaultStateCasts(): array
    {
        return [
            ...parent::getDefaultStateCasts(),
            app(RichEditorStateCast::class, ['richEditor' => $this]),
        ];
    }

    /**
     * @param  array<EditorCommand>  $commands
     * @param  ?array<string, mixed>  $editorSelection
     */
    public function runCommands(array $commands, ?array $editorSelection = null): void
    {
        $key = $this->getKey();
        $livewire = $this->getLivewire();

        $livewire->dispatch(
            'run-rich-editor-commands',
            awaitSchemaComponent: $key,
            livewireId: $livewire->getId(),
            key: $key,
            editorSelection: $editorSelection,
            commands: array_map(fn (EditorCommand $command): array => $command->toArray(), $commands),
        );
    }

    public function uploadingFileMessage(string | Closure | null $message): static
    {
        $this->uploadingFileMessage = $message;

        return $this;
    }

    public function getUploadingFileMessage(): string
    {
        return $this->evaluate($this->uploadingFileMessage) ?? __('filament::components/button.messages.uploading_file');
    }

    public function json(bool | Closure | null $condition = true): static
    {
        $this->isJson = $condition;

        return $this;
    }

    public function isJson(): bool
    {
        return $this->evaluate($this->isJson) ?? $this->getContentAttribute()?->isJson() ?? false;
    }

    public function getTipTapEditor(): Editor
    {
        return RichContentRenderer::make()
            ->plugins($this->getPlugins())
            ->getEditor();
    }

    /**
     * @return array<RichContentPlugin>
     */
    public function getPlugins(): array
    {
        return [
            ...$this->getContentAttribute()?->getPlugins() ?? [],
            ...array_reduce(
                $this->plugins,
                function (array $carry, RichContentPlugin | Closure $plugin): array {
                    if ($plugin instanceof Closure) {
                        $plugin = $this->evaluate($plugin);
                    }

                    return [
                        ...$carry,
                        ...Arr::wrap($plugin),
                    ];
                },
                initial: [],
            ),
        ];
    }

    /**
     * @return array<string>
     */
    public function getTipTapJsExtensions(): array
    {
        return array_reduce(
            $this->getPlugins(),
            fn (array $carry, RichContentPlugin $plugin): array => [
                ...$carry,
                ...$plugin->getTipTapJsExtensions(),
            ],
            initial: [],
        );
    }

    /**
     * @return array<string, RichEditorTool>
     */
    public function getTools(): array
    {
        return array_reduce(
            [
                ...array_reduce(
                    $this->getPlugins(),
                    fn (array $carry, RichContentPlugin $plugin): array => [
                        ...$carry,
                        ...$plugin->getEditorTools(),
                    ],
                    initial: [],
                ),
                ...array_reduce(
                    $this->tools,
                    function (array $carry, RichEditorTool | Closure $tool): array {
                        if ($tool instanceof Closure) {
                            $tool = $this->evaluate($tool);
                        }

                        return [
                            ...$carry,
                            ...Arr::wrap($tool),
                        ];
                    },
                    initial: [],
                ),
            ],
            fn (array $carry, RichEditorTool $tool): array => [
                ...$carry,
                $tool->getName() => $tool->editor($this),
            ],
            initial: [],
        );
    }

    public function getContentAttribute(): ?RichContentAttribute
    {
        $model = $this->getModelInstance();

        if (! ($model instanceof HasRichContent)) {
            return null;
        }

        return $model->getRichContentAttribute($this->getName());
    }

    public function getDefaultFileAttachmentsDiskName(): ?string
    {
        return $this->getContentAttribute()?->getFileAttachmentsDiskName();
    }

    public function getDefaultFileAttachmentsVisibility(): ?string
    {
        return $this->getContentAttribute()?->getFileAttachmentsVisibility();
    }

    public function getFileAttachmentProvider(): ?FileAttachmentProvider
    {
        return $this->getContentAttribute()?->getFileAttachmentProvider();
    }

    public function getDefaultFileAttachmentUrl(mixed $file): ?string
    {
        return $this->getFileAttachmentProvider()?->getFileAttachmentUrl($file);
    }

    public function defaultSaveUploadedFileAttachment(TemporaryUploadedFile $file): mixed
    {
        return $this->getFileAttachmentProvider()?->saveUploadedFileAttachment($file);
    }

    /**
     * @return array<string, array<string>>
     */
    public function getDefaultFloatingToolbars(): array
    {
        return [
            'table' => [
                'tableAddColumnBefore', 'tableAddColumnAfter', 'tableDeleteColumn',
                'tableAddRowBefore', 'tableAddRowAfter', 'tableDeleteRow',
                'tableMergeCells', 'tableSplitCell',
                'tableToggleHeaderRow',
                'tableDelete',
            ],
        ];
    }

    /**
     * @return array<string | array<string>>
     */
    public function getDefaultToolbarButtons(): array
    {
        return [
            ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link'],
            ['h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd'],
            ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
            [
                'table',
                'attachFiles',
                ...(filled($this->getCustomBlocks()) ? ['customBlocks'] : []),
                ...(filled($this->getMergeTags()) ? ['mergeTags'] : []),
            ],
            ['undo', 'redo'],
        ];
    }

    public function getFileAttachmentUrlFromAnotherRecordUsing(?Closure $callback): static
    {
        $this->getFileAttachmentUrlFromAnotherRecordUsing = $callback;

        return $this;
    }

    public function saveFileAttachmentFromAnotherRecordUsing(?Closure $callback): static
    {
        $this->saveFileAttachmentFromAnotherRecordUsing = $callback;

        return $this;
    }

    public function getFileAttachmentUrlFromAnotherRecord(mixed $file): ?string
    {
        return $this->evaluate($this->getFileAttachmentUrlFromAnotherRecordUsing, [
            'file' => $file,
        ]);
    }

    public function saveFileAttachmentFromAnotherRecord(mixed $file): mixed
    {
        return $this->evaluate($this->saveFileAttachmentFromAnotherRecordUsing, [
            'file' => $file,
        ]);
    }

    /**
     * @return array<Action>
     */
    public function getDefaultActions(): array
    {
        return [
            AttachFilesAction::make(),
            CustomBlockAction::make(),
            LinkAction::make(),
            ...array_reduce(
                $this->getPlugins(),
                fn (array $carry, RichContentPlugin $plugin): array => [
                    ...$carry,
                    ...$plugin->getEditorActions(),
                ],
                initial: [],
            ),
        ];
    }

    /**
     * @param  array<string> | Closure | null  $tags
     */
    public function mergeTags(array | Closure | null $tags): static
    {
        $this->mergeTags = $tags;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getMergeTags(): array
    {
        return $this->evaluate($this->mergeTags) ?? $this->getContentAttribute()?->getMergeTags() ?? [];
    }

    public function noMergeTagSearchResultsMessage(string | Closure | null $message): static
    {
        $this->noMergeTagSearchResultsMessage = $message;

        return $this;
    }

    public function getNoMergeTagSearchResultsMessage(): string | Htmlable
    {
        return $this->evaluate($this->noMergeTagSearchResultsMessage) ?? __('filament-forms::components.rich_editor.no_merge_tag_search_results_message');
    }

    public function activePanel(string | Closure | null $panel): static
    {
        $this->activePanel = $panel;

        return $this;
    }

    public function getActivePanel(): ?string
    {
        return $this->evaluate($this->activePanel);
    }

    /**
     * @param  array<class-string<RichContentCustomBlock>> | Closure | null  $blocks
     */
    public function customBlocks(array | Closure | null $blocks): static
    {
        $this->customBlocks = $blocks;

        return $this;
    }

    /**
     * @return array<class-string<RichContentCustomBlock>>
     */
    public function getCustomBlocks(): array
    {
        return $this->evaluate($this->customBlocks) ?? $this->getContentAttribute()?->getCustomBlocks() ?? [];
    }

    /**
     * @return array<string, class-string<RichContentCustomBlock>>
     */
    public function getCachedCustomBlocks(): array
    {
        if (isset($this->cachedCustomBlocks)) {
            return $this->cachedCustomBlocks;
        }

        foreach ($this->getCustomBlocks() as $block) {
            $this->cachedCustomBlocks[$block::getId()] = $block;
        }

        return $this->cachedCustomBlocks;
    }

    /**
     * @return ?class-string<RichContentCustomBlock>
     */
    public function getCustomBlock(string $id): ?string
    {
        return $this->getCachedCustomBlocks()[$id] ?? null;
    }

    /**
     * @param  array<string | array<string>> | Closure | null  $toolbars
     */
    public function floatingToolbars(array | Closure | null $toolbars): static
    {
        $this->floatingToolbars = $toolbars;

        return $this;
    }

    /**
     * @return array<string, array<string>>
     */
    public function getFloatingToolbars(): array
    {
        return $this->evaluate($this->floatingToolbars) ?? $this->getDefaultFloatingToolbars();
    }

    public function getLengthValidationRules(): array
    {
        $rules = [];

        if (filled($maxLength = $this->getMaxLength())) {
            $rules[] = function (string $_attribute, mixed $value, Closure $fail) use ($maxLength): void {
                if (blank($value)) {
                    return;
                }

                $textLength = Str::length($this->getTipTapEditor()
                    ->setContent($value)
                    ->getText());

                if ($textLength > $maxLength) {
                    $fail('validation.max.string')->translate([
                        'max' => $maxLength,
                    ]);
                }
            };
        }

        if (filled($minLength = $this->getMinLength())) {
            $rules[] = function (string $_attribute, mixed $value, Closure $fail) use ($minLength): void {
                if (blank($value)) {
                    return;
                }

                $textLength = Str::length($this->getTipTapEditor()
                    ->setContent($value)
                    ->getText());

                if ($textLength < $minLength) {
                    $fail('validation.min.string')->translate([
                        'min' => $minLength,
                    ]);
                }
            };
        }

        if (filled($length = $this->getLength())) {
            $rules[] = function (string $_attribute, mixed $value, Closure $fail) use ($length): void {
                if (blank($value)) {
                    return;
                }

                $textLength = Str::length($this->getTipTapEditor()
                    ->setContent($value)
                    ->getText());

                if ($textLength !== $length) {
                    $fail('validation.size.string')->translate([
                        'size' => $length,
                    ]);
                }
            };
        }

        return $rules;
    }
}
