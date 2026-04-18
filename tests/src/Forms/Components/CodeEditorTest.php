<?php

namespace Filament\Tests\Forms\Components;

use Filament\Forms\Components\CodeEditor;
use Filament\Forms\Components\CodeEditor\Enums\Language;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\ComponentAttributeBag;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
});

it('can render', function (): void {
    livewire(TestComponentWithCodeEditor::class)
        ->assertSuccessful();
});

it('can set and get state', function (): void {
    livewire(TestComponentWithCodeEditor::class)
        ->fillForm(['code' => '<?php echo "Hello";'])
        ->assertSchemaStateSet(['code' => '<?php echo "Hello";']);
});

it('can render with PHP language', function (): void {
    livewire(TestComponentWithPhpCodeEditor::class)
        ->assertSuccessful();
});

it('can render with JavaScript language', function (): void {
    livewire(TestComponentWithJsCodeEditor::class)
        ->assertSuccessful();
});

describe('language', function (): void {
    it('returns `null` for `getLanguage()` by default', function (): void {
        $editor = CodeEditor::make('code');

        expect($editor->getLanguage())->toBeNull();
    });

    it('can set `language()` with an enum', function (): void {
        $editor = CodeEditor::make('code')
            ->language(Language::Php);

        expect($editor->getLanguage())->toBe(Language::Php);
    });

    it('can set `language()` with a `Closure`', function (): void {
        $editor = CodeEditor::make('code')
            ->language(static fn (): Language => Language::Json);

        expect($editor->getLanguage())->toBe(Language::Json);
    });

    it('can clear `language()` with `null`', function (): void {
        $editor = CodeEditor::make('code')
            ->language(Language::Css)
            ->language(null);

        expect($editor->getLanguage())->toBeNull();
    });
});

describe('wrapping', function (): void {
    it('defaults `canWrap()` to `false`', function (): void {
        $editor = CodeEditor::make('code');

        expect($editor->canWrap())->toBeFalse();
    });

    it('can set `wrap()`', function (): void {
        $editor = CodeEditor::make('code')->wrap();

        expect($editor->canWrap())->toBeTrue();
    });

    it('can set `wrap()` with a `Closure`', function (): void {
        $editor = CodeEditor::make('code')
            ->wrap(static fn (): bool => true);

        expect($editor->canWrap())->toBeTrue();
    });

    it('falls back to `canWrapByDefault()` when `wrap()` is set to `null`', function (): void {
        $editor = CodeEditor::make('code')
            ->wrap()
            ->wrap(null);

        expect($editor->canWrap())->toBeFalse();
    });
});

describe('extra Alpine attributes', function (): void {
    it('can set `extraAlpineAttributes()`', function (): void {
        $editor = CodeEditor::make('code')
            ->extraAlpineAttributes(['x-data' => '{}']);

        expect($editor->getExtraAlpineAttributes())->toBe(['x-data' => '{}']);
    });

    it('can merge `extraAlpineAttributes()`', function (): void {
        $editor = CodeEditor::make('code')
            ->extraAlpineAttributes(['x-data' => '{}'])
            ->extraAlpineAttributes(['x-init' => 'init()'], merge: true);

        $attributes = $editor->getExtraAlpineAttributes();

        expect($attributes)->toHaveKey('x-data', '{}');
        expect($attributes)->toHaveKey('x-init', 'init()');
    });

    it('replaces `extraAlpineAttributes()` without merge', function (): void {
        $editor = CodeEditor::make('code')
            ->extraAlpineAttributes(['x-data' => '{}'])
            ->extraAlpineAttributes(['x-init' => 'init()']);

        $attributes = $editor->getExtraAlpineAttributes();

        expect($attributes)->not->toHaveKey('x-data');
        expect($attributes)->toHaveKey('x-init', 'init()');
    });

    it('can set `extraAlpineAttributes()` with a `Closure`', function (): void {
        $editor = CodeEditor::make('code')
            ->extraAlpineAttributes(static fn (): array => ['x-ref' => 'editor']);

        expect($editor->getExtraAlpineAttributes())->toBe(['x-ref' => 'editor']);
    });

    it('returns `ComponentAttributeBag` from `getExtraAlpineAttributeBag()`', function (): void {
        $editor = CodeEditor::make('code')
            ->extraAlpineAttributes(['x-data' => '{}']);

        $bag = $editor->getExtraAlpineAttributeBag();

        expect($bag)->toBeInstanceOf(ComponentAttributeBag::class);
        expect($bag->get('x-data'))->toBe('{}');
    });
});

describe('rendering', function (): void {
    it('can render with `language()` set via `Closure`', function (): void {
        livewire(TestComponentWithClosureLanguageCodeEditor::class)
            ->assertSuccessful();
    });

    it('can render with `language()` cleared to `null`', function (): void {
        livewire(TestComponentWithNullLanguageCodeEditor::class)
            ->assertSuccessful();
    });

    it('can render with `wrap()`', function (): void {
        livewire(TestComponentWithWrapCodeEditor::class)
            ->assertSuccessful();
    });

    it('can render with `wrap()` set via `Closure`', function (): void {
        livewire(TestComponentWithClosureWrapCodeEditor::class)
            ->assertSuccessful();
    });

    it('can render with `wrap(null)` fallback', function (): void {
        livewire(TestComponentWithNullWrapCodeEditor::class)
            ->assertSuccessful();
    });

    it('can render with `extraAlpineAttributes()`', function (): void {
        livewire(TestComponentWithExtraAlpineAttributesCodeEditor::class)
            ->assertSuccessful();
    });

    it('can render with merged `extraAlpineAttributes()`', function (): void {
        livewire(TestComponentWithMergedExtraAlpineAttributesCodeEditor::class)
            ->assertSuccessful();
    });

    it('can render with replaced `extraAlpineAttributes()`', function (): void {
        livewire(TestComponentWithReplacedExtraAlpineAttributesCodeEditor::class)
            ->assertSuccessful();
    });

    it('can render with `extraAlpineAttributes()` set via `Closure`', function (): void {
        livewire(TestComponentWithClosureExtraAlpineAttributesCodeEditor::class)
            ->assertSuccessful();
    });
});

it('can render `CodeEditor` in the browser', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/code-editor-browser-test')
            ->assertSee('Test Code Editor')
            ->assertNoSmoke();
    });
});

class TestComponentWithCodeEditor extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                CodeEditor::make('code'),
            ])
            ->statePath('data');
    }
}

class TestComponentWithPhpCodeEditor extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                CodeEditor::make('code')->language(Language::Php),
            ])
            ->statePath('data');
    }
}

class TestComponentWithJsCodeEditor extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                CodeEditor::make('code')->language(Language::JavaScript),
            ])
            ->statePath('data');
    }
}

class TestComponentWithClosureLanguageCodeEditor extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                CodeEditor::make('code')
                    ->language(static fn (): Language => Language::Json),
            ])
            ->statePath('data');
    }
}

class TestComponentWithNullLanguageCodeEditor extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                CodeEditor::make('code')
                    ->language(Language::Css)
                    ->language(null),
            ])
            ->statePath('data');
    }
}

class TestComponentWithWrapCodeEditor extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                CodeEditor::make('code')->wrap(),
            ])
            ->statePath('data');
    }
}

class TestComponentWithClosureWrapCodeEditor extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                CodeEditor::make('code')
                    ->wrap(static fn (): bool => true),
            ])
            ->statePath('data');
    }
}

class TestComponentWithNullWrapCodeEditor extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                CodeEditor::make('code')
                    ->wrap()
                    ->wrap(null),
            ])
            ->statePath('data');
    }
}

class TestComponentWithExtraAlpineAttributesCodeEditor extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                CodeEditor::make('code')
                    ->extraAlpineAttributes(['x-data' => '{}']),
            ])
            ->statePath('data');
    }
}

class TestComponentWithMergedExtraAlpineAttributesCodeEditor extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                CodeEditor::make('code')
                    ->extraAlpineAttributes(['x-data' => '{}'])
                    ->extraAlpineAttributes(['x-init' => 'init()'], merge: true),
            ])
            ->statePath('data');
    }
}

class TestComponentWithReplacedExtraAlpineAttributesCodeEditor extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                CodeEditor::make('code')
                    ->extraAlpineAttributes(['x-data' => '{}'])
                    ->extraAlpineAttributes(['x-init' => 'init()']),
            ])
            ->statePath('data');
    }
}

class TestComponentWithClosureExtraAlpineAttributesCodeEditor extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                CodeEditor::make('code')
                    ->extraAlpineAttributes(static fn (): array => ['x-ref' => 'editor']),
            ])
            ->statePath('data');
    }
}
