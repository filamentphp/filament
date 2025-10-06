<?php

namespace Filament\Forms\Components\RichEditor;

use Faker\Factory;
use Faker\Generator;
use Illuminate\Support\Str;

class RichContentFaker
{
    protected Generator $faker;

    protected string $output = '';

    protected ?RichContentRenderer $renderer = null;

    final public function __construct()
    {
        // Prevent direct instantiation
        // Use the static make() method instead
    }

    public static function make(): static
    {
        $static = new static;
        $static->faker = Factory::create();

        return $static;
    }

    public function renderUsing(RichContentRenderer $renderer): static
    {
        $this->renderer = $renderer;

        return $this;
    }

    public function getRenderer(): RichContentRenderer
    {
        return $this->renderer ?? RichContentRenderer::make();
    }

    public function asHtml(): string
    {
        return $this->output;
    }

    /**
     * @return array<string, mixed>
     */
    public function asJson(): array
    {
        return $this->getRenderer()->content($this->output)->toArray();
    }

    public function asText(): string
    {
        return $this->getRenderer()->content($this->output)->toText();
    }

    public function heading(int | string | null $level = 2): static
    {
        $this->output .= '<h' . $level . '>' . Str::title($this->faker->words(mt_rand(3, 8), true)) . '</h' . $level . '>';

        return $this;
    }

    public function emptyParagraph(): static
    {
        $this->output .= '<p></p>';

        return $this;
    }

    public function paragraphs(int $count = 1, bool $withRandomLinks = false): static
    {
        if ($withRandomLinks) {
            $this->output .= '<p>' . collect($this->faker->paragraphs($count))->map(function ($paragraph): string {
                $pos = mt_rand(3, strlen($paragraph));

                $start = substr($paragraph, 0, $pos);
                $end = substr($paragraph, $pos);

                $link = ' ' . $this->generateLink() . ' ';

                return $start . $link . $end;
            })->implode('</p><p>') . '</p>';
        } else {
            $this->output .= '<p>' . collect($this->faker->paragraphs($count))->implode('</p><p>') . '</p>';
        }

        return $this;
    }

    public function link(): static
    {
        $this->output .= $this->generateLink();

        return $this;
    }

    public function lead(int $paragraphs = 1): static
    {
        $this->output .= '<div class="lead"><p>' . collect($this->faker->paragraphs($paragraphs))->implode('</p><p>') . '</p></div>';

        return $this;
    }

    public function small(): static
    {
        $this->output .= '<p><small>' . $this->faker->words(mt_rand(3, 8), true) . '</small></p>';

        return $this;
    }

    public function unorderedList(int $count = 1): static
    {
        $this->output .= '<ul><li>' . collect($this->faker->paragraphs($count))->implode('</li><li>') . '</li></ul>';

        return $this;
    }

    public function orderedList(int $count = 1): static
    {
        $this->output .= '<ol><li>' . collect($this->faker->paragraphs($count))->implode('</li><li>') . '</li></ol>';

        return $this;
    }

    public function image(?string $source = null, ?int $width = 1280, ?int $height = 720): static
    {
        if ($source === null || $source === '' || $source === '0') {
            $source = 'https://picsum.photos/' . $width . '/' . $height;
        }

        $this->output .= '<img src="' . $source . '" alt="' . $this->faker->sentence . '" />';

        return $this;
    }

    public function details(bool $open = false): static
    {
        $this->output .= '<details' . ($open ? ' open' : null) . '><summary>' . $this->faker->sentence() . '</summary><div data-type="detailsContent"><p>' . $this->faker->paragraph() . '</p></div></details>';

        return $this;
    }

    public function code(?string $className = null): static
    {
        $this->output .= "<code class=\"{$className}\">" . $this->faker->words(mt_rand(3, 5), true) . '</code>';

        return $this;
    }

    public function codeBlock(?string $language = 'sh'): static
    {
        $this->output .= "<pre><code class=\"language-{$language}\">export default function testComponent({\n\tstate,\n}) {\n\treturn {\n\t\tstate,\n\t\tinit: function () {\n\t\t\t// Initialize the Alpine component here, if you need to.\n\t\t},\n\t}\n}</code></pre>";

        return $this;
    }

    public function blockquote(): static
    {
        $this->output .= '<blockquote><p>' . $this->faker->paragraph() . '</p>' . '</blockquote>';

        return $this;
    }

    public function hr(): static
    {
        $this->output .= '<hr>';

        return $this;
    }

    public function br(): static
    {
        $this->output .= '<br>';

        return $this;
    }

    public function table(?int $cols = null): static
    {
        $cols ??= mt_rand(3, 8);

        $this->output .= '<table><thead><tr><th>' . collect($this->faker->words($cols))->implode('</th><th>') . '</th></tr></thead><tbody><tr><td>' . collect($this->faker->words($cols))->implode('</td><td>') . '</td></tr><tr><td>' . collect($this->faker->words($cols))->implode('</td><td>') . '</td></tr></tbody></table>';

        return $this;
    }

    /**
     * @param  array<int>  $cols
     */
    public function grid(array $cols = [1, 1, 1], string $breakpoint = 'md'): static
    {
        $this->output .= '<div class="grid-layout" data-cols="' . count($cols) . '" data-from-breakpoint="' . $breakpoint . '" style="grid-template-columns: repeat(' . count($cols) . ', 1fr);">';

        foreach ($cols as $col) {
            $this->output .= '<div class="grid-layout-col" data-col-span="' . $col . '" style="grid-column: span ' . $col . ';"><h2>' . Str::title($this->faker->words(mt_rand(3, 8), true)) . '</h2><p>' . $this->faker->paragraph() . '</p></div>';
        }

        $this->output .= '</div>';

        return $this;
    }

    private function generateLink(): string
    {
        return '<a href="' . $this->faker->url() . '">' . $this->faker->words(mt_rand(3, 8), true) . '</a>';
    }
}
