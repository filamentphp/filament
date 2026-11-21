<?php

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tests\Forms\Fixtures\Livewire;
use Filament\Tests\Models\Post;
use Filament\Tests\Models\Team;
use Filament\Tests\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

uses(TestCase::class);

test('fields can save relationships', function () {
    $numberOfRelationshipsSaved = 0;
    $isFieldVisible = true;

    $saveRelationshipsUsing = function () use (&$numberOfRelationshipsSaved) {
        $numberOfRelationshipsSaved++;
    };

    $componentContainer = ComponentContainer::make(Livewire::make())
        ->statePath('data')
        ->components([
            (new Field(Str::random()))
                ->saveRelationshipsUsing($saveRelationshipsUsing)
                ->visible(function () use (&$isFieldVisible) {
                    return $isFieldVisible;
                }),
        ])
        ->model(Post::factory()->create());

    $componentContainer
        ->saveRelationships();

    expect($numberOfRelationshipsSaved)
        ->toBe(1);

    $componentContainer
        ->saveRelationships();

    expect($numberOfRelationshipsSaved)
        ->toBe(2);

    $isFieldVisible = false;

    $componentContainer
        ->saveRelationships();

    expect($numberOfRelationshipsSaved)
        ->toBe(2);
});

describe('selected record caching', function (): void {
    it('resolves the current selection after its state changes', function (bool $shouldClearState): void {
        $authors = User::factory()->count(2)->create();
        $livewire = Livewire::make()->data(['author_id' => $authors->first()->getKey()]);
        $select = Select::make('author_id')->relationship('author', 'name');

        ComponentContainer::make($livewire)
            ->model(Post::class)
            ->statePath('data')
            ->components([$select])
            ->getComponents();

        $selectedRecord = $select->getSelectedRecord();

        expect($selectedRecord->getKey())->toBe($authors->first()->getKey())
            ->and($select->getSelectedRecord())->toBe($selectedRecord);

        $select->state($shouldClearState ? null : $authors->last()->getKey());

        expect($select->getSelectedRecord()?->getKey())->toBe($shouldClearState ? null : $authors->last()->getKey());
    })->with([false, true]);

    it('does not reuse a selected record when a clone moves to another state path', function (): void {
        $author = User::factory()->create(['name' => 'Original name']);
        $livewire = Livewire::make()->data([
            'author_id' => $author->getKey(),
            'reviewer_id' => $author->getKey(),
        ]);
        $select = Select::make('author_id')->relationship('author', 'name');
        $form = ComponentContainer::make($livewire)
            ->model(Post::class)
            ->statePath('data')
            ->components([$select]);
        $form->getComponents();

        expect($select->getSelectedRecord()->name)->toBe('Original name');

        $author->update(['name' => 'Updated name']);
        $clone = $select->getClone()->statePath('reviewer_id')->container($form);

        expect($clone->getSelectedRecord()->name)->toBe('Updated name');
    });

    it('can resolve a `key()` that reads `getSelectedRecord()`', function (bool $hasMountedAction): void {
        $author = User::factory()->create();
        $livewire = Livewire::make()->data(['author_id' => $author->getKey()]);

        if ($hasMountedAction) {
            $livewire->mountedFormComponentActions = ['inspect'];
            $livewire->mountedFormComponentActionsComponents = ['other'];
            $livewire->mountedFormComponentActionsArguments = [[]];
        }

        $keyEvaluations = 0;
        $select = Select::make('author_id')
            ->relationship('author', 'name')
            ->key(function (Select $component) use (&$keyEvaluations): string {
                if (++$keyEvaluations > 10) {
                    throw new RuntimeException('The component key depends recursively on itself.');
                }

                return 'author-' . $component->getSelectedRecord()?->getKey();
            });

        ComponentContainer::make($livewire)
            ->model(Post::class)
            ->statePath('data')
            ->components([$select])
            ->getComponents();

        expect($select->getKey())->toBe('author-' . $author->getKey());
    })->with([false, true]);
});

test('`getState()` only saves relationships for enabled forms, regardless of component opt-ins', function (bool $shouldSaveRelationshipsWhenDisabled, bool $isFormDisabled, bool $isDisabledInherited, bool $hasAfterValidate): void {
    $user = User::factory()->create();
    $originalTeam = Team::factory()->create();
    $submittedTeam = Team::factory()->create();
    $user->teams()->attach($originalTeam);

    $components = [
        TextInput::make('name'),
        Select::make('teams')
            ->relationship('teams', 'name')
            ->multiple()
            ->saveRelationshipsWhenDisabled($shouldSaveRelationshipsWhenDisabled),
    ];
    $livewire = Livewire::make();
    $form = ComponentContainer::make($livewire)->statePath('data')->model($user);

    if ($isDisabledInherited) {
        $form->components([
            $parentComponent = Group::make($components)->disabled($isFormDisabled),
        ])->fill();

        $form = $parentComponent->getChildComponentContainer();
    } else {
        $form->disabled($isFormDisabled)->components($components)->fill();
    }

    $livewire->data['name'] = 'Submitted name';
    $livewire->data['teams'] = [$submittedTeam->getKey()];
    $hasCalledAfterValidate = false;

    $state = $form->getState(afterValidate: $hasAfterValidate ? static function () use (&$hasCalledAfterValidate): void {
        $hasCalledAfterValidate = true;
    } : null);

    expect($user->teams()->pluck('teams.id')->all())
        ->toBe([($isFormDisabled ? $originalTeam : $submittedTeam)->getKey()])
        ->and($hasCalledAfterValidate)->toBe($hasAfterValidate)
        ->and($state)->toBe(($isFormDisabled && (! $isDisabledInherited)) ? [] : ['name' => 'Submitted name']);

    if ($isFormDisabled && $shouldSaveRelationshipsWhenDisabled) {
        $form->saveRelationships();

        expect($user->teams()->pluck('teams.id')->all())->toBe([$submittedTeam->getKey()]);
    }
})->with([false, true])->with([false, true])->with([false, true])->with([false, true]);

test('multiple `Select` fields reject scalar state before saving relationships', function (bool $isJson) {
    $user = User::factory()->create();
    $allowedTeam = Team::factory()->create();
    $excludedTeam = Team::factory()->create();
    $user->teams()->attach($allowedTeam);

    $livewire = Livewire::make();
    $form = ComponentContainer::make($livewire)
        ->statePath('data')
        ->model($user)
        ->components([
            Select::make('teams')
                ->relationship('teams', 'name')
                ->multiple()
                ->nestedRecursiveRules(['in:' . $allowedTeam->getKey()]),
        ])
        ->fill();

    $livewire->data['teams'] = $isJson ? json_encode([$excludedTeam->getKey()]) : (string) $excludedTeam->getKey();
    $failedRules = [];

    try {
        $form->getState();
    } catch (ValidationException $exception) {
        $failedRules = $exception->validator->failed();
    }

    expect($failedRules['data.teams'] ?? [])->toHaveKey('Array');
    $this->assertDatabaseCount('team_user', 1);
    $this->assertDatabaseHas('team_user', [
        'user_id' => $user->getKey(),
        'team_id' => $allowedTeam->getKey(),
    ]);
})->with([false, true]);

test('hidden fields can save relationships when requested', function () {
    $numberOfRelationshipsSaved = 0;
    $shouldSaveRelationships = true;

    $saveRelationshipsUsing = function () use (&$numberOfRelationshipsSaved) {
        $numberOfRelationshipsSaved++;
    };

    $componentContainer = ComponentContainer::make(Livewire::make())
        ->statePath('data')
        ->components([
            (new Field(Str::random()))
                ->saveRelationshipsUsing($saveRelationshipsUsing)
                ->hidden()
                ->saveRelationshipsWhenHidden(function () use (&$shouldSaveRelationships) {
                    return $shouldSaveRelationships;
                }),
        ])
        ->model(Post::factory()->create());

    $componentContainer
        ->saveRelationships();

    expect($numberOfRelationshipsSaved)
        ->toBe(1);

    $componentContainer
        ->saveRelationships();

    expect($numberOfRelationshipsSaved)
        ->toBe(2);

    $shouldSaveRelationships = false;

    $componentContainer
        ->saveRelationships();

    expect($numberOfRelationshipsSaved)
        ->toBe(2);
});
