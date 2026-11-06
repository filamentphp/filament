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
    Gate::define('updatePost', fn (User $user, Post $post): bool => true);
    Gate::define('denyPost', fn (User $user, Post $post): Response => Response::deny('Nope.'));
});

it('allows updating editable columns without `authorize()`', function (string $column): void {
    $post = Post::factory()->create(['is_published' => false]);

    livewire(TestTableWithAuthorizedEditableColumns::class, ['authorization' => 'none'])
        ->assertTableColumnStateCanBeUpdated($column, $post);
})->with(['is_published_toggle', 'is_published_checkbox', 'title', 'rating']);

it('does not apply a default policy check outside of resources', function (): void {
    $post = Post::factory()->create();

    livewire(TestTableWithAuthorizedEditableColumns::class, ['authorization' => 'none'])
        ->assertTableColumnStateCanBeUpdated('is_published_toggle', $post)
        ->call('updateTableColumnState', 'is_published_toggle', (string) $post->getKey(), false);

    expect($post->fresh()->is_published)->toBeFalse();
});

it('denies the default `update` ability when no policy or gate is defined', function (string $column): void {
    $post = Post::factory()->create();

    livewire(TestTableWithAuthorizedEditableColumns::class, ['authorization' => 'default'])
        ->assertTableColumnStateCanNotBeUpdated($column, $post);
})->with(['is_published_toggle', 'is_published_checkbox', 'title', 'rating']);

it('checks a custom gate ability against the record', function (string $column): void {
    $published = Post::factory()->create(['is_published' => true]);
    $draft = Post::factory()->create(['is_published' => false]);

    livewire(TestTableWithAuthorizedEditableColumns::class, ['authorization' => 'ability'])
        ->assertTableColumnStateCanBeUpdated($column, $published)
        ->assertTableColumnStateCanNotBeUpdated($column, $draft);
})->with(['is_published_toggle', 'is_published_checkbox', 'title', 'rating']);

it('accepts a backed enum as the ability', function (): void {
    $published = Post::factory()->create(['is_published' => true]);
    $draft = Post::factory()->create(['is_published' => false]);

    livewire(TestTableWithAuthorizedEditableColumns::class, ['authorization' => 'enum'])
        ->assertTableColumnStateCanBeUpdated('title', $published)
        ->assertTableColumnStateCanNotBeUpdated('title', $draft);
});

it('accepts a static boolean', function (string $authorization, bool $canUpdate): void {
    $post = Post::factory()->create();

    livewire(TestTableWithAuthorizedEditableColumns::class, ['authorization' => $authorization])
        ->{$canUpdate ? 'assertTableColumnStateCanBeUpdated' : 'assertTableColumnStateCanNotBeUpdated'}('title', $post);
})->with([
    'true' => ['true', true],
    'false' => ['false', false],
]);

it('accepts a closure returning a boolean with the record injected', function (): void {
    $published = Post::factory()->create(['is_published' => true]);
    $draft = Post::factory()->create(['is_published' => false]);

    livewire(TestTableWithAuthorizedEditableColumns::class, ['authorization' => 'closure-bool'])
        ->assertTableColumnStateCanBeUpdated('title', $published)
        ->assertTableColumnStateCanNotBeUpdated('title', $draft);
});

it('accepts a closure returning a response', function (string $authorization, bool $canUpdate): void {
    $post = Post::factory()->create();

    livewire(TestTableWithAuthorizedEditableColumns::class, ['authorization' => $authorization])
        ->{$canUpdate ? 'assertTableColumnStateCanBeUpdated' : 'assertTableColumnStateCanNotBeUpdated'}('title', $post);
})->with([
    'allowed' => ['closure-allow', true],
    'denied' => ['closure-deny', false],
]);

it('exposes the authorization response from the column', function (): void {
    $post = Post::factory()->create();

    $column = livewire(TestTableWithAuthorizedEditableColumns::class, ['authorization' => 'gate-deny'])
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
    $column = livewire(TestTableWithAuthorizedEditableColumns::class, ['authorization' => 'false'])
        ->instance()
        ->getTable()
        ->getColumn('title');

    expect($column->isDisabled())->toBeFalse();
});

it('is still disabled by `disabled()` when authorized', function (): void {
    $post = Post::factory()->create();

    livewire(TestTableWithAuthorizedEditableColumns::class, ['authorization' => 'true', 'areColumnsDisabled' => true])
        ->assertTableColumnStateCanNotBeUpdated('title', $post);
});

it('rejects state updates on the server when unauthorized', function (string $column, mixed $state, string $attribute): void {
    $draft = Post::factory()->create(['is_published' => false, 'title' => 'Original', 'rating' => 1]);
    $original = $draft->getAttribute($attribute);

    livewire(TestTableWithAuthorizedEditableColumns::class, ['authorization' => 'ability'])
        ->call('updateTableColumnState', $column, (string) $draft->getKey(), $state)
        ->assertReturned(['error' => __('filament-tables::table.columns.editable.unavailable')]);

    expect($draft->fresh()->getAttribute($attribute))->toBe($original);
})->with([
    'toggle' => ['is_published_toggle', true, 'is_published'],
    'checkbox' => ['is_published_checkbox', true, 'is_published'],
    'text input' => ['title', 'Changed', 'title'],
    'select' => ['rating', '2', 'rating'],
]);

it('saves state updates on the server when authorized', function (): void {
    $published = Post::factory()->create(['is_published' => true, 'title' => 'Original']);

    livewire(TestTableWithAuthorizedEditableColumns::class, ['authorization' => 'ability'])
        ->call('updateTableColumnState', 'title', (string) $published->getKey(), 'Changed');

    expect($published->fresh()->title)->toBe('Changed');
});

it('uses the default authorization response provided by the Livewire component', function (): void {
    $post = Post::factory()->create();

    livewire(TestTableWithDefaultColumnAuthorization::class)
        ->assertTableColumnStateCanNotBeUpdated('title', $post)
        ->call('updateTableColumnState', 'title', (string) $post->getKey(), 'Changed')
        ->assertReturned(['error' => __('filament-tables::table.columns.editable.unavailable')]);

    expect($post->fresh()->title)->not->toBe('Changed');
});

it('prefers `authorize()` over the default authorization response from the Livewire component', function (): void {
    $post = Post::factory()->create();

    livewire(TestTableWithDefaultColumnAuthorization::class)
        ->assertTableColumnStateCanBeUpdated('title_overridden', $post);
});

it('can not update hidden or non-editable columns', function (string $column): void {
    $post = Post::factory()->create();

    livewire(TestTableWithAuthorizedEditableColumns::class, ['authorization' => 'none'])
        ->assertTableColumnStateCanNotBeUpdated($column, $post);
})->with(['hidden_title', 'text']);

it('fails `assertTableColumnStateCanBeUpdated()` when unauthorized', function (): void {
    $post = Post::factory()->create();

    livewire(TestTableWithAuthorizedEditableColumns::class, ['authorization' => 'false'])
        ->assertTableColumnStateCanBeUpdated('title', $post);
})->throws(AssertionFailedError::class);

it('fails `assertTableColumnStateCanNotBeUpdated()` when authorized', function (): void {
    $post = Post::factory()->create();

    livewire(TestTableWithAuthorizedEditableColumns::class, ['authorization' => 'true'])
        ->assertTableColumnStateCanNotBeUpdated('title', $post);
})->throws(AssertionFailedError::class);

it('accepts a record key in the editable column assertions', function (): void {
    $published = Post::factory()->create(['is_published' => true]);
    $draft = Post::factory()->create(['is_published' => false]);

    livewire(TestTableWithAuthorizedEditableColumns::class, ['authorization' => 'ability'])
        ->assertTableColumnStateCanBeUpdated('title', $published->getKey())
        ->assertTableColumnStateCanNotBeUpdated('title', (string) $draft->getKey());
});

it('does not provide a default authorization response for tables outside of resources', function (): void {
    $post = Post::factory()->create();

    $livewire = livewire(TestTableWithAuthorizedEditableColumns::class, ['authorization' => 'none'])->instance();

    $column = $livewire->getTable()->getColumn('title')->record($post);

    expect($livewire->getDefaultTableColumnAuthorizationResponse($column))->toBeNull()
        ->and($column->getAuthorizationResponse()->allowed())->toBeTrue();
});

it('does not expose the default column authorization response method to the frontend', function (): void {
    livewire(TestTableWithAuthorizedEditableColumns::class, ['authorization' => 'none'])
        ->call('getDefaultTableColumnAuthorizationResponse')
        ->assertForbidden();
});

it('denies the default `update` ability when the gate denies it', function (): void {
    Gate::define('update', fn (User $user, Post $post): bool => (bool) $post->is_published);

    $published = Post::factory()->create(['is_published' => true]);
    $draft = Post::factory()->create(['is_published' => false]);

    livewire(TestTableWithAuthorizedEditableColumns::class, ['authorization' => 'default'])
        ->assertTableColumnStateCanBeUpdated('title', $published)
        ->assertTableColumnStateCanNotBeUpdated('title', $draft);
});

it('keeps the denial message from a closure response', function (): void {
    $post = Post::factory()->create();

    $column = livewire(TestTableWithAuthorizedEditableColumns::class, ['authorization' => 'closure-deny-message'])
        ->instance()
        ->getTable()
        ->getColumn('title')
        ->record($post);

    expect($column->getAuthorizationResponse()->message())->toBe('Not allowed.')
        ->and($column->isDisabled())->toBeTrue();
});

it('saves state updates on the server for each editable column type when authorized', function (string $column, mixed $state, string $attribute, mixed $expected): void {
    $published = Post::factory()->create(['is_published' => true, 'title' => 'Original', 'rating' => 1]);

    livewire(TestTableWithAuthorizedEditableColumns::class, ['authorization' => 'true'])
        ->call('updateTableColumnState', $column, (string) $published->getKey(), $state);

    expect($published->fresh()->getAttribute($attribute))->toEqual($expected);
})->with([
    'toggle' => ['is_published_toggle', false, 'is_published', false],
    'checkbox' => ['is_published_checkbox', false, 'is_published', false],
    'text input' => ['title', 'Changed', 'title', 'Changed'],
    'select' => ['rating', '2', 'rating', 2],
]);

it('resets to the default authorization when `authorize(null)` is used', function (string $column): void {
    $post = Post::factory()->create();

    livewire(TestTableWithAuthorizedEditableColumns::class, ['authorization' => 'reset'])
        ->assertTableColumnStateCanBeUpdated($column, $post);
})->with(['is_published_toggle', 'is_published_checkbox', 'title', 'rating']);

it('casts non-boolean closure results', function (string $authorization, bool $canUpdate): void {
    $post = Post::factory()->create();

    livewire(TestTableWithAuthorizedEditableColumns::class, ['authorization' => $authorization])
        ->{$canUpdate ? 'assertTableColumnStateCanBeUpdated' : 'assertTableColumnStateCanNotBeUpdated'}('title', $post);
})->with([
    'null' => ['closure-null', false],
    'truthy' => ['closure-truthy', true],
]);

it('applies the default authorization response from the Livewire component to each editable column type', function (string $column): void {
    $post = Post::factory()->create();

    livewire(TestTableWithDefaultColumnAuthorization::class)
        ->assertTableColumnStateCanNotBeUpdated($column, $post);
})->with(['title', 'is_published_toggle', 'is_published_checkbox', 'rating']);

it('is disabled by authorization for each editable column type', function (string $column): void {
    $post = Post::factory()->create();

    $instance = livewire(TestTableWithAuthorizedEditableColumns::class, ['authorization' => 'false'])->instance();

    $column = $instance->getTable()->getColumn($column)->record($post);

    expect($column->isAuthorized())->toBeFalse()
        ->and($column->isDisabled())->toBeTrue();
})->with(['is_published_toggle', 'is_published_checkbox', 'title', 'rating']);

enum EditableColumnAbility: string
{
    case UpdatePublishedPost = 'updatePublishedPost';
}

class TestTableWithAuthorizedEditableColumns extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public string $authorization = 'none';

    public bool $areColumnsDisabled = false;

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
                Tables\Columns\TextInputColumn::make('hidden_title')->hidden(),
                Tables\Columns\TextColumn::make('text'),
            ]);
    }

    protected function configureColumn(Column $column): Column
    {
        $column->disabled($this->areColumnsDisabled);

        return match ($this->authorization) {
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

class TestTableWithDefaultColumnAuthorization extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->columns([
                Tables\Columns\TextInputColumn::make('title'),
                Tables\Columns\ToggleColumn::make('is_published_toggle')
                    ->getStateUsing(fn (Post $record): bool => (bool) $record->is_published),
                Tables\Columns\CheckboxColumn::make('is_published_checkbox')
                    ->getStateUsing(fn (Post $record): bool => (bool) $record->is_published),
                Tables\Columns\SelectColumn::make('rating')->options([1 => 'Low', 2 => 'High']),
                Tables\Columns\TextInputColumn::make('title_overridden')
                    ->getStateUsing(fn (Post $record): string => $record->title)
                    ->authorize('updatePost'),
            ]);
    }

    public function getDefaultTableColumnAuthorizationResponse(Column $column): ?Response
    {
        return Response::deny();
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
