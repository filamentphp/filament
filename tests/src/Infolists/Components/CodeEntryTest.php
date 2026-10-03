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

it('injects raw array `$state` and `$relatedRecord` into copying and tooltip evaluations', function (): void {
    $author = User::factory()->create(['json' => ['code' => 'echo true;']]);
    $post = Post::factory()->create(['author_id' => $author->getKey()]);

    $entry = CodeEntry::make('author.json')
        ->copyable(static fn (array $state, User $relatedRecord): bool => ($state === $relatedRecord->json) && $relatedRecord->exists)
        ->copyableState(static fn (array $state, User $relatedRecord): string => "{$state['code']}:{$relatedRecord->email}")
        ->copyMessage(static fn (array $state, User $relatedRecord): string => "{$state['code']}:{$relatedRecord->getKey()}")
        ->tooltip(static fn (array $state, User $relatedRecord): string => "{$relatedRecord->email}:{$state['code']}")
        ->container(Schema::make(Livewire::make())->record($post));

    expect($entry->toHtml())
        ->toContain("echo true;:{$author->email}")
        ->toContain("echo true;:{$author->getKey()}")
        ->toContain("{$author->email}:echo true;");
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
