<?php

use Filament\Forms\Components\TagsInput;
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

it('can trim whitespace from TagsInput array values', function (mixed $input, mixed $expected): void {
    livewire(TestComponentWithTagsInputTrim::class)
        ->fillForm(['tags' => $input])
        ->call('save')
        ->assertSet('data.tags', $expected);
})->with([
    [['  tag1  ', '  tag2  ', 'tag3'], ['tag1', 'tag2', 'tag3']],
    [['tag1', 'tag2', 'tag3'], ['tag1', 'tag2', 'tag3']],
    [null, null],
    [[], []],
    [['  tag1  ', 123, '  tag2  '], ['tag1', 123, 'tag2']],
    [["\t tag1 \n", "\n tag2 \t"], ['tag1', 'tag2']],
    [['　tag1　', '　tag2　'], ['tag1', 'tag2']],
    [[" \t　tag1　\n ", "　 tag2\t "], ['tag1', 'tag2']],
    [['   ', "\t\n　"], ['', '']],
    [['  tag1  ', null, true, '　tag2　'], ['tag1', null, true, 'tag2']],
]);

it('can strip characters from TagsInput array values', function (mixed $input, mixed $expected): void {
    livewire(TestComponentWithTagsInputStripCharacters::class)
        ->fillForm(['tags' => $input])
        ->call('save')
        ->assertSet('data.tags', $expected);
})->with([
    [['tag,1', 'tag.2', 'tag3'], ['tag1', 'tag2', 'tag3']],
    [['tag1', 'tag2', 'tag3'], ['tag1', 'tag2', 'tag3']],
    [null, null],
    [[], []],
]);

class TestComponentWithTagsInputTrim extends Livewire
{
    public $data = [];

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TagsInput::make('tags')
                    ->trim(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->data = $this->form->getState();
    }
}

class TestComponentWithTagsInputStripCharacters extends Livewire
{
    public $data = [];

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TagsInput::make('tags')
                    ->stripCharacters([',', '.']),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->data = $this->form->getState();
    }
}

it('can set `separator()`', function (): void {
    $input = TagsInput::make('tags');

    expect($input->getSeparator())->toBeNull();

    $input->separator(',');

    expect($input->getSeparator())->toBe(',');
});

it('can set `splitKeys()`', function (): void {
    $input = TagsInput::make('tags')
        ->splitKeys(['Tab', ',']);

    expect($input->getSplitKeys())->toBe(['Tab', ',']);
});

it('can set `suggestions()`', function (): void {
    $input = TagsInput::make('tags')
        ->suggestions(['PHP', 'Laravel', 'Filament']);

    expect($input->getSuggestions())->toBe(['PHP', 'Laravel', 'Filament']);
});

it('can set `reorderable()`', function (): void {
    $input = TagsInput::make('tags');

    expect($input->isReorderable())->toBeFalse();

    $input->reorderable();

    expect($input->isReorderable())->toBeTrue();
});

it('can set `tagPrefix()`', function (): void {
    $input = TagsInput::make('tags');

    expect($input->getTagPrefix())->toBeNull();

    $input->tagPrefix('#');

    expect($input->getTagPrefix())->toBe('#');
});

it('can set `tagSuffix()`', function (): void {
    $input = TagsInput::make('tags');

    expect($input->getTagSuffix())->toBeNull();

    $input->tagSuffix('!');

    expect($input->getTagSuffix())->toBe('!');
});

it('defaults `getSplitKeys()` to empty array', function (): void {
    $input = TagsInput::make('tags');

    expect($input->getSplitKeys())->toBe([]);
});

it('defaults `getSuggestions()` to empty array', function (): void {
    $input = TagsInput::make('tags');

    expect($input->getSuggestions())->toBe([]);
});

it('uses `","` as default separator when called without argument', function (): void {
    $input = TagsInput::make('tags')
        ->separator();

    expect($input->getSeparator())->toBe(',');
});

it('can set `separator()` with a `Closure`', function (): void {
    $input = TagsInput::make('tags')
        ->separator(static fn (): string => ';');

    expect($input->getSeparator())->toBe(';');
});

it('can set `splitKeys()` with a `Closure`', function (): void {
    $input = TagsInput::make('tags')
        ->splitKeys(static fn (): array => ['Enter', 'Tab']);

    expect($input->getSplitKeys())->toBe(['Enter', 'Tab']);
});

it('can set `suggestions()` with a `Closure`', function (): void {
    $input = TagsInput::make('tags')
        ->suggestions(static fn (): array => ['A', 'B', 'C']);

    expect($input->getSuggestions())->toBe(['A', 'B', 'C']);
});

it('can set `suggestions()` with an `Arrayable`', function (): void {
    $input = TagsInput::make('tags')
        ->suggestions(collect(['X', 'Y', 'Z']));

    expect($input->getSuggestions())->toBe(['X', 'Y', 'Z']);
});

it('can set `tagPrefix()` with a `Closure`', function (): void {
    $input = TagsInput::make('tags')
        ->tagPrefix(static fn (): string => '@');

    expect($input->getTagPrefix())->toBe('@');
});

it('can set `tagSuffix()` with a `Closure`', function (): void {
    $input = TagsInput::make('tags')
        ->tagSuffix(static fn (): string => '…');

    expect($input->getTagSuffix())->toBe('…');
});

it('can set `reorderable()` with a `Closure`', function (): void {
    $input = TagsInput::make('tags')
        ->reorderable(static fn (): bool => true);

    expect($input->isReorderable())->toBeTrue();
});

it('can clear `separator()` with `null`', function (): void {
    $input = TagsInput::make('tags')
        ->separator(',')
        ->separator(null);

    expect($input->getSeparator())->toBeNull();
});

describe('`afterStateHydrated` closure', function (): void {
    it('keeps array state as-is', function (): void {
        livewire(TagsInputWithSeparator::class)
            ->fillForm(['tags' => ['one', 'two']])
            ->assertSchemaStateSet(['tags' => ['one', 'two']]);
    });

    it('splits string state by separator during hydration', function (): void {
        $livewire = Livewire::make();

        Schema::make($livewire)
            ->statePath('data')
            ->components([
                TagsInput::make('tags')->separator(','),
            ])
            ->fill(['tags' => 'one,two,three']);

        expect($livewire->data['tags'])->toBe(['one', 'two', 'three']);
    });

    it('sets empty array when string state is blank with separator', function (): void {
        $livewire = Livewire::make();

        Schema::make($livewire)
            ->statePath('data')
            ->components([
                TagsInput::make('tags')->separator(','),
            ])
            ->fill(['tags' => '']);

        expect($livewire->data['tags'])->toBe([]);
    });

    it('sets empty array when no separator and state is string', function (): void {
        $livewire = Livewire::make();

        Schema::make($livewire)
            ->statePath('data')
            ->components([
                TagsInput::make('tags'),
            ])
            ->fill(['tags' => 'some string']);

        expect($livewire->data['tags'])->toBe([]);
    });
});

describe('`dehydrateStateUsing` closure', function (): void {
    it('joins array state with separator when set', function (): void {
        livewire(TagsInputWithSeparator::class)
            ->fillForm(['tags' => ['one', 'two', 'three']])
            ->assertSchemaStateSet(function (array $state): array {
                // The state in the form is the array, but dehydration
                // will join it with the separator when getState() is called
                expect($state['tags'])->toBe(['one', 'two', 'three']);

                return [];
            });
    });
});

class TagsInputWithSeparator extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TagsInput::make('tags')->separator(','),
            ])
            ->statePath('data');
    }
}

describe('rendering', function (): void {
    it('can render with `splitKeys()`', function (): void {
        livewire(RenderTagsInputWithSplitKeys::class)->assertSuccessful();
    });

    it('can render with `splitKeys()` set via `Closure`', function (): void {
        livewire(RenderTagsInputWithClosureSplitKeys::class)->assertSuccessful();
    });

    it('can render with `suggestions()`', function (): void {
        livewire(RenderTagsInputWithSuggestions::class)->assertSuccessful();
    });

    it('can render with `suggestions()` set via `Closure`', function (): void {
        livewire(RenderTagsInputWithClosureSuggestions::class)->assertSuccessful();
    });

    it('can render with `suggestions()` from `Arrayable`', function (): void {
        livewire(RenderTagsInputWithArrayableSuggestions::class)->assertSuccessful();
    });

    it('can render with `reorderable()`', function (): void {
        livewire(RenderTagsInputWithReorderable::class)->assertSuccessful();
    });

    it('can render with `reorderable()` set via `Closure`', function (): void {
        livewire(RenderTagsInputWithClosureReorderable::class)->assertSuccessful();
    });

    it('can render with `tagPrefix()`', function (): void {
        livewire(RenderTagsInputWithTagPrefix::class)->assertSuccessful();
    });

    it('can render with `tagPrefix()` set via `Closure`', function (): void {
        livewire(RenderTagsInputWithClosureTagPrefix::class)->assertSuccessful();
    });

    it('can render with `tagSuffix()`', function (): void {
        livewire(RenderTagsInputWithTagSuffix::class)->assertSuccessful();
    });

    it('can render with `tagSuffix()` set via `Closure`', function (): void {
        livewire(RenderTagsInputWithClosureTagSuffix::class)->assertSuccessful();
    });

    it('can render with `placeholder()`', function (): void {
        livewire(RenderTagsInputWithPlaceholder::class)->assertSuccessful();
    });
});

it('can add and remove tags in the browser', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/tags-input-test')
            ->assertSee('Tags Input Test')
            ->assertSee('Basic Tags')
            ->assertDontSee('MyNewTag')
            ->type('[data-testid="basic-tags"] input', 'MyNewTag')
            ->keys('[data-testid="basic-tags"] input', 'Enter')
            ->assertSee('MyNewTag')
            ->assertSee('MyNewTag')
            ->click('[data-testid="basic-tags"] .fi-badge-delete-btn')
            ->assertDontSee('MyNewTag')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        visit('/tags-input-test')
            ->inDarkMode()
            ->assertNoAccessibilityIssues();
    });
});

class RenderTagsInputWithSplitKeys extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([TagsInput::make('tags')->splitKeys(['Tab', ','])])->statePath('data');
    }
}

class RenderTagsInputWithClosureSplitKeys extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([TagsInput::make('tags')->splitKeys(static fn (): array => ['Enter', 'Tab'])])->statePath('data');
    }
}

class RenderTagsInputWithSuggestions extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([TagsInput::make('tags')->suggestions(['PHP', 'Laravel', 'Filament'])])->statePath('data');
    }
}

class RenderTagsInputWithClosureSuggestions extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([TagsInput::make('tags')->suggestions(static fn (): array => ['A', 'B', 'C'])])->statePath('data');
    }
}

class RenderTagsInputWithArrayableSuggestions extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([TagsInput::make('tags')->suggestions(collect(['X', 'Y', 'Z']))])->statePath('data');
    }
}

class RenderTagsInputWithReorderable extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([TagsInput::make('tags')->reorderable()])->statePath('data');
    }
}

class RenderTagsInputWithClosureReorderable extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([TagsInput::make('tags')->reorderable(static fn (): bool => true)])->statePath('data');
    }
}

class RenderTagsInputWithTagPrefix extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([TagsInput::make('tags')->tagPrefix('#')])->statePath('data');
    }
}

class RenderTagsInputWithClosureTagPrefix extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([TagsInput::make('tags')->tagPrefix(static fn (): string => '@')])->statePath('data');
    }
}

class RenderTagsInputWithTagSuffix extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([TagsInput::make('tags')->tagSuffix('!')])->statePath('data');
    }
}

class RenderTagsInputWithClosureTagSuffix extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([TagsInput::make('tags')->tagSuffix(static fn (): string => '…')])->statePath('data');
    }
}

class RenderTagsInputWithPlaceholder extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([TagsInput::make('tags')->placeholder('Add a tag...')])->statePath('data');
    }
}
