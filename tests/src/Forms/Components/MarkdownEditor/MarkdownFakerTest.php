<?php

use Filament\Forms\Components\MarkdownEditor\MarkdownFaker;
use Filament\Forms\Components\RichEditor\RichContentFaker;
use Filament\Tests\TestCase;
use Illuminate\Support\Str;

uses(TestCase::class);

it('resolves `filamentMarkdown()` from the Faker provider', function (): void {
    expect(fake()->filamentMarkdown())->toBeInstanceOf(MarkdownFaker::class);
});

it('serializes an empty builder as an empty string', function (): void {
    expect(fake()->filamentMarkdown()->toString())->toBe('')
        ->and(fake()->filamentMarkdown()->paragraphs(0)->toString())->toBe('');
});

it('generates deterministic Markdown using the Faker seed', function (): void {
    fake()->seed(1234);

    $firstMarkdown = fake()->filamentMarkdown()
        ->article(depth: 2)
        ->toString();

    fake()->seed(1234);

    $secondMarkdown = fake()->filamentMarkdown()
        ->article(depth: 2)
        ->toString();

    expect($secondMarkdown)->toBe($firstMarkdown);
});

it('generates articles to the configured depth', function (): void {
    fake()->seed(5678);

    $markdown = fake()->filamentMarkdown()
        ->article(depth: 3)
        ->toString();

    expect($markdown)
        ->toMatch('/^.+\n\n## /s')
        ->toMatch('/^### /m')
        ->toMatch('/^#### /m')
        ->not->toMatch('/^##### /m');
});

it('rejects article depths that cannot map to heading levels', function (int $depth): void {
    fake()->filamentMarkdown()->article(depth: $depth);
})->with([0, 6])->throws(InvalidArgumentException::class);

it('generates all supported block content as valid Markdown', function (): void {
    $markdown = fake()->filamentMarkdown()
        ->heading(level: 3)
        ->paragraphs(2)
        ->bulletList(items: 2)
        ->orderedList(items: 2)
        ->blockquote(paragraphs: 2)
        ->codeBlock(language: 'php', code: '<?php echo true;')
        ->horizontalRule()
        ->table(columns: 2, rows: 2)
        ->image('https://example.com/image.jpg', alt: 'Example image')
        ->toString();

    $html = Str::markdown($markdown);

    expect($html)
        ->toContain('<h3>')
        ->toContain('<p>')
        ->toContain('<ul>')
        ->toContain('<ol>')
        ->toContain('<blockquote>')
        ->toContain('<pre><code class="language-php">')
        ->toContain('<hr />')
        ->toContain('<table>')
        ->toContain('<img src="https://example.com/image.jpg" alt="Example image" />');
});

it('keeps format-specific macros isolated', function (): void {
    RichContentFaker::macro('formatName', static fn (): string => 'rich');
    MarkdownFaker::macro('formatName', static fn (): string => 'markdown');

    try {
        expect(fake()->filamentRichContent()->formatName())->toBe('rich')
            ->and(fake()->filamentMarkdown()->formatName())->toBe('markdown');

        MarkdownFaker::flushMacros();

        expect(RichContentFaker::hasMacro('formatName'))->toBeTrue()
            ->and(MarkdownFaker::hasMacro('formatName'))->toBeFalse();
    } finally {
        RichContentFaker::flushMacros();
        MarkdownFaker::flushMacros();
    }
});

it('keeps closing fences inside code blocks', function (): void {
    $code = "Before\n```\nInside\n````\nAfter";
    $markdown = fake()->filamentMarkdown()
        ->codeBlock(language: 'markdown', code: $code)
        ->paragraphs()
        ->toString();

    $html = Str::markdown($markdown);

    expect($html)
        ->toContain('<code class="language-markdown">Before')
        ->toContain('```')
        ->toContain('````')
        ->toMatch('/After\s*<\/code><\/pre>/')
        ->and(substr_count($html, '<pre><code'))->toBe(1)
        ->and($html)->toMatch('/<\/pre>\s*<p>.+<\/p>/s');
});

it('separates tilde code fences from info strings', function (): void {
    $html = Str::markdown(
        fake()->filamentMarkdown()
            ->codeBlock(language: '~`', code: "Before\n~~~~\nAfter")
            ->paragraphs()
            ->toString(),
    );

    expect(substr_count($html, '<pre><code'))->toBe(1)
        ->and($html)->toContain('Before')
        ->toContain('~~~~')
        ->toMatch('/After\s*<\/code><\/pre>/')
        ->toMatch('/<\/pre>\s*<p>.+<\/p>/s');
});

it('escapes image alternative text and destinations', function (): void {
    $markdown = fake()->filamentMarkdown()
        ->image(
            'https://example.com/charts/report)&copy;.png',
            alt: 'Use `items[0]` for *draft* \\ files &copy; — “日本語”。',
        )
        ->toString();

    $html = Str::markdown($markdown);

    expect($html)
        ->toContain('src="https://example.com/charts/report)&amp;copy;.png"')
        ->toContain('alt="Use `items[0]` for *draft* \\ files &amp;copy; — “日本語”。"');
});

it('generates requested inline formatting', function (): void {
    $markdown = fake()->filamentMarkdown()
        ->paragraphs(
            links: true,
            bold: true,
            italic: true,
            strike: true,
            code: true,
        )
        ->toString();

    $html = Str::markdown($markdown);

    expect($markdown)
        ->toMatch('/\*\*[^*]+\*\*/')
        ->toMatch('/(?<!\*)\*[^*]+\*(?!\*)/')
        ->toMatch('/~~[^~]+~~/')
        ->toMatch('/`[^`]+`/')
        ->toMatch('/\[[^]]+]\(https?:\/\/[^)]+\)/')
        ->and($html)
        ->toContain('<strong>')
        ->toContain('<em>')
        ->toContain('<del>')
        ->toContain('<code>')
        ->toContain('<a href="');
});

it('inserts a hard break into a paragraph', function (): void {
    $markdown = fake()->filamentMarkdown()
        ->heading()
        ->hardBreak()
        ->toString();

    expect($markdown)->toContain("  \n")
        ->and(Str::markdown($markdown))->toContain('<br />');
});

it('generates deterministic image URLs using an available image service', function (): void {
    fake()->seed(2345);

    $firstImage = fake()->filamentMarkdown()
        ->image(width: 640, height: 480)
        ->toString();

    fake()->seed(2345);

    $secondImage = fake()->filamentMarkdown()
        ->image(width: 640, height: 480)
        ->toString();

    expect($firstImage)
        ->toBe($secondImage)
        ->toContain('](<https://picsum.photos/seed/')
        ->toEndWith('/640/480>)');
});

it('rejects invalid block configuration', function (Closure $generate): void {
    $generate(fake()->filamentMarkdown());
})->with([
    'heading below level 1' => [static fn (MarkdownFaker $faker): MarkdownFaker => $faker->heading(0)],
    'heading above level 6' => [static fn (MarkdownFaker $faker): MarkdownFaker => $faker->heading(7)],
    'negative paragraph count' => [static fn (MarkdownFaker $faker): MarkdownFaker => $faker->paragraphs(-1)],
    'empty bullet list' => [static fn (MarkdownFaker $faker): MarkdownFaker => $faker->bulletList(0)],
    'empty ordered list' => [static fn (MarkdownFaker $faker): MarkdownFaker => $faker->orderedList(0)],
    'empty blockquote' => [static fn (MarkdownFaker $faker): MarkdownFaker => $faker->blockquote(0)],
    'table without columns' => [static fn (MarkdownFaker $faker): MarkdownFaker => $faker->table(columns: 0)],
    'table without rows' => [static fn (MarkdownFaker $faker): MarkdownFaker => $faker->table(rows: 0)],
    'image without width' => [static fn (MarkdownFaker $faker): MarkdownFaker => $faker->image(width: 0)],
    'image without height' => [static fn (MarkdownFaker $faker): MarkdownFaker => $faker->image(height: 0)],
])->throws(InvalidArgumentException::class);
