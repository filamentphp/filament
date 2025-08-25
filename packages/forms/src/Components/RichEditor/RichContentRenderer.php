<?php

namespace Filament\Forms\Components\RichEditor;

use Closure;
use Filament\Forms\Components\RichEditor\FileAttachmentProviders\Contracts\FileAttachmentProvider;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\RichContentPlugin;
use Filament\Forms\Components\RichEditor\TipTapExtensions\CustomBlockExtension;
use Filament\Forms\Components\RichEditor\TipTapExtensions\DetailsContentExtension;
use Filament\Forms\Components\RichEditor\TipTapExtensions\DetailsExtension;
use Filament\Forms\Components\RichEditor\TipTapExtensions\DetailsSummaryExtension;
use Filament\Forms\Components\RichEditor\TipTapExtensions\ImageExtension;
use Filament\Forms\Components\RichEditor\TipTapExtensions\LeadExtension;
use Filament\Forms\Components\RichEditor\TipTapExtensions\MergeTagExtension;
use Filament\Forms\Components\RichEditor\TipTapExtensions\RenderedCustomBlockExtension;
use Filament\Forms\Components\RichEditor\TipTapExtensions\SmallExtension;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use League\Flysystem\UnableToCheckFileExistence;
use Throwable;
use Tiptap\Core\Extension;
use Tiptap\Editor;
use Tiptap\Extensions\TextAlign;
use Tiptap\Marks\Bold;
use Tiptap\Marks\Code;
use Tiptap\Marks\Highlight;
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
use Tiptap\Nodes\HardBreak;
use Tiptap\Nodes\Heading;
use Tiptap\Nodes\HorizontalRule;
use Tiptap\Nodes\ListItem;
use Tiptap\Nodes\OrderedList;
use Tiptap\Nodes\Paragraph;
use Tiptap\Nodes\Table;
use Tiptap\Nodes\TableCell;
use Tiptap\Nodes\TableHeader;
use Tiptap\Nodes\TableRow;
use Tiptap\Nodes\Text;

class RichContentRenderer implements Htmlable
{
    use Macroable;

    /**
     * @var string | array<string, mixed>
     */
    protected string | array | null $content = null;

    protected ?string $fileAttachmentsDiskName = null;

    protected ?string $fileAttachmentsVisibility = null;

    /**
     * @var array<RichContentPlugin>
     */
    protected array $plugins = [];

    protected ?FileAttachmentProvider $fileAttachmentProvider = null;

    /**
     * @var ?array<string, mixed>
     */
    protected ?array $mergeTags = null;

    /**
     * @var ?array<class-string<RichContentCustomBlock> | array<string, mixed> | Closure>
     */
    protected ?array $customBlocks = null;

    /**
     * @var array<string, mixed>
     */
    protected array $cachedMergeTagValues = [];

    /**
     * @param  string | array<string, mixed> | null  $content
     */
    public function __construct(string | array | null $content = null)
    {
        $this->content($content);
    }

    /**
     * @param  string | array<string, mixed> | null  $content
     */
    public static function make(string | array | null $content = null): static
    {
        return app(static::class, [
            'content' => $content,
        ]);
    }

    /**
     * @param  string | array<string, mixed> | null  $content
     */
    public function content(string | array | null $content): static
    {
        $this->content = $content;
        $this->cachedMergeTagValues = [];

        return $this;
    }

    public function fileAttachmentsDisk(?string $name): static
    {
        $this->fileAttachmentsDiskName = $name;

        return $this;
    }

    public function fileAttachmentsVisibility(?string $visibility): static
    {
        $this->fileAttachmentsVisibility = $visibility;

        return $this;
    }

    public function getFileAttachmentUrl(mixed $file): ?string
    {
        $fileAttachmentProvider = $this->getFileAttachmentProvider();

        if ($fileAttachmentProvider) {
            return $fileAttachmentProvider->getFileAttachmentUrl($file);
        }

        $disk = $this->fileAttachmentsDiskName ?? config('filament.default_filesystem_disk');
        $visibility = $this->fileAttachmentsVisibility ?? ($disk === 'public' ? 'public' : 'private');

        $storage = Storage::disk($disk);

        try {
            if (! $storage->exists($file)) {
                return null;
            }
        } catch (UnableToCheckFileExistence $exception) {
            return null;
        }

        if ($visibility === 'private') {
            try {
                return $storage->temporaryUrl(
                    $file,
                    now()->addMinutes(30)->endOfHour(),
                );
            } catch (Throwable $exception) {
                // This driver does not support creating temporary URLs.
            }
        }

        return $storage->url($file);
    }

    /**
     * @param  array<RichContentPlugin>  $plugins
     */
    public function plugins(array $plugins): static
    {
        $this->plugins = [
            ...$this->plugins,
            ...$plugins,
        ];

        return $this;
    }

    protected function processCustomBlocks(Editor $editor): void
    {
        if (blank($this->customBlocks)) {
            return;
        }

        $editor->descendants(function (object &$node): void {
            if ($node->type !== 'customBlock') {
                return;
            }

            if (blank($node->attrs->id ?? null)) {
                return;
            }

            $nodeConfig = json_decode(json_encode($node->attrs->config ?? []), associative: true);

            $node->type = 'renderedCustomBlock';
            $node->html = $this->getCustomBlockHtml($node->attrs->id, $nodeConfig);
            unset($node->attrs->config);
        });
    }

    protected function processFileAttachments(Editor $editor): void
    {
        $editor->descendants(function (object &$node): void {
            if ($node->type !== 'image') {
                return;
            }

            if (blank($node->attrs->id ?? null)) {
                return;
            }

            $node->attrs->src = $this->getFileAttachmentUrl($node->attrs->id);
        });
    }

    protected function processMergeTags(Editor $editor): void
    {
        if (blank($this->mergeTags)) {
            return;
        }

        $editor->descendants(function (object &$node): void {
            if ($node->type !== 'mergeTag') {
                return;
            }

            if (blank($node->attrs->id ?? null)) {
                return;
            }

            $node->content = [
                (object) [
                    'type' => 'text',
                    'text' => $this->getMergeTagValue($node->attrs->id),
                ],
            ];
        });
    }

    /**
     * @return array<RichContentPlugin>
     */
    public function getPlugins(): array
    {
        return $this->plugins;
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
            app(CustomBlockExtension::class),
            app(DetailsContentExtension::class),
            app(DetailsExtension::class),
            app(DetailsSummaryExtension::class),
            app(Document::class),
            app(HardBreak::class),
            app(Heading::class),
            app(Highlight::class),
            app(HorizontalRule::class),
            app(Italic::class),
            app(ImageExtension::class),
            app(LeadExtension::class),
            app(Link::class),
            app(ListItem::class),
            app(MergeTagExtension::class),
            app(OrderedList::class),
            app(Paragraph::class),
            app(RenderedCustomBlockExtension::class),
            app(SmallExtension::class),
            app(Strike::class),
            app(Subscript::class),
            app(Superscript::class),
            app(Table::class),
            app(TableCell::class),
            app(TableHeader::class),
            app(TableRow::class),
            app(Text::class),
            app(TextAlign::class, [
                'options' => [
                    'types' => ['heading', 'paragraph'],
                    'alignments' => ['start', 'center', 'end', 'justify'],
                    'defaultAlignment' => 'start',
                ],
            ]),
            app(Underline::class),
            ...array_reduce(
                $this->getPlugins(),
                fn (array $carry, RichContentPlugin $plugin): array => [
                    ...$carry,
                    ...$plugin->getTipTapPhpExtensions(),
                ],
                initial: [],
            ),
        ];
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

    public function fileAttachmentProvider(?FileAttachmentProvider $provider): static
    {
        $this->fileAttachmentProvider = $provider;

        return $this;
    }

    public function getFileAttachmentProvider(): ?FileAttachmentProvider
    {
        return $this->fileAttachmentProvider;
    }

    public function getEditor(): Editor
    {
        $editor = app(Editor::class, ['configuration' => $this->getTipTapPhpConfiguration()]);

        if (filled($this->content)) {
            $editor->setContent($this->content);
        }

        return $editor;
    }

    public function toUnsafeHtml(): string
    {
        $editor = $this->getEditor();

        $this->processCustomBlocks($editor);
        $this->processFileAttachments($editor);
        $this->processMergeTags($editor);

        return $editor->getHTML();
    }

    public function toHtml(): string
    {
        return Str::sanitizeHtml($this->toUnsafeHtml());
    }

    /**
     * @param  ?array<string, mixed>  $tags
     */
    public function mergeTags(?array $tags): static
    {
        $this->mergeTags = $tags;
        $this->cachedMergeTagValues = [];

        return $this;
    }

    public function getMergeTagValue(string $mergeTag): mixed
    {
        return $this->cachedMergeTagValues[$mergeTag] ??= value($this->mergeTags[$mergeTag] ?? null);
    }

    /**
     * @param  ?array<class-string<RichContentCustomBlock> | array<string, mixed> | Closure>  $blocks
     */
    public function customBlocks(?array $blocks): static
    {
        $this->customBlocks = $blocks;

        return $this;
    }

    /**
     * @param  array<string, mixed>  $config
     */
    public function getCustomBlockHtml(string $id, array $config): ?string
    {
        foreach ($this->customBlocks as $key => $block) {
            if (is_string($key) && ($key::getId() === $id)) {
                return $key::toHtml($config, data: value($block) ?? []);
            } elseif (is_string($block) && ($block::getId() === $id)) {
                return $block::toHtml($config, data: []);
            }
        }

        return null;
    }
}
