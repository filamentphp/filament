<?php

namespace Filament\Tests\Tables\Columns;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables;
use Filament\Tables\Columns\Column;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Tables\TestCase;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use PHPUnit\Framework\AssertionFailedError;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    $this->actingAs(User::factory()->create());

    Gate::define('updatePublishedPost', fn (User $user, Post $post): bool => (bool) $post->is_published);
    Gate::define('denyPost', fn (User $user, Post $post): Response => Response::deny('Nope.'));
});

it('allows updating editable columns without `authorize()`', function (string $columnName): void {
    $post = Post::factory()->create(['is_published' => false]);

    livewire(TestTableWithAuthorizedEditableColumns::class, ['authorizationMode' => 'none'])
        ->assertTableColumnEnabled($columnName, $post);
})->with(['is_published_toggle', 'is_published_checkbox', 'title', 'rating']);

it('does not apply a default policy check outside of resources', function (): void {
    $post = Post::factory()->create();

    livewire(TestTableWithAuthorizedEditableColumns::class, ['authorizationMode' => 'none'])
        ->assertTableColumnEnabled('is_published_toggle', $post)
        ->call('updateTableColumnState', 'is_published_toggle', (string) $post->getKey(), false);

    expect($post->fresh()->is_published)->toBeFalse();
});

it('denies the default `update` ability when no policy or gate is defined', function (string $columnName): void {
    $post = Post::factory()->create();

    livewire(TestTableWithAuthorizedEditableColumns::class, ['authorizationMode' => 'default'])
        ->assertTableColumnDisabled($columnName, $post);
})->with(['is_published_toggle', 'is_published_checkbox', 'title', 'rating']);

it('can opt each editable column type into authorization globally using `configureUsing()`', function (string $columnClass): void {
    Gate::define('update', fn (User $user, Post $post): bool => (bool) $post->is_published);

    $published = Post::factory()->create(['is_published' => true]);
    $draft = Post::factory()->create(['is_published' => false]);

    $columnClass::configureUsing(
        fn (Column $column) => $column->authorize(),
        during: function () use ($columnClass, $published, $draft): void {
            expect($columnClass::make('title')->record($published)->isDisabled())->toBeFalse()
                ->and($columnClass::make('title')->record($draft)->isDisabled())->toBeTrue()
                ->and($columnClass::make('title')->authorize(true)->record($draft)->isDisabled())->toBeFalse()
                ->and($columnClass::make('title')->authorize(null)->record($draft)->isDisabled())->toBeFalse()
                ->and($columnClass::make('title')->authorize('denyPost')->record($published)->isDisabled())->toBeTrue()
                ->and($columnClass::make('title')->authorize(true)->disabled()->record($published)->isDisabled())->toBeTrue();
        },
    );

    expect($columnClass::make('title')->record($draft)->isDisabled())->toBeFalse();
})->with([
    Tables\Columns\ToggleColumn::class,
    Tables\Columns\CheckboxColumn::class,
    Tables\Columns\TextInputColumn::class,
    Tables\Columns\SelectColumn::class,
]);

it('checks a custom gate ability against the record', function (string $columnName): void {
    $published = Post::factory()->create(['is_published' => true]);
    $draft = Post::factory()->create(['is_published' => false]);

    livewire(TestTableWithAuthorizedEditableColumns::class, ['authorizationMode' => 'ability'])
        ->assertTableColumnEnabled($columnName, $published)
        ->assertTableColumnDisabled($columnName, $draft);
})->with(['is_published_toggle', 'is_published_checkbox', 'title', 'rating']);

it('checks a custom gate ability against an array record', function (string $columnClass): void {
    Gate::define('updateArrayRecord', fn (User $user, array $record): bool => $record['is_published']);

    $column = $columnClass::make('title')->authorize('updateArrayRecord');

    $column->record(['title' => 'Published', 'is_published' => true]);

    expect($column->isDisabled())->toBeFalse();

    $column->record(['title' => 'Draft', 'is_published' => false]);

    expect($column->isDisabled())->toBeTrue();
})->with([
    Tables\Columns\ToggleColumn::class,
    Tables\Columns\CheckboxColumn::class,
    Tables\Columns\TextInputColumn::class,
    Tables\Columns\SelectColumn::class,
]);

it('accepts a backed enum as the ability', function (): void {
    $published = Post::factory()->create(['is_published' => true]);
    $draft = Post::factory()->create(['is_published' => false]);

    livewire(TestTableWithAuthorizedEditableColumns::class, ['authorizationMode' => 'enum'])
        ->assertTableColumnEnabled('title', $published)
        ->assertTableColumnDisabled('title', $draft);
});

it('accepts a static boolean', function (string $authorizationMode, bool $isEnabled): void {
    $post = Post::factory()->create();

    livewire(TestTableWithAuthorizedEditableColumns::class, ['authorizationMode' => $authorizationMode])
        ->{$isEnabled ? 'assertTableColumnEnabled' : 'assertTableColumnDisabled'}('title', $post);
})->with([
    'true' => ['true', true],
    'false' => ['false', false],
]);

it('accepts a closure returning a boolean with the record injected', function (): void {
    $published = Post::factory()->create(['is_published' => true]);
    $draft = Post::factory()->create(['is_published' => false]);

    livewire(TestTableWithAuthorizedEditableColumns::class, ['authorizationMode' => 'closure-bool'])
        ->assertTableColumnEnabled('title', $published)
        ->assertTableColumnDisabled('title', $draft);
});

it('accepts a closure returning a response', function (string $authorizationMode, bool $isEnabled): void {
    $post = Post::factory()->create();

    livewire(TestTableWithAuthorizedEditableColumns::class, ['authorizationMode' => $authorizationMode])
        ->{$isEnabled ? 'assertTableColumnEnabled' : 'assertTableColumnDisabled'}('title', $post);
})->with([
    'allowed' => ['closure-allow', true],
    'denied' => ['closure-deny', false],
]);

it('exposes the authorization response from the column', function (): void {
    $post = Post::factory()->create();

    $column = livewire(TestTableWithAuthorizedEditableColumns::class, ['authorizationMode' => 'gate-deny'])
        ->instance()
        ->getTable()
        ->getColumn('title')
        ->record($post);

    $response = $column->getAuthorizationResponse();

    expect($response->denied())->toBeTrue()
        ->and($response->message())->toBe('Nope.')
        ->and($column->isAuthorized())->toBeFalse()
        ->and($column->isDisabled())->toBeTrue();
});

it('is not disabled by authorization when there is no record', function (): void {
    $column = livewire(TestTableWithAuthorizedEditableColumns::class, ['authorizationMode' => 'false'])
        ->instance()
        ->getTable()
        ->getColumn('title');

    expect($column->isDisabled())->toBeFalse();
});

it('is still disabled by `disabled()` when authorized', function (): void {
    $post = Post::factory()->create();

    livewire(TestTableWithAuthorizedEditableColumns::class, ['authorizationMode' => 'true', 'shouldDisableColumns' => true])
        ->assertTableColumnDisabled('title', $post);
});

it('rejects state updates on the server when unauthorized', function (string $columnName, mixed $state, string $attributeName): void {
    $draft = Post::factory()->create(['is_published' => false, 'title' => 'Original', 'rating' => 1]);
    $originalState = $draft->getAttribute($attributeName);

    livewire(TestTableWithAuthorizedEditableColumns::class, ['authorizationMode' => 'ability'])
        ->call('updateTableColumnState', $columnName, (string) $draft->getKey(), $state)
        ->assertReturned(['error' => __('filament-tables::table.columns.editable.unavailable')]);

    expect($draft->fresh()->getAttribute($attributeName))->toBe($originalState);
})->with([
    'toggle' => ['is_published_toggle', true, 'is_published'],
    'checkbox' => ['is_published_checkbox', true, 'is_published'],
    'text input' => ['title', 'Changed', 'title'],
    'select' => ['rating', '2', 'rating'],
]);

it('checks only the disabled state of hidden and non-editable columns', function (string $columnName, bool $isDisabled): void {
    $post = Post::factory()->create();

    livewire(TestTableWithAuthorizedEditableColumns::class, ['shouldDisableColumns' => $isDisabled])
        ->{$isDisabled ? 'assertTableColumnDisabled' : 'assertTableColumnEnabled'}($columnName, $post);
})->with(['hidden_title', 'text'])->with([false, true]);

it('fails `assertTableColumnEnabled()` when unauthorized', function (): void {
    $post = Post::factory()->create();

    livewire(TestTableWithAuthorizedEditableColumns::class, ['authorizationMode' => 'false'])
        ->assertTableColumnEnabled('title', $post);
})->throws(AssertionFailedError::class);

it('fails `assertTableColumnDisabled()` when authorized', function (): void {
    $post = Post::factory()->create();

    livewire(TestTableWithAuthorizedEditableColumns::class, ['authorizationMode' => 'true'])
        ->assertTableColumnDisabled('title', $post);
})->throws(AssertionFailedError::class);

it('accepts a record key in the enabled and disabled column assertions', function (): void {
    $published = Post::factory()->create(['is_published' => true]);
    $draft = Post::factory()->create(['is_published' => false]);

    livewire(TestTableWithAuthorizedEditableColumns::class, ['authorizationMode' => 'ability'])
        ->assertTableColumnEnabled('title', $published->getKey())
        ->assertTableColumnDisabled('title', (string) $draft->getKey());
});

it('denies the default `update` ability when the gate denies it', function (): void {
    Gate::define('update', fn (User $user, Post $post): bool => (bool) $post->is_published);

    $published = Post::factory()->create(['is_published' => true]);
    $draft = Post::factory()->create(['is_published' => false]);

    livewire(TestTableWithAuthorizedEditableColumns::class, ['authorizationMode' => 'default'])
        ->assertTableColumnEnabled('title', $published)
        ->assertTableColumnDisabled('title', $draft);
});

it('keeps the denial message from a closure response', function (): void {
    $post = Post::factory()->create();

    $column = livewire(TestTableWithAuthorizedEditableColumns::class, ['authorizationMode' => 'closure-deny-message'])
        ->instance()
        ->getTable()
        ->getColumn('title')
        ->record($post);

    expect($column->getAuthorizationResponse()->message())->toBe('Not allowed.')
        ->and($column->isDisabled())->toBeTrue();
});

it('saves state updates on the server for each editable column type when authorized', function (string $columnName, mixed $state, string $attributeName, mixed $expectedState): void {
    $published = Post::factory()->create(['is_published' => true, 'title' => 'Original', 'rating' => 1]);

    livewire(TestTableWithAuthorizedEditableColumns::class, ['authorizationMode' => 'ability'])
        ->call('updateTableColumnState', $columnName, (string) $published->getKey(), $state);

    expect($published->fresh()->getAttribute($attributeName))->toEqual($expectedState);
})->with([
    'toggle' => ['is_published_toggle', false, 'is_published', false],
    'checkbox' => ['is_published_checkbox', false, 'is_published', false],
    'text input' => ['title', 'Changed', 'title', 'Changed'],
    'select' => ['rating', '2', 'rating', 2],
]);

it('removes authorization when `authorize(null)` is used', function (string $columnName): void {
    $post = Post::factory()->create();

    livewire(TestTableWithAuthorizedEditableColumns::class, ['authorizationMode' => 'reset'])
        ->assertTableColumnEnabled($columnName, $post);
})->with(['is_published_toggle', 'is_published_checkbox', 'title', 'rating']);

it('casts non-boolean closure results', function (string $authorizationMode, bool $isEnabled): void {
    $post = Post::factory()->create();

    livewire(TestTableWithAuthorizedEditableColumns::class, ['authorizationMode' => $authorizationMode])
        ->{$isEnabled ? 'assertTableColumnEnabled' : 'assertTableColumnDisabled'}('title', $post);
})->with([
    'null' => ['closure-null', false],
    'truthy' => ['closure-truthy', true],
]);

enum EditableColumnAbility: string
{
    case UpdatePublishedPost = 'updatePublishedPost';
}

class TestTableWithAuthorizedEditableColumns extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public string $authorizationMode = 'none';

    public bool $shouldDisableColumns = false;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->columns([
                $this->configureColumn(Tables\Columns\ToggleColumn::make('is_published_toggle')
                    ->getStateUsing(fn (Post $record): bool => (bool) $record->is_published)
                    ->updateStateUsing(fn (Post $record, mixed $state) => $record->update(['is_published' => (bool) $state]))),
                $this->configureColumn(Tables\Columns\CheckboxColumn::make('is_published_checkbox')
                    ->getStateUsing(fn (Post $record): bool => (bool) $record->is_published)
                    ->updateStateUsing(fn (Post $record, mixed $state) => $record->update(['is_published' => (bool) $state]))),
                $this->configureColumn(Tables\Columns\TextInputColumn::make('title')),
                $this->configureColumn(Tables\Columns\SelectColumn::make('rating')->options([1 => 'Low', 2 => 'High'])),
                Tables\Columns\TextInputColumn::make('hidden_title')->hidden()->disabled($this->shouldDisableColumns),
                Tables\Columns\TextColumn::make('text')->disabled($this->shouldDisableColumns),
            ]);
    }

    protected function configureColumn(Column $column): Column
    {
        $column->disabled($this->shouldDisableColumns);

        return match ($this->authorizationMode) {
            'none' => $column,
            'default' => $column->authorize(),
            'ability' => $column->authorize('updatePublishedPost'),
            'enum' => $column->authorize(EditableColumnAbility::UpdatePublishedPost),
            'gate-deny' => $column->authorize('denyPost'),
            'true' => $column->authorize(true),
            'false' => $column->authorize(false),
            'closure-bool' => $column->authorize(fn (Post $record): bool => (bool) $record->is_published),
            'closure-allow' => $column->authorize(fn (): Response => Response::allow()),
            'closure-deny' => $column->authorize(fn (): Response => Response::deny()),
            'closure-deny-message' => $column->authorize(fn (): Response => Response::deny('Not allowed.')),
            'closure-null' => $column->authorize(fn () => null),
            'closure-truthy' => $column->authorize(fn (): int => 1),
            'reset' => $column->authorize(false)->authorize(null),
        };
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
