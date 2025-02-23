<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Forms\Components\RichEditor\Actions\AttachFilesAction;
use Filament\Forms\Components\RichEditor\Actions\LinkAction;
use Filament\Forms\Components\RichEditor\EditorCommand;
use Filament\Forms\Components\RichEditor\StateCasts\RichEditorStateCast;
use Filament\Forms\Components\RichEditor\TipTapExtensions\Image;
use Filament\Schemas\Components\StateCasts\Contracts\StateCast;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Tiptap\Core\Extension;
use Tiptap\Marks\Bold;
use Tiptap\Marks\Code;
use Tiptap\Marks\Italic;
use Tiptap\Marks\Link;
use Tiptap\Marks\Strike;
use Tiptap\Marks\Subscript;
use Tiptap\Marks\Superscript;
use Tiptap\Marks\Underline;
use Tiptap\Nodes\Blockquote;
use Tiptap\Nodes\BulletList;
use Tiptap\Nodes\CodeBlock;
use Tiptap\Nodes\Document;
use Tiptap\Nodes\Heading;
use Tiptap\Nodes\ListItem;
use Tiptap\Nodes\OrderedList;
use Tiptap\Nodes\Paragraph;
use Tiptap\Nodes\Text;

class RichEditor extends Field implements Contracts\CanBeLengthConstrained, Contracts\HasFileAttachments
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

    /**
     * @var array<string>
     */
    protected array | Closure $toolbarButtons = [
        'attachFiles',
        'blockquote',
        'bold',
        'bulletList',
        'codeBlock',
        'h2',
        'h3',
        'italic',
        'link',
        'orderedList',
        'redo',
        'strike',
        'subscript',
        'superscript',
        'underline',
        'undo',
    ];

    protected string | Closure | null $uploadingFileMessage = null;

    protected bool | Closure $isJson = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registerActions([
            AttachFilesAction::make(),
            LinkAction::make(),
        ]);
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
     * @param  array<string, mixed>  $editorSelection
     */
    public function runCommands(array $commands, array $editorSelection): void
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

    public function json(bool | Closure $condition = true): static
    {
        $this->isJson = $condition;

        return $this;
    }

    public function isJson(): bool
    {
        return (bool) $this->evaluate($this->isJson);
    }

    /**
     * @return array{extensions: array<Extension>}
     */
    public function getTipTapPhpConfiguration(): array
    {
        return [
            'extensions' => $this->getTipTapPhpExtensions(),
        ];
    }

    /**
     * @return array<Extension>
     */
    public function getTipTapPhpExtensions(): array
    {
        return [
            app(Blockquote::class),
            app(Bold::class),
            app(BulletList::class),
            app(Code::class),
            app(CodeBlock::class),
            app(Document::class),
            app(Heading::class),
            app(Italic::class),
            app(Image::class),
            app(Link::class),
            app(ListItem::class),
            app(OrderedList::class),
            app(Paragraph::class),
            app(Strike::class),
            app(Subscript::class),
            app(Superscript::class),
            app(Text::class),
            app(Underline::class),
        ];
    }
}
