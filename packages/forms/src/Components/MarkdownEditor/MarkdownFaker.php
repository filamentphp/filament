<?php

namespace Filament\Forms\Components\MarkdownEditor;

use Filament\Forms\Components\ContentFaker;
use Illuminate\Support\Traits\Macroable;
use InvalidArgumentException;

class MarkdownFaker extends ContentFaker
{
    use Macroable;

    /**
     * @var array<array{type: string, markdown: string}>
     */
    protected array $content = [];

    protected function addArticleIntroduction(): void
    {
        $this->paragraphs();
    }

    protected function addArticleHeading(int $level): void
    {
        $this->heading($level);
    }

    protected function addArticleParagraphs(int $count, bool $links, bool $bold, bool $italic): void
    {
        $this->paragraphs($count, $links, $bold, $italic);
    }

    public function heading(int $level = 2): static
    {
        if (($level < 1) || ($level > 6)) {
            throw new InvalidArgumentException('Heading level must be between 1 and 6.');
        }

        $this->content[] = [
            'type' => 'heading',
            'markdown' => str_repeat('#', $level) . ' ' . $this->makeHeadingText(),
        ];

        return $this;
    }

    public function paragraphs(
        int $count = 1,
        bool $links = false,
        bool $bold = false,
        bool $italic = false,
        bool $strike = false,
        bool $code = false,
    ): static {
        if ($count < 0) {
            throw new InvalidArgumentException('Paragraph count must be at least 0.');
        }

        for ($paragraph = 0; $paragraph < $count; $paragraph++) {
            $markdown = $this->makeParagraphText();

            foreach ([
                '**' => $bold,
                '*' => $italic,
                '~~' => $strike,
                '`' => $code,
            ] as $delimiter => $isEnabled) {
                if ($isEnabled) {
                    $markdown .= " {$delimiter}{$this->faker->words($this->faker->numberBetween(1, 4), true)}{$delimiter}";
                }
            }

            if ($links) {
                $label = $this->faker->words($this->faker->numberBetween(2, 5), true);
                $markdown .= " [{$label}]({$this->makeLinkUrl()})";
            }

            $this->content[] = [
                'type' => 'paragraph',
                'markdown' => $markdown . '.',
            ];
        }

        return $this;
    }

    public function bulletList(int $items = 3): static
    {
        return $this->list('-', $items);
    }

    public function orderedList(int $items = 3): static
    {
        return $this->list('1.', $items);
    }

    protected function list(string $marker, int $items): static
    {
        if ($items < 1) {
            throw new InvalidArgumentException('List item count must be at least 1.');
        }

        $lines = [];

        for ($item = 0; $item < $items; $item++) {
            $lines[] = "{$marker} {$this->makeParagraphText(sentence: true)}.";
        }

        $this->content[] = [
            'type' => 'list',
            'markdown' => implode("\n", $lines),
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
            $content[] = '> ' . $this->makeParagraphText() . '.';
        }

        $this->content[] = [
            'type' => 'blockquote',
            'markdown' => implode("\n>\n", $content),
        ];

        return $this;
    }

    public function codeBlock(?string $language = null, ?string $code = null): static
    {
        $code ??= $this->faker->text(120);
        $fenceCharacter = str_contains($language ?? '', '`') ? '~' : '`';
        preg_match_all('/' . preg_quote($fenceCharacter, '/') . '+/', $code, $fenceMatches);
        $fenceLength = max(3, 1 + max([0, ...array_map('strlen', $fenceMatches[0])]));
        $fence = str_repeat($fenceCharacter, $fenceLength);
        $info = filled($language) ? " {$language}" : '';

        $this->content[] = [
            'type' => 'codeBlock',
            'markdown' => "{$fence}{$info}\n{$code}\n{$fence}",
        ];

        return $this;
    }

    public function horizontalRule(): static
    {
        $this->content[] = [
            'type' => 'horizontalRule',
            'markdown' => '---',
        ];

        return $this;
    }

    public function hardBreak(): static
    {
        $paragraphIndexes = array_keys(array_filter(
            $this->content,
            static fn (array $block): bool => $block['type'] === 'paragraph',
        ));

        if ($paragraphIndexes === []) {
            $this->paragraphs();
            $paragraphIndexes = [array_key_last($this->content)];
        }

        $paragraphIndex = $this->faker->randomElement($paragraphIndexes);
        $this->content[$paragraphIndex]['markdown'] .= "  \n{$this->makeParagraphText(sentence: true)}.";

        return $this;
    }

    public function table(int $columns = 3, int $rows = 2): static
    {
        if (($columns < 1) || ($rows < 1)) {
            throw new InvalidArgumentException('Table column and row counts must be at least 1.');
        }

        $lines = [];
        $headers = [];

        for ($column = 0; $column < $columns; $column++) {
            $headers[] = $this->makeHeadingText();
        }

        $lines[] = '| ' . implode(' | ', $headers) . ' |';
        $lines[] = '| ' . implode(' | ', array_fill(0, $columns, '---')) . ' |';

        for ($row = 1; $row < $rows; $row++) {
            $cells = [];

            for ($column = 0; $column < $columns; $column++) {
                $cells[] = $this->faker->words($this->faker->numberBetween(1, 3), true);
            }

            $lines[] = '| ' . implode(' | ', $cells) . ' |';
        }

        $this->content[] = [
            'type' => 'table',
            'markdown' => implode("\n", $lines),
        ];

        return $this;
    }

    public function image(?string $url = null, int $width = 1280, int $height = 720, ?string $alt = null): static
    {
        if (($width < 1) || ($height < 1)) {
            throw new InvalidArgumentException('Image width and height must be at least 1.');
        }

        $alt = preg_replace_callback(
            '/[\x21-\x2F\x3A-\x40\x5B-\x60\x7B-\x7E]/',
            static fn (array $matches): string => '\\' . $matches[0],
            $alt ?? $this->faker->sentence(),
        );
        $url ??= $this->makeImageUrl($width, $height);
        $url = str_replace(['\\', '<', '>', '&'], ['\\\\', '\\<', '\\>', '&amp;'], $url);

        $this->content[] = [
            'type' => 'image',
            'markdown' => "![{$alt}](<{$url}>)",
        ];

        return $this;
    }

    public function toString(): string
    {
        return implode("\n\n", array_column($this->content, 'markdown'));
    }
}
