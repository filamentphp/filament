<?php

namespace Filament\Tests\Infolists\Components;

use DOMDocument;
use DOMXPath;
use Filament\Infolists\Components\CodeEntry;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Js;
use Livewire\Component;
use Phiki\Grammar\Grammar;
use Phiki\Theme\Theme;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render', function (): void {
    livewire(TestComponentWithCodeEntry::class)
        ->assertSuccessful()
        ->assertSeeText('echo "Hello World"');
});

it('can render with grammar highlighting', function (): void {
    livewire(TestComponentWithPhpCodeEntry::class)
        ->assertSuccessful();
});

it('renders malicious HTML as code text rather than active elements', function (): void {
    $html = CodeEntry::make('code')
        ->container(Schema::make(Livewire::make()))
        ->state('<script>alert(1)</script><img src="x" onerror="alert(2)"> & tail')
        ->toHtml();

    $document = new DOMDocument;
    $document->loadHTML($html);
    $xpath = new DOMXPath($document);

    expect($xpath->query('//script | //img')->length)->toBe(0)
        ->and(rtrim($xpath->query('//code')->item(0)->textContent, "\n"))
        ->toBe('<script>alert(1)</script><img src="x" onerror="alert(2)"> & tail');
});

it('injects the related model into copying and tooltip evaluations', function (): void {
    $author = User::factory()->create(['json' => ['code' => 'echo true;']]);
    $post = Post::factory()->create(['author_id' => $author->getKey()]);

    $entry = CodeEntry::make('author.json.code')
        ->copyable(static fn (User $relatedRecord): bool => $relatedRecord->exists)
        ->copyableState(static fn (User $relatedRecord): string => "copy-{$relatedRecord->email}")
        ->copyMessage(static fn (User $relatedRecord): string => "message-{$relatedRecord->email}")
        ->copyMessageDuration(static fn (User $relatedRecord): int => 1234)
        ->tooltip(static fn (User $relatedRecord): string => "tooltip-{$relatedRecord->email}")
        ->container(Schema::make(Livewire::make())->record($post));

    expect($entry->toHtml())
        ->toContain("copy-{$author->email}")
        ->toContain("message-{$author->email}")
        ->toContain('1234')
        ->toContain("tooltip-{$author->email}");
});

it('injects JSON `$state` and preserves both records in copying and tooltip evaluations', function (int $flags, string $expectedState): void {
    $author = User::factory()->create(['json' => ['url' => 'https://example.com/café']]);
    $post = Post::factory()->create(['author_id' => $author->getKey()]);
    $evaluatedMethods = [];

    $entry = CodeEntry::make('author.json')
        ->jsonFlags($flags)
        ->container(Schema::make(Livewire::make())->record($post));

    foreach (['copyable', 'copyableState', 'copyMessage', 'copyMessageDuration', 'tooltip'] as $method) {
        $entry->{$method}(static function (string $state, User $relatedRecord, Post $record) use ($author, &$evaluatedMethods, $expectedState, $method, $post): bool | int | string {
            expect($state)->toBe($expectedState)
                ->and($relatedRecord->is($author))->toBeTrue()
                ->and($record->is($post))->toBeTrue();

            $evaluatedMethods[] = $method;

            return match ($method) {
                'copyable' => true,
                'copyableState' => "Copy: {$state}",
                'copyMessage' => 'Copied code',
                'copyMessageDuration' => 1234,
                'tooltip' => 'Copy code',
            };
        });
    }

    $document = new DOMDocument;
    $document->loadHTML('<?xml encoding="UTF-8">' . $entry->toHtml());
    $xpath = new DOMXPath($document);
    $clickHandler = $xpath->query('//*[@*[name()="x-on:click"]]')->item(0)->getAttribute('x-on:click');

    expect($evaluatedMethods)->toBe(['copyable', 'copyableState', 'copyMessage', 'copyMessageDuration', 'tooltip'])
        ->and(rtrim($xpath->query('//code')->item(0)->textContent, "\n"))->toBe($expectedState)
        ->and($clickHandler)->toContain('clipboard.writeText(' . Js::from("Copy: {$expectedState}") . ')')
        ->toContain("\$tooltip('Copied code'")
        ->toContain('timeout: 1234');
})->with([
    'pretty JSON' => [JSON_PRETTY_PRINT, "{\n    \"url\": \"https:\\/\\/example.com\\/caf\\u00e9\"\n}"],
    'unescaped JSON' => [JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE, '{"url":"https://example.com/café"}'],
]);

it('copies the displayed JSON by default for array and `Collection` state', function (bool $isCollection): void {
    $state = ['enabled' => true, 'retries' => 3];
    $expectedState = "{\n    \"enabled\": true,\n    \"retries\": 3\n}";

    $entry = CodeEntry::make('code')
        ->container(Schema::make(Livewire::make()))
        ->state($isCollection ? collect($state) : $state)
        ->copyable(static fn (string $state): bool => $state === $expectedState);

    $document = new DOMDocument;
    $document->loadHTML($entry->toHtml());
    $xpath = new DOMXPath($document);

    expect(rtrim($xpath->query('//code')->item(0)->textContent, "\n"))->toBe($expectedState)
        ->and($xpath->query('//*[@*[name()="x-on:click"]]')->item(0)->getAttribute('x-on:click'))
        ->toContain('clipboard.writeText(' . Js::from($expectedState) . ')');

    $entry->copyable(static fn (string $state): bool => $state !== $expectedState);

    expect($entry->toHtml())->not->toContain('clipboard.writeText');
})->with([false, true]);

it('copies JSON and custom string callback content in the browser', function (): void {
    Artisan::call('filament:assets');
    $this->actingAs(User::factory()->create());

    $expectedState = "{\n    \"enabled\": true,\n    \"retries\": 3\n}";

    foreach ([false, true] as $isDarkMode) {
        $page = visit('/infolist-entries-browser-test');

        if ($isDarkMode) {
            $page = $page->inDarkMode();
        }

        $page->script("Object.defineProperty(window.navigator, 'clipboard', { configurable: true, value: { writeText: async (value) => { window.__copiedText = value } } })");

        $page
            ->assertScript('document.querySelector(\'[data-testid="copyable-code"] code\').textContent.trim()', $expectedState)
            ->click('[data-testid="copyable-code"]')
            ->assertScript('window.__copiedText', $expectedState)
            ->click('[data-testid="custom-copy-code"]')
            ->assertScript('window.__copiedText', "Copy: {$expectedState}")
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();
    }
});

it('can set and get `grammar()`', function (): void {
    $entry = CodeEntry::make('code')->grammar(Grammar::Json);
    expect($entry->getGrammar())->toBe(Grammar::Json);
});

it('returns `null` for `getGrammar()` by default', function (): void {
    $entry = CodeEntry::make('code');
    expect($entry->getGrammar())->toBeNull();
});

it('can set and get `lightTheme()`', function (): void {
    $entry = CodeEntry::make('code')->lightTheme(Theme::GithubLight);
    expect($entry->getLightTheme())->toBe(Theme::GithubLight);
});

it('can set and get `darkTheme()`', function (): void {
    $entry = CodeEntry::make('code')->darkTheme(Theme::GithubDarkHighContrast);
    expect($entry->getDarkTheme())->toBe(Theme::GithubDarkHighContrast);
});

it('returns `null` for `getLightTheme()` by default', function (): void {
    $entry = CodeEntry::make('code');
    expect($entry->getLightTheme())->toBeNull();
});

it('returns `null` for `getDarkTheme()` by default', function (): void {
    $entry = CodeEntry::make('code');
    expect($entry->getDarkTheme())->toBeNull();
});

it('can set and get `jsonFlags()`', function (): void {
    $entry = CodeEntry::make('code')->jsonFlags(JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    expect($entry->getJsonFlags())->toBe(JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
});

it('has a default `getJsonFlags()` of `JSON_PRETTY_PRINT`', function (): void {
    $entry = CodeEntry::make('code');
    expect($entry->getJsonFlags())->toBe(JSON_PRETTY_PRINT);
});

it('can set `grammar()` with a `Closure`', function (): void {
    $entry = CodeEntry::make('code')
        ->grammar(static fn () => Grammar::Php);

    expect($entry->getGrammar())->toBe(Grammar::Php);
});

it('can set `lightTheme()` with a `Closure`', function (): void {
    $entry = CodeEntry::make('code')
        ->lightTheme(static fn () => Theme::GithubLight);

    expect($entry->getLightTheme())->toBe(Theme::GithubLight);
});

it('can set `darkTheme()` with a `Closure`', function (): void {
    $entry = CodeEntry::make('code')
        ->darkTheme(static fn () => Theme::GithubDarkHighContrast);

    expect($entry->getDarkTheme())->toBe(Theme::GithubDarkHighContrast);
});

it('can set `jsonFlags()` with a `Closure`', function (): void {
    $entry = CodeEntry::make('code')
        ->jsonFlags(static fn (): int => JSON_UNESCAPED_SLASHES);

    expect($entry->getJsonFlags())->toBe(JSON_UNESCAPED_SLASHES);
});

describe('rendering', function (): void {
    it('can render with `grammar()` set via `Closure`', function (): void {
        livewire(RenderCodeEntryWithClosureGrammar::class)->assertSuccessful();
    });

    it('can render with `lightTheme()`', function (): void {
        livewire(RenderCodeEntryWithLightTheme::class)->assertSuccessful();
    });

    it('can render with `lightTheme()` set via `Closure`', function (): void {
        livewire(RenderCodeEntryWithClosureLightTheme::class)->assertSuccessful();
    });

    it('can render with `darkTheme()`', function (): void {
        livewire(RenderCodeEntryWithDarkTheme::class)->assertSuccessful();
    });

    it('can render with `darkTheme()` set via `Closure`', function (): void {
        livewire(RenderCodeEntryWithClosureDarkTheme::class)->assertSuccessful();
    });

    it('can render with `jsonFlags()`', function (): void {
        livewire(RenderCodeEntryWithJsonFlags::class)->assertSuccessful();
    });

    it('can render with `jsonFlags()` set via `Closure`', function (): void {
        livewire(RenderCodeEntryWithClosureJsonFlags::class)->assertSuccessful();
    });
});

class TestComponentWithCodeEntry extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'code' => 'echo "Hello World"',
            ])
            ->components([
                CodeEntry::make('code'),
            ]);
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->infolist }}
            </div>
            BLADE;
    }
}

class TestComponentWithPhpCodeEntry extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'php_code' => '<?php echo "Hello"; ?>',
            ])
            ->components([
                CodeEntry::make('php_code')
                    ->grammar(Grammar::Php),
            ]);
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->infolist }}
            </div>
            BLADE;
    }
}

class RenderCodeEntryWithClosureGrammar extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['code' => '<?php echo "Hello"; ?>'])->components([
            CodeEntry::make('code')->grammar(static fn () => Grammar::Php),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderCodeEntryWithLightTheme extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['code' => 'echo "test"'])->components([
            CodeEntry::make('code')->lightTheme(Theme::GithubLight),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderCodeEntryWithClosureLightTheme extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['code' => 'echo "test"'])->components([
            CodeEntry::make('code')->lightTheme(static fn () => Theme::GithubLight),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderCodeEntryWithDarkTheme extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['code' => 'echo "test"'])->components([
            CodeEntry::make('code')->darkTheme(Theme::GithubDarkHighContrast),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderCodeEntryWithClosureDarkTheme extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['code' => 'echo "test"'])->components([
            CodeEntry::make('code')->darkTheme(static fn () => Theme::GithubDarkHighContrast),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderCodeEntryWithJsonFlags extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['code' => ['key' => 'value']])->components([
            CodeEntry::make('code')->jsonFlags(JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderCodeEntryWithClosureJsonFlags extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['code' => ['key' => 'value']])->components([
            CodeEntry::make('code')->jsonFlags(static fn (): int => JSON_UNESCAPED_SLASHES),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}
