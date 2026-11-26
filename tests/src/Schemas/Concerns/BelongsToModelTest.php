<?php

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('can store a record', function (): void {
    $schema = Schema::make(Livewire::make())->model($record = new Post);

    expect($schema->getRecord())->toBe($record);
});

it('can get the model class from a class name or record', function (): void {
    $schema = Schema::make(Livewire::make())->model(Post::class);

    expect($schema->getModel())->toBe(Post::class);

    $schema->model(new Post);

    expect($schema->getModel())->toBe(Post::class);
});

it('can get an instance of the model', function (): void {
    $schema = Schema::make(Livewire::make())->model($record = new Post);

    expect($schema->getModelInstance())->toBe($record);

    $schema->model(Post::class);

    expect($schema->getModelInstance())->toBeInstanceOf(Post::class);
});

it('does not newly admit relationship state after binding a created record', function (string $eligibility, bool $optIn, ?string $model): void {
    $team = Team::factory()->create();
    $field = Select::make('teams')->multiple()->relationship('teams', 'name');

    match ($eligibility) {
        'hidden' => $field->hidden(fn (?User $record): bool => ! $record?->exists)->saveRelationshipsWhenHidden($optIn),
        'disabled' => $field->disabled(fn (?User $record): bool => ! $record?->exists)->saveRelationshipsWhenDisabled($optIn),
        'saved' => $field->saved(fn (?User $record): bool => (bool) $record?->exists),
        'eligible' => null,
    };

    $livewire = RelationshipSavingTestLivewire::make();

    $schema = $livewire->getSchema('form')
        ->model($model)
        ->statePath('data')
        ->components([$field]);

    $livewire->data(['teams' => [$team->getKey()]]);

    $schema->getState();

    $user = User::factory()->create();
    $schema->model($user)->saveRelationships();

    expect($user->teams()->pluck('teams.id')->all())
        ->toBe(($optIn || ($eligibility === 'eligible')) ? [$team->getKey()] : []);
})->with([
    'hidden' => ['hidden', false, User::class],
    'disabled' => ['disabled', false, User::class],
    'not saved' => ['saved', false, User::class],
    'eligible' => ['eligible', false, User::class],
    'hidden opt-in' => ['hidden', true, User::class],
    'disabled opt-in' => ['disabled', true, User::class],
    'no model' => ['hidden', false, null],
]);

it('does not newly admit relationships inside an excluded layout after creation', function (string $eligibility): void {
    $team = Team::factory()->create();
    $group = Group::make([
        Select::make('teams')->multiple()->relationship('teams', 'name'),
    ]);

    match ($eligibility) {
        'hidden' => $group->hidden(fn (?User $record): bool => ! $record?->exists),
        'disabled' => $group->disabled(fn (?User $record): bool => ! $record?->exists),
    };

    $livewire = RelationshipSavingTestLivewire::make();
    $schema = $livewire->getSchema('form')->model(User::class)->statePath('data')->components([$group]);
    $livewire->data(['teams' => [$team->getKey()]]);

    $schema->getState();

    $user = User::factory()->create();
    $schema->model($user)->saveRelationships();

    expect($user->teams()->exists())->toBeFalse();
})->with(['hidden', 'disabled']);

it('reevaluates relationship eligibility for each submission', function (): void {
    $team = Team::factory()->create();
    $isHidden = true;
    $field = Select::make('teams')->multiple()->relationship('teams', 'name')
        ->hidden(function () use (&$isHidden): bool {
            return $isHidden;
        });

    $livewire = RelationshipSavingTestLivewire::make();
    $schema = $livewire->getSchema('form')->model(User::class)->statePath('data')->components([$field]);
    $livewire->data(['teams' => [$team->getKey()]]);

    $schema->getState();
    $schema->model($firstUser = User::factory()->create())->saveRelationships();

    expect($firstUser->teams()->exists())->toBeFalse();

    $isHidden = false;
    $schema->model(User::class);
    $livewire->data(['teams' => [$team->getKey()]]);

    $schema->getState();
    $schema->model($secondUser = User::factory()->create())->saveRelationships();

    expect($secondUser->teams()->pluck('teams.id')->all())->toBe([$team->getKey()]);
});

it('does not evaluate record-dependent callbacks inside an excluded layout', function (): void {
    $livewire = RelationshipSavingTestLivewire::make();
    $schema = $livewire->getSchema('form')->model(User::class)->statePath('data')->components([
        Section::make('Details')
            ->statePath('details')
            ->hidden(fn (?User $record): bool => ! $record?->exists)
            ->schema([
                TextInput::make('name')->hidden(fn (User $record): bool => $record->name === 'Admin'),
            ]),
    ]);
    $livewire->data(['details' => ['name' => 'Client name']]);

    expect($schema->getState())->toBe([]);
});

it('preserves relationship exclusions when creating a singular relationship rebuilds its schema', function (bool $isHidden, bool $usesClosure): void {
    $team = Team::factory()->create();
    $livewire = RelationshipSavingTestLivewire::make();
    $components = fn (): array => [
        TextInput::make('name')->required(),
        TextInput::make('email')->required()->email(),
        TextInput::make('password')->required(),
        Select::make('teams')->multiple()->relationship('teams', 'name')
            ->hidden(fn (?User $record): bool => $isHidden && (! $record?->exists)),
    ];

    $schema = $livewire->getSchema('form')->model(Post::class)->statePath('data')->components([
        Group::make()->relationship('author')->schema($usesClosure ? $components : $components()),
    ]);
    $livewire->data(['author' => [
        'name' => 'New author',
        'email' => 'author@example.com',
        'password' => 'password',
        'teams' => [$team->getKey()],
    ]]);

    $schema->getState();
    $schema->model($post = Post::factory()->create(['author_id' => null]))->saveRelationships();

    expect($post->fresh()->author->teams()->pluck('teams.id')->all())->toBe($isHidden ? [] : [$team->getKey()]);
})->with(['excluded' => true, 'eligible' => false])->with(['closure' => true, 'array' => false]);

it('does not merge excluded state from another layout saving the same singular relationship', function (): void {
    $livewire = RelationshipSavingTestLivewire::make();
    $schema = $livewire->getSchema('form')->model(Post::class)->statePath('data')->components([
        Group::make()->relationship('author')->schema([
            TextInput::make('name')->required(),
            TextInput::make('email')->required()->email(),
            TextInput::make('password')->required(),
        ]),
        Group::make()->relationship('author')
            ->hidden(fn (?Post $record): bool => ! $record?->exists)
            ->schema([
                TextInput::make('score')->numeric(),
            ]),
    ]);
    $livewire->data(['author' => [
        'name' => 'New author',
        'email' => 'author@example.com',
        'password' => 'password',
        'score' => 543,
    ]]);

    $schema->getState();
    $schema->model($post = Post::factory()->create(['author_id' => null]))->saveRelationships();

    expect($post->fresh()->author->score)->toBeNull();
});

class RelationshipSavingTestLivewire extends Livewire
{
    public function form(Schema $schema): Schema
    {
        return $schema;
    }
}
