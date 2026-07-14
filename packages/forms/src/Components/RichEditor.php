<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\Actions\AttachFilesAction;
use Filament\Forms\Components\RichEditor\Actions\CustomBlockAction;
use Filament\Forms\Components\RichEditor\Actions\GridAction;
use Filament\Forms\Components\RichEditor\Actions\LinkAction;
use Filament\Forms\Components\RichEditor\Actions\TextColorAction;
use Filament\Forms\Components\RichEditor\EditorCommand;
use Filament\Forms\Components\RichEditor\FileAttachmentProviders\Contracts\FileAttachmentProvider;
use Filament\Forms\Components\RichEditor\MentionProvider;
use Filament\Forms\Components\RichEditor\Models\Contracts\HasRichContent;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\HasToolbarButtons;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\RichContentPlugin;
use Filament\Forms\Components\RichEditor\RichContentAttribute;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Filament\Forms\Components\RichEditor\RichEditorTool;
use Filament\Forms\Components\RichEditor\StateCasts\RichEditorStateCast;
use Filament\Forms\Components\RichEditor\TextColor;
use Filament\Forms\Components\RichEditor\ToolbarButtonGroup;
use Filament\Forms\View\FormsIconAlias;
use Filament\Schemas\Components\StateCasts\Contracts\StateCast;
use Filament\Support\Colors\Color;
use Filament\Support\Components\Attributes\ExposedLivewireMethod;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Icons\Heroicon;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Js;
use Illuminate\Support\Str;
use Livewire\Attributes\Renderless;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use LogicException;
use Tiptap\Editor;

use function Filament\Support\generate_icon_html;
use function Filament\Support\generate_loading_indicator_html;

class RichEditor extends Field implements Contracts\CanBeLengthConstrained, HasEmbeddedView
{
    // Security: The rich editor outputs raw HTML. Attackers can intercept
    // the value and send arbitrary HTML to the backend. When rendering
    // in Blade views, always sanitize using `sanitizeHtml()` or the
    // `RichContentRenderer`. Never use `{!! $content !!}` unsanitized.
    // The default sanitizer permits inline `style` attributes —
    // configure a restrictive one for untrusted user content.

    use Concerns\CanBeLengthConstrained;
    use Concerns\HasExtraInputAttributes;
    use Concerns\HasFileAttachments;
    use Concerns\HasPlaceholder;
    use Concerns\InteractsWithToolbarButtons {
        Concerns\InteractsWithToolbarButtons::getToolbarButtons as getBaseToolbarButtons;
    }
    use HasExtraAlpineAttributes;

    protected ?string $publishedViewOverrideCheckPath = 'filament-forms::components.rich-editor';

    protected string | Closure | null $uploadingFileMessage = null;

    /**
     * @var array<string> | Closure
     */
    protected array | Closure $linkProtocols = ['http', 'https', 'ftp', 'ftps', 'mailto', 'tel', 'callto', 'sms', 'cid', 'xmpp'];

    protected bool | Closure | null $isJson = null;

    /**
     * @var array<RichContentPlugin | Closure>
     */
    protected array $plugins = [];

    /**
     * @var array<RichContentPlugin> | null
     */
    protected ?array $cachedPlugins = null;

    /**
     * @var array<RichEditorTool | Closure>
     */
    protected array $tools = [];

    /**
     * @var array<string, RichEditorTool> | null
     */
    protected ?array $cachedTools = null;

    /**
     * @var array<string> | Closure | null
     */
    protected array | Closure | null $mergeTags = null;

    /**
     * @var array<MentionProvider> | Closure | null
     */
    protected array | Closure | null $mentions = null;

    /**
     * @var array<class-string<RichContentCustomBlock>> | Closure | null
     */
    protected array | Closure | null $customBlocks = null;

    protected string | Closure | null $noMergeTagSearchResultsMessage = null;

    protected ?Closure $getFileAttachmentUrlFromAnotherRecordUsing = null;

    protected ?Closure $saveFileAttachmentFromAnotherRecordUsing = null;

    protected bool | Closure $shouldPreventFileAttachmentPathTampering = false;

    protected ?Closure $allowFileAttachmentPathUsing = null;

    protected string | Closure | null $activePanel = null;

    /**
     * @var array<string, class-string<RichContentCustomBlock>>
     */
    protected array $cachedCustomBlocks;

    /**
     * @var array<string | array<string>> | Closure | null
     */
    protected array | Closure | null $floatingToolbars = null;

    /**
     * @var array<string, string | TextColor> | Closure | null
     */
    protected array | Closure | null $textColors = null;

    protected bool | Closure | null $hasCustomTextColors = null;

    protected bool | Closure | null $hasResizableImages = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tools([
            RichEditorTool::make('bold')
                ->label(__('filament-forms::components.rich_editor.tools.bold'))
                ->jsHandler('$getEditor()?.chain().focus().toggleBold().run()')
                ->toggle()
                ->icon(Heroicon::Bold)
                ->iconAlias('forms:components.rich-editor.toolbar.bold'),
            RichEditorTool::make('italic')
                ->label(__('filament-forms::components.rich_editor.tools.italic'))
                ->jsHandler('$getEditor()?.chain().focus().toggleItalic().run()')
                ->toggle()
                ->icon(Heroicon::Italic)
                ->iconAlias('forms:components.rich-editor.toolbar.italic'),
            RichEditorTool::make('underline')
                ->label(__('filament-forms::components.rich_editor.tools.underline'))
                ->jsHandler('$getEditor()?.chain().focus().toggleUnderline().run()')
                ->toggle()
                ->icon(Heroicon::Underline)
                ->iconAlias('forms:components.rich-editor.toolbar.underline'),
            RichEditorTool::make('strike')
                ->label(__('filament-forms::components.rich_editor.tools.strike'))
                ->jsHandler('$getEditor()?.chain().focus().toggleStrike().run()')
                ->toggle()
                ->icon(Heroicon::Strikethrough)
                ->iconAlias('forms:components.rich-editor.toolbar.strike'),
            RichEditorTool::make('subscript')
                ->label(__('filament-forms::components.rich_editor.tools.subscript'))
                ->jsHandler('$getEditor()?.chain().focus().toggleSubscript().run()')
                ->toggle()
                ->icon('fi-o-subscript')
                ->iconAlias('forms:components.rich-editor.toolbar.subscript'),
            RichEditorTool::make('superscript')
                ->label(__('filament-forms::components.rich_editor.tools.superscript'))
                ->jsHandler('$getEditor()?.chain().focus().toggleSuperscript().run()')
                ->toggle()
                ->icon('fi-o-superscript')
                ->iconAlias('forms:components.rich-editor.toolbar.superscript'),
            RichEditorTool::make('link')
                ->label(__('filament-forms::components.rich_editor.tools.link'))
                ->action(arguments: '{ url: $getEditor().getAttributes(\'link\')?.href, shouldOpenInNewTab: $getEditor().getAttributes(\'link\')?.target === \'_blank\' }')
                ->toggle()
                ->icon(Heroicon::Link)
                ->iconAlias('forms:components.rich-editor.toolbar.link'),
            RichEditorTool::make('textColor')
                ->label(__('filament-forms::components.rich_editor.tools.text_color'))
                ->action(arguments: '{ color: $getEditor().getAttributes(\'textColor\')[\'data-color\'] ?? null }')
                ->toggle()
                ->icon(Heroicon::Swatch)
                ->iconAlias('forms:components.rich-editor.toolbar.text-color'),
            RichEditorTool::make('h1')
                ->label(__('filament-forms::components.rich_editor.tools.h1'))
                ->jsHandler('$getEditor()?.chain().focus().toggleHeading({ level: 1 }).run()')
                ->toggle()
                ->activeKey('heading')
                ->activeOptions(['level' => 1])
                ->icon('fi-o-h1')
                ->iconAlias('forms:components.rich-editor.toolbar.h1'),
            RichEditorTool::make('h2')
                ->label(__('filament-forms::components.rich_editor.tools.h2'))
                ->jsHandler('$getEditor()?.chain().focus().toggleHeading({ level: 2 }).run()')
                ->toggle()
                ->activeKey('heading')
                ->activeOptions(['level' => 2])
                ->icon('fi-o-h2')
                ->iconAlias('forms:components.rich-editor.toolbar.h2'),
            RichEditorTool::make('h3')
                ->label(__('filament-forms::components.rich_editor.tools.h3'))
                ->jsHandler('$getEditor()?.chain().focus().toggleHeading({ level: 3 }).run()')
                ->toggle()
                ->activeKey('heading')
                ->activeOptions(['level' => 3])
                ->icon('fi-o-h3')
                ->iconAlias('forms:components.rich-editor.toolbar.h3'),
            RichEditorTool::make('paragraph')
                ->label(__('filament-forms::components.rich_editor.tools.paragraph'))
                ->jsHandler('$getEditor()?.chain().focus().setParagraph().run()')
                ->toggle()
                ->icon('fi-o-paragraph')
                ->iconAlias('forms:components.rich-editor.toolbar.paragraph'),
            RichEditorTool::make('h4')
                ->label(__('filament-forms::components.rich_editor.tools.h4'))
                ->jsHandler('$getEditor()?.chain().focus().toggleHeading({ level: 4 }).run()')
                ->toggle()
                ->activeKey('heading')
                ->activeOptions(['level' => 4])
                ->icon('fi-o-h4')
                ->iconAlias('forms:components.rich-editor.toolbar.h4'),
            RichEditorTool::make('h5')
                ->label(__('filament-forms::components.rich_editor.tools.h5'))
                ->jsHandler('$getEditor()?.chain().focus().toggleHeading({ level: 5 }).run()')
                ->toggle()
                ->activeKey('heading')
                ->activeOptions(['level' => 5])
                ->icon('fi-o-h5')
                ->iconAlias('forms:components.rich-editor.toolbar.h5'),
            RichEditorTool::make('h6')
                ->label(__('filament-forms::components.rich_editor.tools.h6'))
                ->jsHandler('$getEditor()?.chain().focus().toggleHeading({ level: 6 }).run()')
                ->toggle()
                ->activeKey('heading')
                ->activeOptions(['level' => 6])
                ->icon('fi-o-h6')
                ->iconAlias('forms:components.rich-editor.toolbar.h6'),
            RichEditorTool::make('blockquote')
                ->label(__('filament-forms::components.rich_editor.tools.blockquote'))
                ->jsHandler('$getEditor()?.chain().focus().toggleBlockquote().run()')
                ->toggle()
                ->icon(Heroicon::ChatBubbleBottomCenterText)
                ->iconAlias('forms:components.rich-editor.toolbar.blockquote'),
            RichEditorTool::make('code')
                ->label(__('filament-forms::components.rich_editor.tools.code'))
                ->jsHandler('$getEditor()?.chain().focus().toggleCode().run()')
                ->toggle()
                ->icon('fi-o-code')
                ->iconAlias('forms:components.rich-editor.toolbar.code'),
            RichEditorTool::make('codeBlock')
                ->label(__('filament-forms::components.rich_editor.tools.code_block'))
                ->jsHandler('$getEditor()?.chain().focus().toggleCodeBlock().run()')
                ->toggle()
                ->icon('fi-o-code-block')
                ->iconAlias('forms:components.rich-editor.toolbar.code-block'),
            RichEditorTool::make('bulletList')
                ->label(__('filament-forms::components.rich_editor.tools.bullet_list'))
                ->jsHandler('$getEditor()?.chain().focus().toggleBulletList().run()')
                ->toggle()
                ->icon(Heroicon::ListBullet)
                ->iconAlias('forms:components.rich-editor.toolbar.bullet-list'),
            RichEditorTool::make('orderedList')
                ->label(__('filament-forms::components.rich_editor.tools.ordered_list'))
                ->jsHandler('$getEditor()?.chain().focus().toggleOrderedList().run()')
                ->toggle()
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
            RichEditorTool::make('tableToggleHeaderCell')
                ->label(__('filament-forms::components.rich_editor.tools.table_toggle_header_cell'))
                ->jsHandler('$getEditor()?.chain().focus().toggleHeaderCell().run()')
                ->icon('fi-o-table-toggle-header-cell')
                ->iconAlias('forms:components.rich-editor.toolbar.table_toggle_header_cell'),
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
                ->toggle()
                ->icon('fi-o-highlight')
                ->iconAlias('forms:components.rich-editor.toolbar.highlight'),
            RichEditorTool::make('small')
                ->label(__('filament-forms::components.rich_editor.tools.small'))
                ->jsHandler('$getEditor()?.chain().focus().toggleSmall().run()')
                ->toggle()
                ->icon('fi-o-small')
                ->iconAlias('forms:components.rich-editor.toolbar.small'),
            RichEditorTool::make('lead')
                ->label(__('filament-forms::components.rich_editor.tools.lead'))
                ->jsHandler('$getEditor()?.chain().focus().toggleLead().run()')
                ->toggle()
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
                ->activeJsExpression('$getEditor()?.isActive({ textAlign: \'start\' })')
                ->toggle()
                ->icon('fi-o-align-start')
                ->iconAlias('forms:components.rich-editor.toolbar.align-start'),
            RichEditorTool::make('alignCenter')
                ->label(__('filament-forms::components.rich_editor.tools.align_center'))
                ->jsHandler('$getEditor()?.chain().focus().setTextAlign(\'center\').run()')
                ->activeJsExpression('$getEditor()?.isActive({ textAlign: \'center\' })')
                ->toggle()
                ->icon('fi-o-align-center')
                ->iconAlias('forms:components.rich-editor.toolbar.align-center'),
            RichEditorTool::make('alignEnd')
                ->label(__('filament-forms::components.rich_editor.tools.align_end'))
                ->jsHandler('$getEditor()?.chain().focus().setTextAlign(\'end\').run()')
                ->activeJsExpression('$getEditor()?.isActive({ textAlign: \'end\' })')
                ->toggle()
                ->icon('fi-o-align-end')
                ->iconAlias('forms:components.rich-editor.toolbar.align-end'),
            RichEditorTool::make('alignJustify')
                ->label(__('filament-forms::components.rich_editor.tools.align_justify'))
                ->jsHandler('$getEditor()?.chain().focus().setTextAlign(\'justify\').run()')
                ->activeJsExpression('$getEditor()?.isActive({ textAlign: \'justify\' })')
                ->toggle()
                ->icon('fi-o-align-justify')
                ->iconAlias('forms:components.rich-editor.toolbar.align-justify'),
            RichEditorTool::make('grid')
                ->label(__('filament-forms::components.rich_editor.tools.grid'))
                ->action()
                ->activeJsExpression('false')
                ->icon('fi-o-columns')
                ->iconAlias('forms:components.rich-editor.toolbar.grid'),
            RichEditorTool::make('gridDelete')
                ->label(__('filament-forms::components.rich_editor.tools.grid_delete'))
                ->jsHandler('$getEditor()?.chain().focus().deleteNode(\'grid\').run()')
                ->activeKey('grid')
                ->activeStyling(false)
                ->disabledWhenNotActive()
                ->icon('fi-o-columns-delete')
                ->iconAlias('forms:components.rich-editor.toolbar.grid_delete'),
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

        $this->beforeStateDehydrated(static function (RichEditor $component): void {
            $component->saveFileAttachments();
        }, shouldUpdateValidatedStateAfter: true);

        $this->saveRelationshipsUsing(static function (RichEditor $component): void {
            $component->saveFileAttachmentsToRecord();
        });

        $this->rule(static function (RichEditor $component): Closure {
            return static function (string $attribute, mixed $value, Closure $fail) use ($component): void {
                if (blank($value)) {
                    return;
                }

                $originalPaths = $component->getOriginalFileAttachmentPaths();
                $attachmentIds = [];

                $component->getTipTapEditor()
                    ->setContent($value)
                    ->descendants(function (object $node) use (&$attachmentIds): void {
                        if ($node->type !== 'image') {
                            return;
                        }

                        $id = $node->attrs->id ?? null;

                        if (blank($id)) {
                            return;
                        }

                        $attachmentIds[] = $id;
                    });

                foreach ($attachmentIds as $id) {
                    if ($component->getUploadedFileAttachment($id) !== null) {
                        continue;
                    }

                    if ($component->isFileAttachmentPathAuthorized($id, $originalPaths)) {
                        continue;
                    }

                    $fail(__($component->getValidationMessages()['tampered'] ?? 'filament-forms::validation.tampered_file_path', ['attribute' => $component->getValidationAttribute()]));

                    return;
                }
            };
        }, static fn (RichEditor $component): bool => $component->shouldPreventFileAttachmentPathTampering());
    }

    /**
     * @return array<string>
     */
    public function resolveFileAttachmentIds(): array
    {
        $fileAttachmentIds = [];

        $this->rawState(
            $this->getTipTapEditor()
                ->setContent($this->getRawState() ?? [
                    'type' => 'doc',
                    'content' => [],
                ])
                ->descendants(function (object &$node) use (&$fileAttachmentIds): void {
                    if ($node->type !== 'image') {
                        return;
                    }

                    if (blank($node->attrs->id ?? null)) {
                        return;
                    }

                    $attachment = $this->getUploadedFileAttachment($node->attrs->id);

                    if ($attachment) {
                        $node->attrs->id = $this->saveUploadedFileAttachment($attachment);
                        $node->attrs->src = $this->getFileAttachmentUrl($node->attrs->id);

                        $fileAttachmentIds[] = $node->attrs->id;

                        return;
                    }

                    if (filled($this->getFileAttachmentUrl($node->attrs->id))) {
                        $fileAttachmentIds[] = $node->attrs->id;

                        return;
                    }

                    $fileAttachmentIdFromAnotherRecord = $this->saveFileAttachmentFromAnotherRecord($node->attrs->id);

                    if (blank($fileAttachmentIdFromAnotherRecord)) {
                        $fileAttachmentIds[] = $node->attrs->id;

                        return;
                    }

                    $node->attrs->id = $fileAttachmentIdFromAnotherRecord;
                    $node->attrs->src = $this->getFileAttachmentUrl($fileAttachmentIdFromAnotherRecord) ?? $node->attrs->src ?? null;
                })
                ->getDocument(),
        );

        return $fileAttachmentIds;
    }

    public function preventFileAttachmentPathTampering(bool | Closure $condition = true, ?Closure $allowFilePathUsing = null): static
    {
        $this->shouldPreventFileAttachmentPathTampering = $condition;
        $this->allowFileAttachmentPathUsing = $allowFilePathUsing;

        return $this;
    }

    public function shouldPreventFileAttachmentPathTampering(): bool
    {
        return (bool) $this->evaluate($this->shouldPreventFileAttachmentPathTampering);
    }

    /**
     * @param  array<string> | null  $originalPaths
     */
    public function isFileAttachmentPathAuthorized(string $file, ?array $originalPaths = null): bool
    {
        if (in_array($file, $originalPaths ?? $this->getOriginalFileAttachmentPaths(), strict: true)) {
            return true;
        }

        if ($this->allowFileAttachmentPathUsing) {
            return (bool) $this->evaluate($this->allowFileAttachmentPathUsing, [
                'file' => $file,
            ]);
        }

        return false;
    }

    /**
     * @return array<string>
     */
    public function getOriginalFileAttachmentPaths(): array
    {
        $record = $this->getRecord();

        if (! $record instanceof Model) {
            return [];
        }

        $attribute = $this->getName();

        $originalContent = $record->getOriginal($attribute, $record->getAttribute($attribute));

        if (blank($originalContent)) {
            return [];
        }

        $ids = [];

        $this->getTipTapEditor()
            ->setContent($originalContent)
            ->descendants(function (object $node) use (&$ids): void {
                if ($node->type !== 'image') {
                    return;
                }

                if (blank($node->attrs->id ?? null)) {
                    return;
                }

                $ids[] = $node->attrs->id;
            });

        return $ids;
    }

    public function saveFileAttachments(): void
    {
        $fileAttachmentProvider = $this->getFileAttachmentProvider();

        if ($fileAttachmentProvider?->isExistingRecordRequiredToSaveNewFileAttachments() && (! $this->getRecord())) {
            return;
        }

        $fileAttachmentIds = $this->resolveFileAttachmentIds();

        $fileAttachmentProvider?->cleanUpFileAttachments(exceptIds: $fileAttachmentIds);
    }

    public function saveFileAttachmentsToRecord(): void
    {
        $fileAttachmentProvider = $this->getFileAttachmentProvider();

        if (! $fileAttachmentProvider) {
            return;
        }

        if (! $fileAttachmentProvider->isExistingRecordRequiredToSaveNewFileAttachments()) {
            return;
        }

        $record = $this->getRecord();

        if (! $record->wasRecentlyCreated) {
            return;
        }

        $fileAttachmentIds = $this->resolveFileAttachmentIds();

        $record->setAttribute($this->getContentAttribute()->getName(), $this->getState());
        $record->save();

        $fileAttachmentProvider->cleanUpFileAttachments(exceptIds: $fileAttachmentIds);
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

        $this->cachedPlugins = null;
        $this->cachedTools = null;

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

        $this->cachedTools = null;

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
        return $this->evaluate($this->uploadingFileMessage) ?? __('filament-forms::components.rich_editor.uploading_file_message');
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
            ->linkProtocols($this->getLinkProtocols())
            ->getEditor();
    }

    /**
     * @param  array<string> | Closure  $protocols
     */
    public function linkProtocols(array | Closure $protocols): static
    {
        $this->linkProtocols = $protocols;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getLinkProtocols(): array
    {
        return $this->evaluate($this->linkProtocols);
    }

    /**
     * @return array<RichContentPlugin>
     */
    public function getPlugins(): array
    {
        return $this->cachedPlugins ??= [
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
        return $this->cachedTools ??= array_reduce(
            [
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
                ...array_reduce(
                    $this->getPlugins(),
                    fn (array $carry, RichContentPlugin $plugin): array => [
                        ...$carry,
                        ...$plugin->getEditorTools(),
                    ],
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

    /**
     * @return array<array<string | ToolbarButtonGroup>>
     */
    public function getToolbarButtons(): array
    {
        $groups = $this->getBaseToolbarButtons();
        $tools = $this->getTools();

        return array_map(
            fn (array $group): array => array_map(
                fn (string | ToolbarButtonGroup $item): string | ToolbarButtonGroup => $item instanceof ToolbarButtonGroup
                    ? $item->resolve($tools)
                    : $item,
                $group,
            ),
            $groups,
        );
    }

    public function getContentAttribute(): ?RichContentAttribute
    {
        // Do not read content attributes from the model when the
        // rich editor is nested inside a custom block action
        // modal — the content attribute should only be used
        // to configure the parent rich editor.
        if ($this->getRootContainer()->getOperation() === CustomBlockAction::NAME) {
            return null;
        }

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
                'tableToggleHeaderRow', 'tableToggleHeaderCell',
                'tableDelete',
            ],
        ];
    }

    /**
     * @return array<array{type: string, buttons?: array<string | array<string | array<string>>>}>
     */
    protected function getExtraToolbarButtonsModifications(): array
    {
        $modifications = [];

        foreach ($this->getPlugins() as $plugin) {
            if (! ($plugin instanceof HasToolbarButtons)) {
                continue;
            }

            $enabledButtons = $plugin->getEnabledToolbarButtons();

            if (filled($enabledButtons)) {
                $modifications[] = [
                    'type' => 'enable',
                    'buttons' => $enabledButtons,
                ];
            }

            $disabledButtons = $plugin->getDisabledToolbarButtons();

            if (filled($disabledButtons)) {
                $modifications[] = [
                    'type' => 'disable',
                    'buttons' => $disabledButtons,
                ];
            }
        }

        return $modifications;
    }

    protected function hasToolbarButtonInItem(object $item, string $button): bool
    {
        if ($item instanceof ToolbarButtonGroup) {
            return in_array($button, $item->getButtons());
        }

        return false;
    }

    /**
     * @param  array<string>  $buttonsToDisable
     */
    protected function filterDisabledToolbarButtonsFromItem(object $item, array $buttonsToDisable): ?object
    {
        if (! ($item instanceof ToolbarButtonGroup)) {
            return $item;
        }

        $buttons = array_values(array_filter(
            $item->getButtons(),
            static fn (string $button): bool => ! in_array($button, $buttonsToDisable),
        ));

        if (blank($buttons)) {
            return null;
        }

        $item = clone $item;
        $item->buttons($buttons);

        return $item;
    }

    /**
     * @return array<string | array<string>>
     */
    public function getDefaultToolbarButtons(): array
    {
        return [
            ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link'],
            ['h2', 'h3'],
            ['alignStart', 'alignCenter', 'alignEnd'],
            ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
            [
                'table',
                ...($this->hasFileAttachments(default: true) ? ['attachFiles'] : []),
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
            GridAction::make(),
            LinkAction::make(),
            TextColorAction::make(),
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
     * @return array<string, string>
     */
    public function getMergeTags(): array
    {
        $mergeTags = $this->evaluate($this->mergeTags) ?? $this->getContentAttribute()?->getMergeTags() ?? [];

        return Arr::mapWithKeys(
            $mergeTags,
            fn (string $label, int | string $id): array => [(is_string($id) ? $id : $label) => $label],
        );
    }

    /**
     * @param  array<MentionProvider> | Closure  $providers
     */
    public function mentions(array | Closure $providers): static
    {
        $this->mentions = $providers;

        return $this;
    }

    /**
     * @return array<MentionProvider>
     */
    public function getMentionProviders(): array
    {
        return [
            ...($this->getContentAttribute()?->getMentionProviders() ?? []),
            ...($this->evaluate($this->mentions) ?? []),
        ];
    }

    /**
     * @return array<int, array{char: string, extraAttributes: array<string, mixed>, isSearchable: bool, items: array<string, string>, noOptionsMessage: string, noSearchResultsMessage: string, searchPrompt: string, searchingMessage: string}>
     */
    public function getMentionsForJs(): array
    {
        return array_map(
            function (MentionProvider $provider): array {
                return [
                    'char' => $provider->getChar(),
                    'extraAttributes' => $provider->getExtraAttributes(),
                    'isSearchable' => $provider->hasSearchResultsUsing(),
                    'items' => $provider->getItems(),
                    'noOptionsMessage' => $provider->getNoItemsMessage(),
                    'noSearchResultsMessage' => $provider->getNoSearchResultsMessage(),
                    'searchPrompt' => $provider->getSearchPrompt(),
                    'searchingMessage' => $provider->getSearchingMessage(),
                ];
            },
            $this->getMentionProviders(),
        );
    }

    /**
     * @return array<mixed>
     */
    #[ExposedLivewireMethod]
    #[Renderless]
    public function getMentionSearchResultsForJs(?string $search = null, ?string $char = '@'): array
    {
        $char = $char ?? '@';

        $providers = $this->getMentionProviders();

        $provider = collect($providers)->first(function (MentionProvider $mentionProvider) use ($char): bool {
            return $mentionProvider->getChar() === $char;
        }) ?? ($providers[0] ?? null);

        if (! $provider) {
            return [];
        }

        return $provider->getSearchResults($search ?? '');
    }

    /**
     * @param  array<array{id: mixed, char: string}>  $mentions
     * @return array<mixed, string>
     */
    #[ExposedLivewireMethod]
    #[Renderless]
    public function getMentionLabelsForJs(array $mentions = []): array
    {
        $providers = $this->getMentionProviders();
        $labels = [];

        $mentionsByChar = collect($mentions)->groupBy('char');

        foreach ($mentionsByChar as $char => $charMentions) {
            $provider = collect($providers)->first(function (MentionProvider $mentionProvider) use ($char): bool {
                return $mentionProvider->getChar() === $char;
            }) ?? ($providers[0] ?? null);

            if (! $provider) {
                continue;
            }

            $ids = $charMentions->pluck('id')->all();
            $charLabels = $provider->getLabels($ids);

            foreach ($charLabels as $id => $label) {
                $labels[$id] = $label;
            }
        }

        return $labels;
    }

    public function hasMentions(): bool
    {
        return isset($this->mentions);
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
     * @param  array<class-string<RichContentCustomBlock> | array<class-string<RichContentCustomBlock>>> | Closure | null  $blocks
     */
    public function customBlocks(array | Closure | null $blocks): static
    {
        $this->customBlocks = $blocks;

        return $this;
    }

    /**
     * @return array<class-string<RichContentCustomBlock> | array<class-string<RichContentCustomBlock>>>
     */
    protected function resolveCustomBlocks(): array
    {
        return $this->evaluate($this->customBlocks) ?? $this->getContentAttribute()?->getCustomBlocksConfig() ?? [];
    }

    /**
     * @return array<class-string<RichContentCustomBlock>>
     */
    public function getCustomBlocks(): array
    {
        $blocks = $this->resolveCustomBlocks();
        $result = [];

        foreach ($blocks as $value) {
            if (is_array($value)) {
                foreach ($value as $innerKey => $innerValue) {
                    $result[] = is_string($innerKey) ? $innerKey : $innerValue;
                }
            } else {
                $result[] = $value;
            }
        }

        return $result;
    }

    /**
     * @return array<string, class-string<RichContentCustomBlock>>
     */
    public function getCachedCustomBlocks(): array
    {
        if (isset($this->cachedCustomBlocks)) {
            return $this->cachedCustomBlocks;
        }

        $this->cachedCustomBlocks = [];

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
     * @return Collection<string, Collection<int, class-string<RichContentCustomBlock>>>
     */
    public function getGroupedCustomBlocks(): Collection
    {
        $blocks = $this->resolveCustomBlocks();
        $ungrouped = [];
        $groups = collect();

        foreach ($blocks as $key => $value) {
            if (is_string($key) && is_array($value)) {
                $groupBlocks = [];

                foreach ($value as $innerKey => $innerValue) {
                    $groupBlocks[] = is_string($innerKey) ? $innerKey : $innerValue;
                }

                $groups->put($key, collect($groupBlocks));
            } elseif (is_array($value)) {
                foreach ($value as $innerKey => $innerValue) {
                    $ungrouped[] = is_string($innerKey) ? $innerKey : $innerValue;
                }
            } else {
                $ungrouped[] = $value;
            }
        }

        $result = collect();

        if (! empty($ungrouped)) {
            $result->put('', collect($ungrouped));
        }

        return $result->merge($groups);
    }

    /**
     * @param  array<string, array<string | ToolbarButtonGroup>> | Closure | null  $toolbars
     */
    public function floatingToolbars(array | Closure | null $toolbars): static
    {
        $this->floatingToolbars = $toolbars;

        return $this;
    }

    /**
     * @return array<string, array<string | ToolbarButtonGroup>>
     */
    public function getFloatingToolbars(): array
    {
        $toolbars = $this->evaluate($this->floatingToolbars) ?? $this->getDefaultFloatingToolbars();
        $tools = $this->getTools();

        return array_map(
            fn (array $buttons): array => array_map(
                fn (string | ToolbarButtonGroup $item): string | ToolbarButtonGroup => $item instanceof ToolbarButtonGroup
                    ? $item->resolve($tools)
                    : $item,
                $buttons,
            ),
            $toolbars,
        );
    }

    public function getLengthValidationRules(): array
    {
        $rules = [];

        if (filled($maxLength = $this->getMaxLength())) {
            $rules[] = function (string $attribute, mixed $value, Closure $fail) use ($maxLength): void {
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
            $rules[] = function (string $attribute, mixed $value, Closure $fail) use ($minLength): void {
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
            $rules[] = function (string $attribute, mixed $value, Closure $fail) use ($length): void {
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

    public function getRequiredValidationRule(): string | Closure
    {
        if (! $this->isRequired()) {
            return 'nullable';
        }

        return function (string $attribute, mixed $value, Closure $fail): void {
            if (blank($value)) {
                return;
            }

            $isEmpty = is_array($value)
                && (($value['type'] ?? null) === 'doc')
                && (count($value['content'] ?? []) === 1)
                && (($value['content'][0]['type'] ?? null) === 'paragraph')
                && blank($value['content'][0]['content'] ?? []);

            if ($isEmpty) {
                $fail('validation.required')->translate();
            }
        };
    }

    public function callAfterStateUpdated(bool $shouldBubbleToParents = true): static
    {
        $rawState = $this->getRawState();

        // https://github.com/filamentphp/filament/issues/17472
        if (! is_array($rawState)) {
            foreach ($this->getStateCasts() as $stateCast) {
                $rawState = $stateCast->set($rawState);
            }

            $this->rawState($rawState);
        }

        return parent::callAfterStateUpdated($shouldBubbleToParents);
    }

    /**
     * @param  array<string, string | TextColor> | Closure | null  $colors
     */
    public function textColors(array | Closure | null $colors): static
    {
        $this->textColors = $colors;

        return $this;
    }

    /**
     * @return array<string, string | TextColor>
     */
    public function getTextColors(): array
    {
        $textColors = $this->evaluate($this->textColors) ?? $this->getContentAttribute()?->getTextColors() ?? TextColor::getDefaults();

        return Arr::mapWithKeys(
            $textColors,
            fn (string | TextColor $color, string $name): array => [$name => ($color instanceof TextColor) ? $color : TextColor::make($color, $name)],
        );
    }

    /**
     * @return array<string, array{color: string, darkColor: string}>
     */
    public function getTextColorsForJs(): array
    {
        return array_map(
            fn (TextColor $color): array => [
                'color' => $color->getColor(),
                'darkColor' => $color->getDarkColor(),
            ],
            $this->getTextColors(),
        );
    }

    public function customTextColors(bool | Closure | null $condition = true): static
    {
        $this->hasCustomTextColors = $condition;

        return $this;
    }

    public function hasCustomTextColors(): bool
    {
        return (bool) ($this->evaluate($this->hasCustomTextColors) ?? $this->getContentAttribute()?->hasCustomTextColors() ?? false);
    }

    public function resizableImages(bool | Closure | null $condition = true): static
    {
        $this->hasResizableImages = $condition;

        return $this;
    }

    public function hasResizableImages(): bool
    {
        return (bool) $this->evaluate($this->hasResizableImages);
    }

    public function hasFileAttachmentsByDefault(): bool
    {
        return $this->hasToolbarButton('attachFiles');
    }

    public function toEmbeddedHtml(): string
    {
        $groupedCustomBlocks = $this->getGroupedCustomBlocks();
        $id = $this->getId();
        $isDisabled = $this->isDisabled();
        $label = $this->getLabel();
        $livewireKey = $this->getLivewireKey();
        $key = $this->getKey();
        $mergeTags = $this->getMergeTags();
        $statePath = $this->getStatePath();
        $mentions = $this->getMentionsForJs();
        $toolbarButtons = $this->getToolbarButtons();
        $tools = $this->getTools();
        $floatingToolbars = $this->getFloatingToolbars();
        $linkProtocols = $this->getLinkProtocols();
        $fileAttachmentsMaxSize = $this->getFileAttachmentsMaxSize();
        $fileAttachmentsAcceptedFileTypes = $this->getFileAttachmentsAcceptedFileTypes();

        $wrapperAttributes = $this->getExtraAttributeBag()
            ->merge(['x-cloak' => true], escape: false)
            ->class(['fi-fo-rich-editor']);

        $deleteIconHtml = generate_icon_html(Heroicon::Trash, alias: FormsIconAlias::COMPONENTS_RICH_EDITOR_PANELS_CUSTOM_BLOCK_DELETE_BUTTON);
        $editIconHtml = generate_icon_html(Heroicon::PencilSquare, alias: FormsIconAlias::COMPONENTS_RICH_EDITOR_PANELS_CUSTOM_BLOCK_EDIT_BUTTON);

        ob_start(); ?>

        <div
            aria-labelledby="<?= e($id) ?>-label"
            id="<?= e($id) ?>"
            role="group"
            x-load
            x-load-src="<?= e(FilamentAsset::getAlpineComponentSrc('rich-editor', 'filament/forms')) ?>"
            x-data="richEditorFormComponent({
                            acceptedFileTypes: <?= Js::from($fileAttachmentsAcceptedFileTypes) ?>,
                            acceptedFileTypesValidationMessage: <?= Js::from($fileAttachmentsAcceptedFileTypes ? __('filament-forms::components.rich_editor.file_attachments_accepted_file_types_message', ['values' => implode(', ', $fileAttachmentsAcceptedFileTypes)]) : null) ?>,
                            activePanel: <?= Js::from($this->getActivePanel()) ?>,
                            canAttachFiles: <?= Js::from($this->hasFileAttachments()) ?>,
                            deleteCustomBlockButtonIconHtml: <?= Js::from($deleteIconHtml?->toHtml()) ?>,
                            editCustomBlockButtonIconHtml: <?= Js::from($editIconHtml?->toHtml()) ?>,
                            extensions: <?= Js::from($this->getTipTapJsExtensions()) ?>,
                            floatingToolbars: <?= Js::from($floatingToolbars) ?>,
                            getMentionLabelsUsing: async (mentions) => {
                                return await $wire.callSchemaComponentMethod(
                                    <?= Js::from($key) ?>,
                                    'getMentionLabelsForJs',
                                    { mentions },
                                )
                            },
                            getMentionSearchResultsUsing: async (query, char) => {
                                return await $wire.callSchemaComponentMethod(
                                    <?= Js::from($key) ?>,
                                    'getMentionSearchResultsForJs',
                                    { search: query, char },
                                )
                            },
                            hasResizableImages: <?= Js::from($this->hasResizableImages()) ?>,
                            isDisabled: <?= Js::from($isDisabled) ?>,
                            label: <?= Js::from($label) ?>,
                            isLiveDebounced: <?= Js::from($this->isLiveDebounced()) ?>,
                            isLiveOnBlur: <?= Js::from($this->isLiveOnBlur()) ?>,
                            key: <?= Js::from($key) ?>,
                            linkProtocols: <?= Js::from($linkProtocols) ?>,
                            liveDebounce: <?= Js::from($this->getNormalizedLiveDebounce()) ?>,
                            livewireId: <?= Js::from($this->getLivewire()->getId()) ?>,
                            maxFileSize: <?= Js::from($fileAttachmentsMaxSize) ?>,
                            maxFileSizeValidationMessage: <?= Js::from($fileAttachmentsMaxSize ? trans_choice('filament-forms::components.rich_editor.file_attachments_max_size_message', $fileAttachmentsMaxSize, ['max' => $fileAttachmentsMaxSize]) : null) ?>,
                            mentions: <?= Js::from($mentions) ?>,
                            mergeTags: <?= Js::from($mergeTags) ?>,
                            noMergeTagSearchResultsMessage: <?= Js::from($this->getNoMergeTagSearchResultsMessage()) ?>,
                            placeholder: <?= Js::from($this->getPlaceholder()) ?>,
                            state: $wire.<?= $this->applyStateBindingModifiers("\$entangle('{$statePath}')", isOptimisticallyLive: false) ?>,
                            statePath: <?= Js::from($statePath) ?>,
                            textColors: <?= Js::from($this->getTextColorsForJs()) ?>,
                            uploadingFileMessage: <?= Js::from($this->getUploadingFileMessage()) ?>,
                        })"
            x-bind:class="{
                'fi-fo-rich-editor-uploading-file': isUploadingFile,
            }"
            wire:ignore
            wire:key="<?= e($livewireKey) ?>.<?= substr(md5(serialize([$isDisabled])), 0, 64) ?>"
        >
            <?php if ((! $isDisabled) && filled($toolbarButtons)) { ?>
                <?php // `role="toolbar"` is withheld until the APG roving-tabindex/arrow-key pattern is
                      // implemented: the tools are `tabindex="-1"` with no arrow-key navigation, so announcing
                      // a toolbar would promise keyboard behaviour that does not exist. The `aria-label`
                      // still names the group.?>
                <div
                    class="fi-fo-rich-editor-toolbar"
                    aria-label="<?= e(__('filament-forms::components.rich_editor.toolbar.aria_label')) ?>"
                >
                    <?php foreach ($toolbarButtons as $buttonGroup) { ?>
                        <div class="fi-fo-rich-editor-toolbar-group">
                            <?php foreach ($buttonGroup as $button) { ?>
                                <?php if (is_string($button)) { ?>
                                    <?= ($tools[$button] ?? throw new LogicException("Toolbar button [{$button}] cannot be found."))->toHtml() ?>
                                <?php } else { ?>
                                    <?= $button->toHtml() ?>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>

            <div
                x-show="isUploadingFile"
                x-cloak
                class="fi-fo-rich-editor-uploading-file-message"
            >
                <?= generate_loading_indicator_html()->toHtml() ?>

                <span><?= e($this->getUploadingFileMessage()) ?></span>
            </div>

            <div
                x-show="! isUploadingFile && fileValidationMessage"
                x-cloak
                class="fi-fo-rich-editor-file-validation-message"
            >
                <span
                    x-text="fileValidationMessage"
                    x-show="! isUploadingFile && fileValidationMessage"
                ></span>
            </div>

            <div <?= $this->getExtraInputAttributeBag()->class(['fi-fo-rich-editor-main'])->toHtml() ?>>
                <div class="fi-fo-rich-editor-content fi-prose" x-ref="editor">
                    <?php foreach ($floatingToolbars as $nodeName => $buttons) { ?>
                        <div
                            x-ref="floatingToolbar::<?= e($nodeName) ?>"
                            class="fi-fo-rich-editor-floating-toolbar fi-not-prose"
                        >
                            <?php foreach ($buttons as $button) { ?>
                                <?php if (is_string($button)) { ?>
                                    <?= $tools[$button]->toHtml() ?>
                                <?php } else { ?>
                                    <?= $button->toHtml() ?>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>

                <?php if (! $isDisabled) { ?>
                    <div
                        x-show="isPanelActive()"
                        x-cloak
                        class="fi-fo-rich-editor-panels"
                    >
                        <div
                            x-show="isPanelActive('customBlocks')"
                            x-cloak
                            class="fi-fo-rich-editor-panel"
                        >
                            <div class="fi-fo-rich-editor-panel-header">
                                <p class="fi-fo-rich-editor-panel-heading">
                                    <?= e(__('filament-forms::components.rich_editor.tools.custom_blocks')) ?>
                                </p>

                                <div class="fi-fo-rich-editor-panel-close-btn-ctn">
                                    <button type="button" x-on:click="togglePanel()" class="fi-icon-btn">
                                        <?= generate_icon_html(Heroicon::XMark, alias: FormsIconAlias::COMPONENTS_RICH_EDITOR_PANELS_CUSTOM_BLOCKS_CLOSE_BUTTON)?->toHtml() ?>
                                    </button>
                                </div>
                            </div>

                            <div class="fi-fo-rich-editor-custom-blocks-ctn">
                                <?php foreach ($groupedCustomBlocks as $customBlockGroupLabel => $groupBlocks) { ?>
                                    <?php if (filled($customBlockGroupLabel)) { ?>
                                        <h4 class="fi-fo-rich-editor-custom-blocks-group-header">
                                            <?= e($customBlockGroupLabel) ?>
                                        </h4>
                                    <?php } ?>

                                    <div class="fi-fo-rich-editor-custom-blocks-list">
                                        <?php foreach ($groupBlocks as $block) { ?>
                                            <?php $blockId = $block::getId(); ?>
                                            <button
                                                draggable="true"
                                                type="button"
                                                x-data="{ isLoading: false }"
                                                x-on:click="
                                                    isLoading = true
                                                    $wire.mountAction(
                                                        'customBlock',
                                                        { editorSelection, id: <?= Js::from($blockId) ?>, mode: 'insert' },
                                                        { schemaComponent: <?= Js::from($key) ?> },
                                                    )
                                                "
                                                x-on:dragstart="$event.dataTransfer.setData('customBlock', <?= Js::from($blockId) ?>)"
                                                x-on:open-modal.window="isLoading = false"
                                                x-on:run-rich-editor-commands.window="isLoading = false"
                                                class="fi-fo-rich-editor-custom-block-btn"
                                            >
                                                <?= generate_loading_indicator_html((new FilamentComponentAttributeBag(['x-show' => 'isLoading'])))->toHtml() ?>
                                                <?= e($block::getLabel()) ?>
                                            </button>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                        <div
                            x-show="isPanelActive('mergeTags')"
                            x-cloak
                            class="fi-fo-rich-editor-panel"
                        >
                            <div class="fi-fo-rich-editor-panel-header">
                                <p class="fi-fo-rich-editor-panel-heading">
                                    <?= e(__('filament-forms::components.rich_editor.tools.merge_tags')) ?>
                                </p>

                                <div class="fi-fo-rich-editor-panel-close-btn-ctn">
                                    <button type="button" x-on:click="togglePanel()" class="fi-icon-btn">
                                        <?= generate_icon_html(Heroicon::XMark, alias: FormsIconAlias::COMPONENTS_RICH_EDITOR_PANELS_MERGE_TAGS_CLOSE_BUTTON)?->toHtml() ?>
                                    </button>
                                </div>
                            </div>

                            <div class="fi-fo-rich-editor-merge-tags-list">
                                <?php foreach ($mergeTags as $tagId => $tagLabel) { ?>
                                    <button
                                        draggable="true"
                                        type="button"
                                        x-on:click="insertMergeTag(<?= Js::from($tagId) ?>)"
                                        x-on:dragstart="$event.dataTransfer.setData('mergeTag', <?= Js::from($tagId) ?>)"
                                        class="fi-fo-rich-editor-merge-tag-btn"
                                    >
                                        <span data-type="mergeTag" data-id="<?= e($tagId) ?>">
                                            <?= e($tagLabel) ?>
                                        </span>
                                    </button>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>

        <?php $slotHtml = ob_get_clean();

        return $this->wrapEmbeddedHtml(
            $this->wrapInputHtml(
                $slotHtml,
                attributes: $wrapperAttributes,
            ),
            labelTag: 'div',
        );
    }
}
