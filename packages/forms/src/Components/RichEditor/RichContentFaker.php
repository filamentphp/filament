<?php

namespace Filament\Forms\Components\RichEditor;

use Closure;
use Faker\Generator;
use Filament\Forms\Components\RichEditor\Contracts\CanGenerateFakeConfiguration;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use InvalidArgumentException;
use LogicException;

class RichContentFaker
{
    use Macroable;

    /**
     * @var array<array<string, mixed>>
     */
    protected array $content = [];

    protected ?RichContentRenderer $renderer = null;

    public function __construct(
        protected Generator $faker,
        protected ?RichContentAttribute $attribute = null,
    ) {}

    public static function make(Generator $faker, ?RichContentAttribute $attribute = null): static
    {
        return app(static::class, [
            'faker' => $faker,
            'attribute' => $attribute,
        ]);
    }

    public function attribute(?RichContentAttribute $attribute): static
    {
        $this->attribute = $attribute;
        $this->renderer = null;

        return $this;
    }

    public function article(int $depth = 1): static
    {
        if (($depth < 1) || ($depth > 5)) {
            throw new InvalidArgumentException('Article depth must be between 1 and 5.');
        }

        $this->lead();
        $this->articleSections(level: 2, depth: $depth, isRoot: true);

        return $this;
    }

    protected function articleSections(int $level, int $depth, bool $isRoot = false): void
    {
        $sectionCount = $this->faker->numberBetween($isRoot ? 2 : 1, $isRoot ? 4 : 2);

        for ($section = 0; $section < $sectionCount; $section++) {
            $this->heading($level);
            $this->paragraphs(
                count: $this->faker->numberBetween(1, 3),
                links: $this->faker->boolean(30),
                bold: $this->faker->boolean(40),
                italic: $this->faker->boolean(20),
            );

            if ($depth > 1) {
                $this->articleSections(level: $level + 1, depth: $depth - 1);
            }
        }
    }

    public function heading(int $level = 2): static
    {
        if (($level < 1) || ($level > 6)) {
            throw new InvalidArgumentException('Heading level must be between 1 and 6.');
        }

        $this->content[] = [
            'type' => 'heading',
            'attrs' => ['level' => $level],
            'content' => [[
                'type' => 'text',
                'text' => Str::title($this->faker->words($this->faker->numberBetween(3, 8), true)),
            ]],
        ];

        return $this;
    }

    public function paragraphs(
        int $count = 1,
        bool $links = false,
        bool $bold = false,
        bool $italic = false,
        bool $underline = false,
        bool $strike = false,
        bool $subscript = false,
        bool $superscript = false,
        bool $code = false,
        bool $small = false,
        bool $highlight = false,
    ): static {
        if ($count < 0) {
            throw new InvalidArgumentException('Paragraph count must be at least 0.');
        }

        for ($paragraph = 0; $paragraph < $count; $paragraph++) {
            $this->content[] = $this->makeParagraphNode(
                links: $links,
                bold: $bold,
                italic: $italic,
                underline: $underline,
                strike: $strike,
                subscript: $subscript,
                superscript: $superscript,
                code: $code,
                small: $small,
                highlight: $highlight,
            );
        }

        return $this;
    }

    public function lead(int $paragraphs = 1): static
    {
        if ($paragraphs < 1) {
            throw new InvalidArgumentException('Lead paragraph count must be at least 1.');
        }

        $content = [];

        for ($paragraph = 0; $paragraph < $paragraphs; $paragraph++) {
            $content[] = $this->makeParagraphNode();
        }

        $this->content[] = [
            'type' => 'lead',
            'content' => $content,
        ];

        return $this;
    }

    public function bulletList(int $items = 3): static
    {
        return $this->list('bulletList', $items);
    }

    public function orderedList(int $items = 3): static
    {
        return $this->list('orderedList', $items);
    }

    protected function list(string $type, int $items): static
    {
        if ($items < 1) {
            throw new InvalidArgumentException('List item count must be at least 1.');
        }

        $content = [];

        for ($item = 0; $item < $items; $item++) {
            $content[] = [
                'type' => 'listItem',
                'content' => [$this->makeParagraphNode(sentence: true)],
            ];
        }

        $this->content[] = [
            'type' => $type,
            'content' => $content,
        ];

        return $this;
    }

    public function blockquote(int $paragraphs = 1): static
    {
        if ($paragraphs < 1) {
            throw new InvalidArgumentException('Blockquote paragraph count must be at least 1.');
        }

        $content = [];

        for ($paragraph = 0; $paragraph < $paragraphs; $paragraph++) {
            $content[] = $this->makeParagraphNode();
        }

        $this->content[] = [
            'type' => 'blockquote',
            'content' => $content,
        ];

        return $this;
    }

    public function codeBlock(?string $language = null, ?string $code = null): static
    {
        $code ??= $this->faker->text(120);

        $node = [
            'type' => 'codeBlock',
            'attrs' => ['language' => $language],
        ];

        if ($code !== '') {
            $node['content'] = [[
                'type' => 'text',
                'text' => $code,
            ]];
        }

        $this->content[] = $node;

        return $this;
    }

    public function horizontalRule(): static
    {
        $this->content[] = ['type' => 'horizontalRule'];

        return $this;
    }

    public function hardBreak(): static
    {
        $this->insertInlineNode(['type' => 'hardBreak'], surroundWithSpaces: false);

        return $this;
    }

    public function table(int $columns = 3, int $rows = 2): static
    {
        if (($columns < 1) || ($rows < 1)) {
            throw new InvalidArgumentException('Table column and row counts must be at least 1.');
        }

        $content = [];

        for ($row = 0; $row < $rows; $row++) {
            $cells = [];

            for ($column = 0; $column < $columns; $column++) {
                $cells[] = [
                    'type' => ($row === 0) ? 'tableHeader' : 'tableCell',
                    'content' => [[
                        'type' => 'paragraph',
                        'content' => [[
                            'type' => 'text',
                            'text' => ($row === 0)
                                ? Str::title($this->faker->word())
                                : $this->faker->words($this->faker->numberBetween(1, 3), true),
                        ]],
                    ]],
                ];
            }

            $content[] = [
                'type' => 'tableRow',
                'content' => $cells,
            ];
        }

        $this->content[] = [
            'type' => 'table',
            'content' => $content,
        ];

        return $this;
    }

    public function details(int $paragraphs = 1): static
    {
        if ($paragraphs < 1) {
            throw new InvalidArgumentException('Details paragraph count must be at least 1.');
        }

        $detailsContent = [];

        for ($paragraph = 0; $paragraph < $paragraphs; $paragraph++) {
            $detailsContent[] = $this->makeParagraphNode();
        }

        $this->content[] = [
            'type' => 'details',
            'content' => [
                [
                    'type' => 'detailsSummary',
                    'content' => [[
                        'type' => 'text',
                        'text' => $this->faker->sentence(),
                    ]],
                ],
                [
                    'type' => 'detailsContent',
                    'content' => $detailsContent,
                ],
            ],
        ];

        return $this;
    }

    /**
     * @param  array<int>  $columns
     */
    public function grid(array $columns = [1, 1], string $breakpoint = 'lg'): static
    {
        if ($columns === []) {
            throw new InvalidArgumentException('A grid must contain at least one column.');
        }

        $content = [];

        foreach ($columns as $columnSpan) {
            if ($columnSpan < 1) {
                throw new InvalidArgumentException('Grid column spans must be at least 1.');
            }

            $content[] = [
                'type' => 'gridColumn',
                'attrs' => ['data-col-span' => $columnSpan],
                'content' => [$this->makeParagraphNode()],
            ];
        }

        $this->content[] = [
            'type' => 'grid',
            'attrs' => [
                'data-cols' => array_sum($columns),
                'data-from-breakpoint' => $breakpoint,
            ],
            'content' => $content,
        ];

        return $this;
    }

    public function image(?string $url = null, int $width = 1280, int $height = 720, ?string $alt = null): static
    {
        if (($width < 1) || ($height < 1)) {
            throw new InvalidArgumentException('Image width and height must be at least 1.');
        }

        $this->content[] = [
            'type' => 'paragraph',
            'content' => [[
                'type' => 'image',
                'attrs' => [
                    'src' => $url ?? "https://picsum.photos/seed/{$this->faker->uuid()}/{$width}/{$height}",
                    'alt' => $alt ?? $this->faker->sentence(),
                    'width' => $width,
                    'height' => $height,
                ],
            ]],
        ];

        return $this;
    }

    public function fileAttachment(mixed $id, ?int $width = null, ?int $height = null, ?string $alt = null): static
    {
        if (($width !== null) && ($width < 1)) {
            throw new InvalidArgumentException('File attachment width must be at least 1.');
        }

        if (($height !== null) && ($height < 1)) {
            throw new InvalidArgumentException('File attachment height must be at least 1.');
        }

        $this->content[] = [
            'type' => 'paragraph',
            'content' => [[
                'type' => 'image',
                'attrs' => [
                    'id' => $id,
                    'alt' => $alt ?? $this->faker->sentence(),
                    'width' => $width,
                    'height' => $height,
                ],
            ]],
        ];

        return $this;
    }

    /**
     * @param  class-string<RichContentCustomBlock> | string | null  $block
     * @param  ?array<string, mixed>  $configuration
     */
    public function customBlock(?string $block = null, ?array $configuration = null): static
    {
        if ($block === null) {
            $blocks = array_values(array_filter(
                $this->attribute?->getCustomBlocks() ?? [],
                fn (string $registeredBlock): bool => is_a($registeredBlock, CanGenerateFakeConfiguration::class, allow_string: true),
            ));

            if ($blocks === []) {
                throw new LogicException('No registered custom blocks can generate fake configuration.');
            }

            $block = $this->faker->randomElement($blocks);
        }

        $blockClass = $this->getCustomBlockClass($block);

        if (($configuration === null) && ($blockClass !== null) && is_a($blockClass, CanGenerateFakeConfiguration::class, allow_string: true)) {
            $configuration = $blockClass::generateFakeConfiguration($this->faker);
        }

        $this->content[] = [
            'type' => 'customBlock',
            'attrs' => [
                'id' => $blockClass ? $blockClass::getId() : $block,
                'config' => $configuration ?? [],
            ],
        ];

        return $this;
    }

    public function mergeTag(?string $id = null): static
    {
        if ($id === null) {
            $mergeTags = array_keys($this->attribute?->getMergeTags() ?? []);

            if ($mergeTags === []) {
                throw new LogicException('No merge tags are configured on the rich content attribute.');
            }

            $id = $this->faker->randomElement($mergeTags);
        }

        $this->insertInlineNode([
            'type' => 'mergeTag',
            'attrs' => ['id' => $id],
        ]);

        return $this;
    }

    public function mergeTags(int $count = 1): static
    {
        if ($count < 0) {
            throw new InvalidArgumentException('Merge tag count must be at least 0.');
        }

        for ($mergeTag = 0; $mergeTag < $count; $mergeTag++) {
            $this->mergeTag();
        }

        return $this;
    }

    public function mention(string | int | null $id = null, ?string $char = null): static
    {
        $providers = $this->attribute?->getMentionProviders() ?? [];
        $provider = null;
        $items = null;

        if (($id !== null) && ($char === null) && (count($providers) > 1)) {
            throw new InvalidArgumentException('A mention trigger character must be specified when generating an explicit mention ID with multiple providers.');
        }

        if ($providers !== []) {
            if (($id === null) && ($char === null)) {
                $providersWithItems = [];

                foreach ($providers as $registeredProvider) {
                    $registeredProviderItems = $registeredProvider->getSearchResults('');

                    if ($registeredProviderItems === []) {
                        continue;
                    }

                    $providersWithItems[] = [$registeredProvider, $registeredProviderItems];
                }

                if ($providersWithItems !== []) {
                    [$provider, $items] = $this->faker->randomElement($providersWithItems);
                }
            } else {
                $provider = ($char === null)
                    ? $this->faker->randomElement($providers)
                    : collect($providers)->first(fn (MentionProvider $registeredProvider): bool => $registeredProvider->getChar() === $char);
            }
        }

        if (($id === null) && (! $provider)) {
            throw new LogicException('No matching mention provider is configured on the rich content attribute.');
        }

        $char ??= $provider?->getChar() ?? '@';

        if ($id === null) {
            $items ??= $provider->getSearchResults('');

            if ($items === []) {
                throw new LogicException("The mention provider for [{$char}] did not return any items.");
            }

            $id = $this->faker->randomElement(array_keys($items));
            $label = $items[(string) $id];
        } else {
            $id = (string) $id;
            $label = $provider?->getLabels([$id])[$id] ?? $id;
        }

        $this->insertInlineNode([
            'type' => 'mention',
            'attrs' => [
                'id' => (string) $id,
                'label' => $label,
                'char' => $char,
            ],
        ]);

        return $this;
    }

    public function mentions(int $count = 1): static
    {
        if ($count < 0) {
            throw new InvalidArgumentException('Mention count must be at least 0.');
        }

        for ($mention = 0; $mention < $count; $mention++) {
            $this->mention();
        }

        return $this;
    }

    public function textColor(?string $color = null): static
    {
        if ($color === null) {
            $colors = array_keys($this->attribute?->getTextColors() ?? TextColor::getDefaults());

            if ($colors === []) {
                throw new LogicException('No text colors are configured on the rich content attribute.');
            }

            $color = $this->faker->randomElement($colors);
        }

        $this->markRandomText([
            'type' => 'textColor',
            'attrs' => ['data-color' => $color],
        ]);

        return $this;
    }

    public function textAlignment(?string $alignment = null): static
    {
        $alignments = ['start', 'center', 'end', 'justify'];
        $alignment ??= $this->faker->randomElement($alignments);

        if (! in_array($alignment, $alignments, strict: true)) {
            throw new InvalidArgumentException('Text alignment must be start, center, end, or justify.');
        }

        $this->modifyRandomTextBlock(function (array &$node) use ($alignment): void {
            $node['attrs']['textAlign'] = $alignment;
        });

        return $this;
    }

    /**
     * @param  array<string, mixed>  $node
     */
    public function block(array $node): static
    {
        $this->content[] = $node;

        return $this;
    }

    /**
     * @param  array<string, mixed>  $node
     */
    public function inline(array $node): static
    {
        $this->insertInlineNode($node);

        return $this;
    }

    public function renderUsing(RichContentRenderer $renderer): static
    {
        $this->renderer = $renderer;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type' => 'doc',
            'content' => $this->content ?: [['type' => 'paragraph']],
        ];
    }

    /**
     * @return array<string, mixed> | string
     */
    public function toValue(): array | string
    {
        return ($this->attribute?->isJson() ?? false)
            ? $this->toArray()
            : $this->toHtml();
    }

    public function toHtml(): string
    {
        return $this->getRenderer()
            ->content($this->toArray())
            ->getEditor()
            ->getHTML();
    }

    public function toRenderedHtml(): string
    {
        return $this->getRenderer()
            ->content($this->toArray())
            ->toHtml();
    }

    public function toText(): string
    {
        return $this->getRenderer()
            ->content($this->toArray())
            ->toText();
    }

    protected function getRenderer(): RichContentRenderer
    {
        return $this->renderer ??= $this->attribute?->getRenderer() ?? RichContentRenderer::make();
    }

    /**
     * @return class-string<RichContentCustomBlock> | null
     */
    protected function getCustomBlockClass(string $block): ?string
    {
        if (is_a($block, RichContentCustomBlock::class, allow_string: true)) {
            return $block;
        }

        foreach ($this->attribute?->getCustomBlocks() ?? [] as $registeredBlock) {
            if ($registeredBlock::getId() === $block) {
                return $registeredBlock;
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $node
     */
    protected function insertInlineNode(array $node, bool $surroundWithSpaces = true): void
    {
        if ($this->countNodesOfTypes($this->content, ['paragraph']) === 0) {
            $this->paragraphs();
        }

        $this->modifyRandomNode(['paragraph'], function (array &$paragraph) use ($node, $surroundWithSpaces): void {
            $content = $paragraph['content'] ?? [];
            $textNodeIndexes = [];

            foreach ($content as $index => $childNode) {
                if (
                    (($childNode['type'] ?? null) === 'text') &&
                    blank($childNode['marks'] ?? null) &&
                    (count(preg_split('/\s+/u', trim($childNode['text'] ?? '')) ?: []) > 1)
                ) {
                    $textNodeIndexes[] = $index;
                }
            }

            if ($textNodeIndexes === []) {
                $lastNodeIndex = array_key_last($content);

                if ($surroundWithSpaces && ($lastNodeIndex !== null)) {
                    $lastNode = $content[$lastNodeIndex];

                    if (($lastNode['type'] ?? null) === 'text') {
                        if (! preg_match('/\s$/u', $lastNode['text'] ?? '')) {
                            $paragraph['content'][$lastNodeIndex]['text'] .= ' ';
                        }
                    } elseif (($lastNode['type'] ?? null) !== 'hardBreak') {
                        $paragraph['content'][] = [
                            'type' => 'text',
                            'text' => ' ',
                        ];
                    }
                }

                $paragraph['content'][] = $node;

                return;
            }

            $textNodeIndex = $this->faker->randomElement($textNodeIndexes);
            $textNode = $content[$textNodeIndex];
            preg_match('/^(\s*)(.*?)(\s*)$/us', $textNode['text'], $textMatches);

            $leadingWhitespace = $textMatches[1] ?? '';
            $trailingWhitespace = $textMatches[3] ?? '';
            $words = preg_split('/\s+/u', $textMatches[2] ?? '') ?: [];
            $wordOffset = $this->faker->numberBetween(1, count($words) - 1);

            $textBefore = $leadingWhitespace . implode(' ', array_slice($words, 0, $wordOffset));
            $textAfter = implode(' ', array_slice($words, $wordOffset)) . $trailingWhitespace;

            if ($surroundWithSpaces) {
                $textBefore .= ' ';
                $textAfter = ' ' . $textAfter;
            }

            array_splice($paragraph['content'], $textNodeIndex, 1, [
                [...$textNode, 'text' => $textBefore],
                $node,
                [...$textNode, 'text' => $textAfter],
            ]);
        });
    }

    /**
     * @param  array<string, mixed>  $mark
     */
    protected function markRandomText(array $mark): void
    {
        $markType = $mark['type'] ?? null;
        $eligibleTextNodeCount = $this->countTextNodesEligibleForMark($this->content, $markType);

        if ($eligibleTextNodeCount === 0) {
            $this->paragraphs();
            $eligibleTextNodeCount = $this->countTextNodesEligibleForMark($this->content, $markType);
        }

        $targetTextNode = $this->faker->numberBetween(0, $eligibleTextNodeCount - 1);
        $currentTextNode = 0;

        $this->markTextNode($this->content, $mark, $targetTextNode, $currentTextNode);
    }

    /**
     * @param  array<array<string, mixed>>  $nodes
     * @param  array<string, mixed>  $mark
     */
    protected function markTextNode(array &$nodes, array $mark, int $targetTextNode, int &$currentTextNode, bool $isInParagraph = false): bool
    {
        $markType = $mark['type'] ?? null;

        foreach ($nodes as &$node) {
            if ($isInParagraph && $this->isTextNodeEligibleForMark($node, $markType)) {
                if ($currentTextNode === $targetTextNode) {
                    $node['marks'] = [
                        ...array_values(array_filter(
                            $node['marks'] ?? [],
                            static fn (array $existingMark): bool => ($existingMark['type'] ?? null) !== $markType,
                        )),
                        $mark,
                    ];

                    return true;
                }

                $currentTextNode++;
            }

            if (
                isset($node['content']) &&
                is_array($node['content']) &&
                $this->markTextNode(
                    $node['content'],
                    $mark,
                    $targetTextNode,
                    $currentTextNode,
                    $isInParagraph || (($node['type'] ?? null) === 'paragraph'),
                )
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<array<string, mixed>>  $nodes
     */
    protected function countTextNodesEligibleForMark(array $nodes, ?string $markType, bool $isInParagraph = false): int
    {
        $count = 0;

        foreach ($nodes as $node) {
            if ($isInParagraph && $this->isTextNodeEligibleForMark($node, $markType)) {
                $count++;
            }

            if (isset($node['content']) && is_array($node['content'])) {
                $count += $this->countTextNodesEligibleForMark(
                    $node['content'],
                    $markType,
                    $isInParagraph || (($node['type'] ?? null) === 'paragraph'),
                );
            }
        }

        return $count;
    }

    /**
     * @param  array<string, mixed>  $node
     */
    protected function isTextNodeEligibleForMark(array $node, ?string $markType): bool
    {
        return (($node['type'] ?? null) === 'text') &&
            (mb_strlen(trim($node['text'] ?? '')) > 1) &&
            (($markType === 'code') || (! collect($node['marks'] ?? [])->contains('type', 'code')));
    }

    protected function modifyRandomTextBlock(Closure $callback): void
    {
        if ($this->countNodesOfTypes($this->content, ['heading', 'paragraph']) === 0) {
            $this->paragraphs();
        }

        $this->modifyRandomNode(['heading', 'paragraph'], $callback);
    }

    /**
     * @param  array<string>  $types
     */
    protected function modifyRandomNode(array $types, Closure $callback): void
    {
        $nodeCount = $this->countNodesOfTypes($this->content, $types);

        if ($nodeCount === 0) {
            return;
        }

        $targetNode = $this->faker->numberBetween(0, $nodeCount - 1);
        $currentNode = 0;

        $this->modifyNode($this->content, $types, $targetNode, $currentNode, $callback);
    }

    /**
     * @param  array<array<string, mixed>>  $nodes
     * @param  array<string>  $types
     */
    protected function modifyNode(array &$nodes, array $types, int $targetNode, int &$currentNode, Closure $callback): bool
    {
        foreach ($nodes as &$node) {
            if (in_array($node['type'] ?? null, $types, strict: true)) {
                if ($currentNode === $targetNode) {
                    $callback($node);

                    return true;
                }

                $currentNode++;
            }

            if (isset($node['content']) && is_array($node['content']) && $this->modifyNode($node['content'], $types, $targetNode, $currentNode, $callback)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<array<string, mixed>>  $nodes
     * @param  array<string>  $types
     */
    protected function countNodesOfTypes(array $nodes, array $types): int
    {
        $count = 0;

        foreach ($nodes as $node) {
            if (in_array($node['type'] ?? null, $types, strict: true)) {
                $count++;
            }

            if (isset($node['content']) && is_array($node['content'])) {
                $count += $this->countNodesOfTypes($node['content'], $types);
            }
        }

        return $count;
    }

    /**
     * @return array<string, mixed>
     */
    protected function makeParagraphNode(
        bool $sentence = false,
        bool $links = false,
        bool $bold = false,
        bool $italic = false,
        bool $underline = false,
        bool $strike = false,
        bool $subscript = false,
        bool $superscript = false,
        bool $code = false,
        bool $small = false,
        bool $highlight = false,
    ): array {
        $text = rtrim($sentence ? $this->faker->sentence() : $this->faker->paragraph(), '.');

        $content = [[
            'type' => 'text',
            'text' => $text,
        ]];

        $marks = [
            'bold' => $bold,
            'italic' => $italic,
            'underline' => $underline,
            'strike' => $strike,
            'subscript' => $subscript,
            'superscript' => $superscript,
            'code' => $code,
            'small' => $small,
            'highlight' => $highlight,
        ];

        foreach ($marks as $mark => $isEnabled) {
            if (! $isEnabled) {
                continue;
            }

            $content[] = [
                'type' => 'text',
                'text' => ' ' . $this->faker->words($this->faker->numberBetween(1, 4), true),
                'marks' => [['type' => $mark]],
            ];
        }

        if ($links) {
            $content[] = [
                'type' => 'text',
                'text' => ' ' . $this->faker->words($this->faker->numberBetween(2, 5), true),
                'marks' => [[
                    'type' => 'link',
                    'attrs' => ['href' => $this->makeLinkUrl()],
                ]],
            ];
        }

        $content[] = [
            'type' => 'text',
            'text' => '.',
        ];

        return [
            'type' => 'paragraph',
            'content' => $content,
        ];
    }

    protected function makeLinkUrl(): string
    {
        $protocols = $this->attribute?->getLinkProtocols();

        if ($protocols === null) {
            return $this->faker->url();
        }

        if ($protocols === []) {
            return '/' . $this->faker->slug();
        }

        $protocol = $this->faker->randomElement($protocols);

        return match ($protocol) {
            'http', 'https', 'ftp', 'ftps' => "{$protocol}://{$this->faker->domainName()}/{$this->faker->slug()}",
            'mailto' => "mailto:{$this->faker->safeEmail()}",
            'tel', 'callto', 'sms' => "{$protocol}:{$this->faker->numerify('+1##########')}",
            'cid' => "cid:{$this->faker->uuid()}",
            'xmpp' => "xmpp:{$this->faker->userName()}@{$this->faker->domainName()}",
            default => "{$protocol}:{$this->faker->slug()}",
        };
    }
}
