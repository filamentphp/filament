<?php

use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
});

it('can `trim()` whitespace from `Textarea`', function (mixed $input, mixed $expected): void {
    livewire(TestComponentWithTextareaTrim::class)
        ->fillForm(['description' => $input])
        ->call('save')
        ->assertSet('data.description', $expected);
})->with([
    ["  multiline\ntext content  ", "multiline\ntext content"],
    ['test content', 'test content'],
    [null, null],
    ['', ''],
    [123, 123],
    ["\t test content \n", 'test content'],
    ['　test content　', 'test content'],
    [" \t　test content　\n ", 'test content'],
    ['   ', ''],
    ["\t\n　", ''],
]);

it('can set `autosize()`', function (): void {
    $textarea = Textarea::make('content');

    expect($textarea->shouldAutosize())->toBeFalse();

    $textarea->autosize();

    expect($textarea->shouldAutosize())->toBeTrue();
});

it('can set `cols()`', function (): void {
    $textarea = Textarea::make('content');

    expect($textarea->getCols())->toBeNull();

    $textarea->cols(40);

    expect($textarea->getCols())->toBe(40);
});

it('can set `rows()`', function (): void {
    $textarea = Textarea::make('content');

    expect($textarea->getRows())->toBeNull();

    $textarea->rows(10);

    expect($textarea->getRows())->toBe(10);
});

it('can set `autosize()` with a `Closure`', function (): void {
    $textarea = Textarea::make('content')
        ->autosize(static fn (): bool => true);

    expect($textarea->shouldAutosize())->toBeTrue();
});

it('can undo `autosize()` with `false`', function (): void {
    $textarea = Textarea::make('content')
        ->autosize()
        ->autosize(false);

    expect($textarea->shouldAutosize())->toBeFalse();
});

it('can set `cols()` with a `Closure`', function (): void {
    $textarea = Textarea::make('content')
        ->cols(static fn (): int => 60);

    expect($textarea->getCols())->toBe(60);
});

it('can set `rows()` with a `Closure`', function (): void {
    $textarea = Textarea::make('content')
        ->rows(static fn (): int => 15);

    expect($textarea->getRows())->toBe(15);
});

it('can clear `cols()` with `null`', function (): void {
    $textarea = Textarea::make('content')
        ->cols(40)
        ->cols(null);

    expect($textarea->getCols())->toBeNull();
});

it('can clear `rows()` with `null`', function (): void {
    $textarea = Textarea::make('content')
        ->rows(10)
        ->rows(null);

    expect($textarea->getRows())->toBeNull();
});

class TestComponentWithTextareaTrim extends Livewire
{
    public $data = [];

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Textarea::make('description')
                    ->trim(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->data = $this->form->getState();
    }
}

describe('autocomplete', function (): void {
    it('returns `null` for `getAutocomplete()` by default', function (): void {
        $textarea = Textarea::make('content');

        expect($textarea->getAutocomplete())->toBeNull();
    });

    it('returns `on` for `getAutocomplete()` when set to `true`', function (): void {
        $textarea = Textarea::make('content')->autocomplete(true);

        expect($textarea->getAutocomplete())->toBe('on');
    });

    it('returns `off` for `getAutocomplete()` when set to `false`', function (): void {
        $textarea = Textarea::make('content')->autocomplete(false);

        expect($textarea->getAutocomplete())->toBe('off');
    });

    it('returns custom string for `getAutocomplete()` when set to string', function (): void {
        $textarea = Textarea::make('content')->autocomplete('name');

        expect($textarea->getAutocomplete())->toBe('name');
    });

    it('can set `autocomplete()` with a `Closure`', function (): void {
        $textarea = Textarea::make('content')
            ->autocomplete(static fn (): string => 'email');

        expect($textarea->getAutocomplete())->toBe('email');
    });
});

describe('grammarly', function (): void {
    it('defaults `isGrammarlyDisabled()` to `false`', function (): void {
        $textarea = Textarea::make('content');

        expect($textarea->isGrammarlyDisabled())->toBeFalse();
    });

    it('can set `disableGrammarly()`', function (): void {
        $textarea = Textarea::make('content')->disableGrammarly();

        expect($textarea->isGrammarlyDisabled())->toBeTrue();
    });

    it('can set `disableGrammarly()` with a `Closure`', function (): void {
        $textarea = Textarea::make('content')
            ->disableGrammarly(static fn (): bool => true);

        expect($textarea->isGrammarlyDisabled())->toBeTrue();
    });
});

describe('read only', function (): void {
    it('defaults `isReadOnly()` to `false`', function (): void {
        $textarea = Textarea::make('content');

        expect($textarea->isReadOnly())->toBeFalse();
    });

    it('can set `readOnly()`', function (): void {
        $textarea = Textarea::make('content')->readOnly();

        expect($textarea->isReadOnly())->toBeTrue();
    });
});

describe('placeholder', function (): void {
    it('returns `null` for `getPlaceholder()` by default', function (): void {
        $textarea = Textarea::make('content');

        expect($textarea->getPlaceholder())->toBeNull();
    });

    it('can set `placeholder()`', function (): void {
        $textarea = Textarea::make('content')
            ->placeholder('Enter your text...');

        expect($textarea->getPlaceholder())->toBe('Enter your text...');
    });

    it('can set `placeholder()` with a `Closure`', function (): void {
        $textarea = Textarea::make('content')
            ->placeholder(static fn (): string => 'Dynamic');

        expect($textarea->getPlaceholder())->toBe('Dynamic');
    });
});

describe('rendering', function (): void {
    it('can render with `autosize()`', function (): void {
        livewire(RenderTextareaWithAutosize::class)->assertSuccessful();
    });

    it('can render with `autosize()` set via `Closure`', function (): void {
        livewire(RenderTextareaWithClosureAutosize::class)->assertSuccessful();
    });

    it('can render with `autosize(false)`', function (): void {
        livewire(RenderTextareaWithAutosizeFalse::class)->assertSuccessful();
    });

    it('can render with `cols()`', function (): void {
        livewire(RenderTextareaWithCols::class)->assertSuccessful();
    });

    it('can render with `cols()` set via `Closure`', function (): void {
        livewire(RenderTextareaWithClosureCols::class)->assertSuccessful();
    });

    it('can render with `cols(null)`', function (): void {
        livewire(RenderTextareaWithNullCols::class)->assertSuccessful();
    });

    it('can render with `rows()`', function (): void {
        livewire(RenderTextareaWithRows::class)->assertSuccessful();
    });

    it('can render with `rows()` set via `Closure`', function (): void {
        livewire(RenderTextareaWithClosureRows::class)->assertSuccessful();
    });

    it('can render with `rows(null)`', function (): void {
        livewire(RenderTextareaWithNullRows::class)->assertSuccessful();
    });

    it('can render with `autocomplete(true)`', function (): void {
        livewire(RenderTextareaWithAutocompleteTrue::class)->assertSuccessful();
    });

    it('can render with `autocomplete(false)`', function (): void {
        livewire(RenderTextareaWithAutocompleteFalse::class)->assertSuccessful();
    });

    it('can render with `autocomplete()` string', function (): void {
        livewire(RenderTextareaWithAutocompleteString::class)->assertSuccessful();
    });

    it('can render with `autocomplete()` set via `Closure`', function (): void {
        livewire(RenderTextareaWithClosureAutocomplete::class)->assertSuccessful();
    });

    it('can render with `disableGrammarly()`', function (): void {
        livewire(RenderTextareaWithDisableGrammarly::class)->assertSuccessful();
    });

    it('can render with `disableGrammarly()` set via `Closure`', function (): void {
        livewire(RenderTextareaWithClosureDisableGrammarly::class)->assertSuccessful();
    });

    it('can render with `readOnly()`', function (): void {
        livewire(RenderTextareaWithReadOnly::class)->assertSuccessful();
    });

    it('can render with `placeholder()`', function (): void {
        livewire(RenderTextareaWithPlaceholder::class)->assertSuccessful();
    });

    it('can render with `placeholder()` set via `Closure`', function (): void {
        livewire(RenderTextareaWithClosurePlaceholder::class)->assertSuccessful();
    });
});

it('can render `Textarea` in the browser', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/textarea-test')
            ->assertSee('Test Textarea')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        visit('/textarea-test')
            ->inDarkMode()
            ->assertNoAccessibilityIssues();
    });
});

class RenderTextareaWithAutosize extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Textarea::make('content')->autosize()])->statePath('data');
    }
}

class RenderTextareaWithClosureAutosize extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Textarea::make('content')->autosize(static fn (): bool => true)])->statePath('data');
    }
}

class RenderTextareaWithAutosizeFalse extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Textarea::make('content')->autosize()->autosize(false)])->statePath('data');
    }
}

class RenderTextareaWithCols extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Textarea::make('content')->cols(40)])->statePath('data');
    }
}

class RenderTextareaWithClosureCols extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Textarea::make('content')->cols(static fn (): int => 60)])->statePath('data');
    }
}

class RenderTextareaWithNullCols extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Textarea::make('content')->cols(40)->cols(null)])->statePath('data');
    }
}

class RenderTextareaWithRows extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Textarea::make('content')->rows(10)])->statePath('data');
    }
}

class RenderTextareaWithClosureRows extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Textarea::make('content')->rows(static fn (): int => 15)])->statePath('data');
    }
}

class RenderTextareaWithNullRows extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Textarea::make('content')->rows(10)->rows(null)])->statePath('data');
    }
}

class RenderTextareaWithAutocompleteTrue extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Textarea::make('content')->autocomplete(true)])->statePath('data');
    }
}

class RenderTextareaWithAutocompleteFalse extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Textarea::make('content')->autocomplete(false)])->statePath('data');
    }
}

class RenderTextareaWithAutocompleteString extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Textarea::make('content')->autocomplete('name')])->statePath('data');
    }
}

class RenderTextareaWithClosureAutocomplete extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Textarea::make('content')->autocomplete(static fn (): string => 'email')])->statePath('data');
    }
}

class RenderTextareaWithDisableGrammarly extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Textarea::make('content')->disableGrammarly()])->statePath('data');
    }
}

class RenderTextareaWithClosureDisableGrammarly extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Textarea::make('content')->disableGrammarly(static fn (): bool => true)])->statePath('data');
    }
}

class RenderTextareaWithReadOnly extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Textarea::make('content')->readOnly()])->statePath('data');
    }
}

class RenderTextareaWithPlaceholder extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Textarea::make('content')->placeholder('Enter your text...')])->statePath('data');
    }
}

class RenderTextareaWithClosurePlaceholder extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Textarea::make('content')->placeholder(static fn (): string => 'Dynamic')])->statePath('data');
    }
}
