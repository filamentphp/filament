<?php

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Enums\StringBackedEnum;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\Company;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
});

describe('validation', function (): void {
    it('can automatically validate valid options', function (): void {
        livewire(TestComponentWithSelect::class)
            ->fillForm(['number' => 'one'])
            ->call('save')
            ->assertHasNoFormErrors();

        livewire(TestComponentWithSelect::class)
            ->fillForm(['number' => 'four'])
            ->call('save')
            ->assertHasFormErrors(['number' => ['in']]);
    });

    it('can automatically validate valid multiple options', function (): void {
        livewire(TestComponentWithMultipleSelect::class)
            ->fillForm(['number' => ['one', 'two']])
            ->call('save')
            ->assertHasNoFormErrors();

        livewire(TestComponentWithMultipleSelect::class)
            ->fillForm(['number' => ['one', 'four']])
            ->call('save')
            ->assertHasFormErrors(['number.1' => ['in']]);
    });

    it('can automatically validate valid options with custom search results', function (): void {
        livewire(TestComponentWithSelectCustomSearchResults::class)
            ->fillForm(['number' => 'one'])
            ->call('save')
            ->assertHasNoFormErrors();

        livewire(TestComponentWithSelectCustomSearchResults::class)
            ->fillForm(['number' => 'four'])
            ->call('save')
            ->assertHasFormErrors(['number' => ['in']]);
    });

    it('can automatically validate valid multiple options with custom search results', function (): void {
        livewire(TestComponentWithMultipleSelectCustomSearchResults::class)
            ->fillForm(['number' => ['one', 'two']])
            ->call('save')
            ->assertHasNoFormErrors();

        livewire(TestComponentWithMultipleSelectCustomSearchResults::class)
            ->fillForm(['number' => ['one', 'four']])
            ->call('save')
            ->assertHasFormErrors(['number.1' => ['in']]);
    });

    it('can automatically validate valid options with `BelongsTo` relationship', function (): void {
        $users = User::factory()->count(3)->create();

        livewire(TestComponentWithBelongsToRelationshipValidation::class)
            ->fillForm(['author_id' => (string) $users->first()->id])
            ->call('save')
            ->assertHasNoFormErrors();

        livewire(TestComponentWithBelongsToRelationshipValidation::class)
            ->fillForm(['author_id' => '99999'])
            ->call('save')
            ->assertHasFormErrors(['author_id' => ['in']]);
    });

    it('can automatically validate valid options with searchable `BelongsTo` relationship', function (): void {
        $users = User::factory()->count(3)->create();

        livewire(TestComponentWithSearchableBelongsToRelationshipValidation::class)
            ->fillForm(['author_id' => (string) $users->first()->id])
            ->call('save')
            ->assertHasNoFormErrors();

        livewire(TestComponentWithSearchableBelongsToRelationshipValidation::class)
            ->fillForm(['author_id' => '99999'])
            ->call('save')
            ->assertHasFormErrors(['author_id' => ['in']]);
    });

    it('can automatically validate valid multiple options with `BelongsToMany` relationship', function (): void {
        $user = User::factory()->create();
        $teams = Team::factory()->count(3)->create();

        livewire(TestComponentWithBelongsToManyRelationshipValidation::class, ['record' => $user])
            ->fillForm(['teams' => $teams->take(2)->pluck('id')->map(fn ($id) => (string) $id)->all()])
            ->call('save')
            ->assertHasNoFormErrors();

        livewire(TestComponentWithBelongsToManyRelationshipValidation::class, ['record' => $user])
            ->fillForm(['teams' => [(string) $teams->first()->id, '99999']])
            ->call('save')
            ->assertHasFormErrors(['teams.1' => ['in']]);
    });

    it('can automatically validate valid multiple options with searchable `BelongsToMany` relationship', function (): void {
        $user = User::factory()->create();
        $teams = Team::factory()->count(3)->create();

        livewire(TestComponentWithSearchableBelongsToManyRelationshipValidation::class, ['record' => $user])
            ->fillForm(['teams' => $teams->take(2)->pluck('id')->map(fn ($id) => (string) $id)->all()])
            ->call('save')
            ->assertHasNoFormErrors();

        livewire(TestComponentWithSearchableBelongsToManyRelationshipValidation::class, ['record' => $user])
            ->fillForm(['teams' => [(string) $teams->first()->id, '99999']])
            ->call('save')
            ->assertHasFormErrors(['teams.1' => ['in']]);
    });

    it('rejects disabled static options during validation', function (): void {
        livewire(TestComponentWithDisabledOptions::class)
            ->fillForm(['status' => 'active'])
            ->call('save')
            ->assertHasNoFormErrors();

        livewire(TestComponentWithDisabledOptions::class)
            ->fillForm(['status' => 'archived'])
            ->call('save')
            ->assertHasFormErrors(['status' => ['in']]);
    });

    it('rejects disabled static options during validation for multiple select', function (): void {
        livewire(TestComponentWithMultipleDisabledOptions::class)
            ->fillForm(['statuses' => ['active', 'pending']])
            ->call('save')
            ->assertHasNoFormErrors();

        livewire(TestComponentWithMultipleDisabledOptions::class)
            ->fillForm(['statuses' => ['active', 'archived']])
            ->call('save')
            ->assertHasFormErrors(['statuses.1' => ['in']]);
    });

    it('passes validation when state is blank for single select', function (): void {
        livewire(TestComponentWithSelect::class)
            ->fillForm(['number' => null])
            ->call('save')
            ->assertHasNoFormErrors();

        livewire(TestComponentWithSelect::class)
            ->fillForm(['number' => ''])
            ->call('save')
            ->assertHasNoFormErrors();
    });

    it('passes validation when state is blank for multiple select', function (): void {
        livewire(TestComponentWithMultipleSelect::class)
            ->fillForm(['number' => null])
            ->call('save')
            ->assertHasNoFormErrors();

        livewire(TestComponentWithMultipleSelect::class)
            ->fillForm(['number' => []])
            ->call('save')
            ->assertHasNoFormErrors();
    });

    it('passes validation when state is blank for relationship select', function (): void {
        User::factory()->count(3)->create();

        livewire(TestComponentWithBelongsToRelationshipValidation::class)
            ->fillForm(['author_id' => null])
            ->call('save')
            ->assertHasNoFormErrors();
    });
});

describe('`BelongsToMany` relationship', function (): void {
    it('can use `BelongsToMany` relationship as multiple select', function (): void {
        $user = User::factory()->create();
        $teams = Team::factory()->count(3)->create();
        $user->teams()->attach($teams);

        expect($user->teams)->toHaveCount(3);

        livewire(TestComponentWithBelongsToManyMultipleSelect::class, ['record' => $user])
            ->assertSchemaStateSet([
                'teams' => $teams->pluck('id')->map(fn ($id) => (string) $id)->all(),
            ]);
    });

    it('can save `BelongsToMany` relationship as multiple select', function (): void {
        $user = User::factory()->create();
        $teams = Team::factory()->count(3)->create();
        $user->teams()->attach($teams->first());

        expect($user->teams)->toHaveCount(1);

        $newTeamIds = $teams->take(2)->pluck('id')->map(fn ($id) => (string) $id)->all();

        livewire(TestComponentWithBelongsToManyMultipleSelect::class, ['record' => $user])
            ->fillForm(['teams' => $newTeamIds])
            ->call('save');

        $user->refresh();
        expect($user->teams)->toHaveCount(2);
        expect($user->teams->pluck('id')->sort()->values()->all())->toBe($teams->take(2)->pluck('id')->sort()->values()->all());
    });

    it('can use `BelongsToMany` relationship as single select', function (): void {
        $user = User::factory()->create();
        $teams = Team::factory()->count(3)->create();
        $user->teams()->attach($teams);

        expect($user->teams)->toHaveCount(3);

        livewire(TestComponentWithBelongsToManySelect::class, ['record' => $user])
            ->assertSchemaStateSet([
                'teams' => (string) $teams->first()->id,
            ]);
    });

    it('can save `BelongsToMany` relationship as single select', function (): void {
        $user = User::factory()->create();
        $teams = Team::factory()->count(3)->create();
        $user->teams()->attach($teams->take(2));

        expect($user->teams)->toHaveCount(2);

        $newTeamId = (string) $teams->last()->id;

        livewire(TestComponentWithBelongsToManySelect::class, ['record' => $user])
            ->fillForm(['teams' => $newTeamId])
            ->call('save');

        $user->refresh();
        expect($user->teams)->toHaveCount(1);
        expect($user->teams->first()->id)->toBe($teams->last()->id);
    });
});

describe('options and search branches', function (): void {
    it('caches relationship options on the component to avoid duplicate queries for repeated `getOptions()` calls', function (): void {
        // Why this matters: a Select inside a Repeater can have `getOptions()` invoked many times during a
        // render cycle (once per item lifecycle hook, validation, view rendering). Without caching, that
        // becomes a query per repeater item. The cache keeps the load to a single query for the whole cycle.
        Team::factory()->count(3)->create();

        $select = Select::make('teams')
            ->relationship('teams', 'name')
            ->multiple()
            ->preload()
            ->container(
                Schema::make(Livewire::make())
                    ->model(User::class)
                    ->statePath('data'),
            );

        DB::enableQueryLog();
        DB::flushQueryLog();

        $first = $select->getOptions();
        $queriesAfterFirstCall = count(DB::getQueryLog());

        DB::flushQueryLog();

        $second = $select->getOptions();
        $queriesAfterSecondCall = count(DB::getQueryLog());

        DB::disableQueryLog();

        expect($queriesAfterFirstCall)->toBeGreaterThan(0);
        expect($queriesAfterSecondCall)->toBe(0);
        expect($second)->toBe($first);
    });

    it('does not cache `getOptions()` when `modifyQueryUsing()` is set, since the closure can vary per repeater item', function (): void {
        // Why this matters: `modifyQueryUsing` callbacks frequently inject `Get` / `$record` etc., which may
        // produce different option lists for different sibling rows in a Repeater. Caching here would leak
        // the first row's options to every other row, so the cache must be skipped.
        Team::factory()->count(3)->create();

        $select = Select::make('teams')
            ->relationship('teams', 'name', modifyQueryUsing: fn ($query) => $query)
            ->multiple()
            ->preload()
            ->container(
                Schema::make(Livewire::make())
                    ->model(User::class)
                    ->statePath('data'),
            );

        DB::enableQueryLog();
        DB::flushQueryLog();

        $select->getOptions();
        $select->getOptions();

        $totalQueries = count(DB::getQueryLog());
        DB::disableQueryLog();

        expect($totalQueries)->toBeGreaterThan(1);
    });

    it('respects an explicit `limit` set on the relationship query inside `getSearchResults()`', function (): void {
        Team::factory()->count(5)->create();

        livewire(SelectWithBelongsToManyRelationshipAndExplicitLimit::class)
            ->assertFormComponentExists('teams', function (Select $component): bool {
                $results = $component->getSearchResults('');

                expect($results)->toHaveCount(2);

                return true;
            });
    });

    it('skips re-applying `orderBy` when relationship query already has orders', function (): void {
        Team::factory()->create(['name' => 'Bravo']);
        Team::factory()->create(['name' => 'Alpha']);

        livewire(SelectWithBelongsToManyRelationshipAndExistingOrder::class)
            ->assertFormComponentExists('teams', function (Select $component): bool {
                $results = $component->getSearchResults('');

                // The custom DESC order must win because the method skips orderBy when orders exist
                $names = array_values($results);
                expect($names)->toBe(['Bravo', 'Alpha']);

                return true;
            });
    });

    it('uses `getOptionLabelFromRecord()` callback inside `getSearchResults()` when configured', function (): void {
        Team::factory()->create(['name' => 'Alpha']);
        Team::factory()->create(['name' => 'Beta']);

        livewire(SelectWithBelongsToManyRelationshipAndOptionLabelFromRecord::class)
            ->assertFormComponentExists('teams', function (Select $component): bool {
                $results = $component->getSearchResults('');

                expect(array_values($results))->toContain('Custom: Alpha');
                expect(array_values($results))->toContain('Custom: Beta');

                return true;
            });
    });

    it('handles JSON-path `titleAttribute` (`->`) inside `getSearchResults()`', function (): void {
        $author = User::factory()->create(['name' => 'Author']);
        Post::factory()->create(['author_id' => $author->id, 'title' => 'Post A', 'json' => ['title' => 'JSON Title A']]);
        Post::factory()->create(['author_id' => $author->id, 'title' => 'Post B', 'json' => ['title' => 'JSON Title B']]);

        livewire(SelectWithJsonPathTitleAttribute::class, ['record' => $author])
            ->assertFormComponentExists('posts', function (Select $component): bool {
                $results = $component->getSearchResults('');

                expect(array_values($results))->toContain('JSON Title A');
                expect(array_values($results))->toContain('JSON Title B');

                return true;
            });
    });
});

describe('save state branches', function (): void {
    it('returns early from `fillStateFromRelationship()` when state is already filled', function (): void {
        $teams = Team::factory()->count(2)->create();
        $user = User::factory()->create(['team_id' => $teams->first()->id]);

        livewire(SelectWithBelongsToRelationship::class, ['record' => $user])
            ->assertFormComponentExists('team_id', function (Select $component) use ($teams): bool {
                $component->state((string) $teams->last()->id);
                $component->fillStateFromRelationship();

                expect((string) $component->getState())->toBe((string) $teams->last()->id);

                return true;
            });
    });

    it('nulls and re-associates foreign keys when saving a `HasMany` relationship', function (): void {
        $user = User::factory()->create();
        $existingPost = Post::factory()->create(['author_id' => $user->id]);
        $orphanPosts = Post::factory()->count(2)->create(['author_id' => null]);

        livewire(SelectWithHasManyRelationship::class, ['record' => $user])
            ->assertFormComponentExists('posts', function (Select $component) use ($orphanPosts): bool {
                $component->state($orphanPosts->pluck('id')->map(fn ($id) => (string) $id)->all());
                $component->saveStateToRelationship();

                return true;
            });

        expect($existingPost->fresh()->author_id)->toBeNull();
        foreach ($orphanPosts as $post) {
            expect($post->fresh()->author_id)->toBe($user->id);
        }
    });

    it('nulls and re-associates a foreign key when saving a `HasOne` relationship', function (): void {
        $user = User::factory()->create();
        $existingPost = Post::factory()->create(['author_id' => $user->id, 'is_published' => true]);
        $orphanPost = Post::factory()->create(['author_id' => null, 'is_published' => true]);

        livewire(SelectWithHasOneRelationship::class, ['record' => $user])
            ->assertFormComponentExists('publishedPost', function (Select $component) use ($orphanPost): bool {
                $component->state((string) $orphanPost->id);
                $component->saveStateToRelationship();

                return true;
            });

        expect($existingPost->fresh()->author_id)->toBeNull();
        expect($orphanPost->fresh()->author_id)->toBe($user->id);
    });

    it('applies `modifyQueryUsing` when saving a `HasMany` relationship', function (): void {
        $user = User::factory()->create();
        $modifyCallCount = 0;
        SelectWithHasManyRelationshipAndModifyQuery::$onModify = static function () use (&$modifyCallCount): void {
            $modifyCallCount++;
        };

        livewire(SelectWithHasManyRelationshipAndModifyQuery::class, ['record' => $user])
            ->assertFormComponentExists('posts', function (Select $component): bool {
                $component->state([]);
                $component->saveStateToRelationship();

                return true;
            });

        expect($modifyCallCount)->toBeGreaterThan(0);
    });

    it('does not overwrite a `BelongsTo` foreign key on a recently created record', function (): void {
        $teamA = Team::factory()->create();
        $teamB = Team::factory()->create();
        $user = User::factory()->create(['team_id' => $teamA->id]);

        livewire(SelectWithBelongsToRelationship::class, ['record' => $user])
            ->assertFormComponentExists('team_id', function (Select $component) use ($teamA, $teamB): bool {
                $component->getRecord()->wasRecentlyCreated = true;
                $component->getRecord()->team_id = $teamA->id;

                $component->state((string) $teamB->id);
                $component->saveStateToRelationship();

                expect($component->getRecord()->team_id)->toBe($teamA->id);

                return true;
            });
    });

    it('treats saving a `HasManyThrough` relationship as a no-op', function (): void {
        $team = Team::factory()->create();
        $user = User::factory()->create(['team_id' => $team->id]);
        $posts = Post::factory()->count(2)->create(['author_id' => $user->id]);

        livewire(SelectWithHasManyThroughRelationship::class, ['record' => $team])
            ->assertFormComponentExists('posts', function (Select $component) use ($posts): bool {
                $component->state([(string) $posts->first()->id]);
                $component->saveStateToRelationship();

                return true;
            });

        foreach ($posts as $post) {
            expect($post->fresh()->author_id)->toBe($user->id);
        }
    });

    it('treats saving a `BelongsToThrough` relationship as a no-op', function (): void {
        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();
        $team = Team::factory()->create(['company_id' => $companyA->id]);
        $user = User::factory()->create(['team_id' => $team->id]);

        livewire(SelectWithBelongsToThroughRelationship::class, ['record' => $user])
            ->assertFormComponentExists('company', function (Select $component) use ($companyB): bool {
                $component->state((string) $companyB->id);
                $component->saveStateToRelationship();

                return true;
            });

        expect($user->fresh()->company?->id)->toBe($companyA->id);
    });

    it('loads state from a `HasOne` relationship', function (): void {
        $user = User::factory()->create();
        $publishedPost = Post::factory()->create(['author_id' => $user->id, 'is_published' => true]);

        livewire(SelectWithHasOneRelationship::class, ['record' => $user])
            ->assertSchemaStateSet([
                'publishedPost' => (string) $publishedPost->id,
            ]);
    });

    it('loads state from a `BelongsToThrough` relationship', function (): void {
        $company = Company::factory()->create();
        $team = Team::factory()->create(['company_id' => $company->id]);
        $user = User::factory()->create(['team_id' => $team->id]);

        livewire(SelectWithBelongsToThroughRelationship::class, ['record' => $user])
            ->assertSchemaStateSet([
                'company' => (string) $company->id,
            ]);
    });

    it('loads state from a `HasManyThrough` relationship via `HasOneOrManyThrough` branch', function (): void {
        $team = Team::factory()->create();
        $user = User::factory()->create(['team_id' => $team->id]);
        $posts = Post::factory()->count(2)->create(['author_id' => $user->id]);

        livewire(SelectWithHasManyThroughRelationship::class, ['record' => $team])
            ->assertSchemaStateSet(function (array $state) use ($posts): array {
                expect(collect($state['posts'])->sort()->values()->all())
                    ->toBe($posts->pluck('id')->map(fn ($id) => (string) $id)->sort()->values()->all());

                return [];
            });
    });
});

describe('eager loading', function (): void {
    it('can load state from a `BelongsToMany` relationship using eager loaded data without additional queries', function (): void {
        $user = User::factory()->create();
        $teams = Team::factory()->count(3)->create();
        $user->teams()->attach($teams);

        $freshUser = $user->fresh();
        expect($freshUser->relationLoaded('teams'))->toBeFalse();

        DB::enableQueryLog();
        DB::flushQueryLog();

        livewire(SelectWithBelongsToManyRelationship::class, ['record' => $freshUser])
            ->assertSchemaStateSet(function (array $state) use ($teams) {
                expect(collect($state['teams'])->sort()->values()->all())
                    ->toBe($teams->pluck('id')->map(fn ($id) => (string) $id)->sort()->values()->all());

                return [];
            });

        $queriesWithoutEagerLoading = count(DB::getQueryLog());

        $eagerUser = $user->fresh();
        $eagerUser->load('teams');
        expect($eagerUser->relationLoaded('teams'))->toBeTrue();

        DB::flushQueryLog();

        livewire(SelectWithBelongsToManyRelationship::class, ['record' => $eagerUser])
            ->assertSchemaStateSet(function (array $state) use ($teams) {
                expect(collect($state['teams'])->sort()->values()->all())
                    ->toBe($teams->pluck('id')->map(fn ($id) => (string) $id)->sort()->values()->all());

                return [];
            });

        $queriesWithEagerLoading = count(DB::getQueryLog());
        DB::disableQueryLog();

        $queriesSaved = $queriesWithoutEagerLoading - $queriesWithEagerLoading;
        expect($queriesSaved)->toBe(2, "Expected to save 2 queries with eager loading, but saved {$queriesSaved}");
    });

    it('does not use eager loaded data when `modifyQueryUsing()` is set', function (): void {
        $user = User::factory()->create();
        $teams = Team::factory()->count(3)->create();
        $user->teams()->attach($teams);

        $freshUser = $user->fresh();
        expect($freshUser->relationLoaded('teams'))->toBeFalse();

        DB::enableQueryLog();
        DB::flushQueryLog();

        livewire(SelectWithBelongsToManyRelationshipAndModifyQuery::class, ['record' => $freshUser])
            ->assertSchemaStateSet(function (array $state) use ($teams) {
                expect(collect($state['teams'])->sort()->values()->all())
                    ->toBe($teams->pluck('id')->map(fn ($id) => (string) $id)->sort()->values()->all());

                return [];
            });

        $queriesWithoutEagerLoading = count(DB::getQueryLog());

        $eagerUser = $user->fresh();
        $eagerUser->load('teams');
        expect($eagerUser->relationLoaded('teams'))->toBeTrue();

        DB::flushQueryLog();

        livewire(SelectWithBelongsToManyRelationshipAndModifyQuery::class, ['record' => $eagerUser])
            ->assertSchemaStateSet(function (array $state) use ($teams) {
                expect(collect($state['teams'])->sort()->values()->all())
                    ->toBe($teams->pluck('id')->map(fn ($id) => (string) $id)->sort()->values()->all());

                return [];
            });

        $queriesWithEagerLoading = count(DB::getQueryLog());
        DB::disableQueryLog();

        expect($queriesWithEagerLoading)->toBe($queriesWithoutEagerLoading);
    });

    it('can load state from a `BelongsTo` relationship using eager loaded data without additional queries', function (): void {
        $team = Team::factory()->create();
        $user = User::factory()->create(['team_id' => $team->id]);

        $freshUser = $user->fresh();
        expect($freshUser->relationLoaded('team'))->toBeFalse();

        DB::enableQueryLog();
        DB::flushQueryLog();

        livewire(SelectWithBelongsToRelationship::class, ['record' => $freshUser])
            ->assertSchemaStateSet([
                'team_id' => (string) $team->id,
            ]);

        $queriesWithoutEagerLoading = count(DB::getQueryLog());

        $eagerUser = $user->fresh();
        $eagerUser->load('team');
        expect($eagerUser->relationLoaded('team'))->toBeTrue();

        DB::flushQueryLog();

        livewire(SelectWithBelongsToRelationship::class, ['record' => $eagerUser])
            ->assertSchemaStateSet([
                'team_id' => (string) $team->id,
            ]);

        $queriesWithEagerLoading = count(DB::getQueryLog());
        DB::disableQueryLog();

        $queriesSaved = $queriesWithoutEagerLoading - $queriesWithEagerLoading;
        expect($queriesSaved)->toBe(1, "Expected to save 1 query with eager loading, but saved {$queriesSaved}");
    });

    it('can load state from a `HasMany` relationship using eager loaded data without additional queries', function (): void {
        $user = User::factory()->create();
        $posts = Post::factory()->count(3)->create(['author_id' => $user->id]);

        $freshUser = $user->fresh();
        expect($freshUser->relationLoaded('posts'))->toBeFalse();

        DB::enableQueryLog();
        DB::flushQueryLog();

        livewire(SelectWithHasManyRelationship::class, ['record' => $freshUser])
            ->assertSchemaStateSet(function (array $state) use ($posts) {
                expect(collect($state['posts'])->sort()->values()->all())
                    ->toBe($posts->pluck('id')->map(fn ($id) => (string) $id)->sort()->values()->all());

                return [];
            });

        $queriesWithoutEagerLoading = count(DB::getQueryLog());

        $eagerUser = $user->fresh();
        $eagerUser->load('posts');
        expect($eagerUser->relationLoaded('posts'))->toBeTrue();

        DB::flushQueryLog();

        livewire(SelectWithHasManyRelationship::class, ['record' => $eagerUser])
            ->assertSchemaStateSet(function (array $state) use ($posts) {
                expect(collect($state['posts'])->sort()->values()->all())
                    ->toBe($posts->pluck('id')->map(fn ($id) => (string) $id)->sort()->values()->all());

                return [];
            });

        $queriesWithEagerLoading = count(DB::getQueryLog());
        DB::disableQueryLog();

        $queriesSaved = $queriesWithoutEagerLoading - $queriesWithEagerLoading;
        expect($queriesSaved)->toBe(2, "Expected to save 2 queries with eager loading, but saved {$queriesSaved}");
    });

    it('can load state from a `BelongsToMany` relationship inside a Repeater using eager loaded data without additional queries', function (): void {
        $undoRepeaterFake = Repeater::fake();

        $company = Company::factory()->create();
        $teams = Team::factory()->count(2)->create(['company_id' => $company->id]);
        $users = User::factory()->count(3)->create();

        foreach ($teams as $team) {
            $team->users()->attach($users);
        }

        DB::enableQueryLog();
        DB::flushQueryLog();

        livewire(RepeaterWithSelectBelongsToManyRelationship::class, ['record' => $company->fresh()])
            ->assertSchemaStateSet(function (array $state) use ($users) {
                expect($state['teams'])->toHaveCount(2);
                foreach ($state['teams'] as $teamState) {
                    expect(collect($teamState['users'])->sort()->values()->all())
                        ->toBe($users->pluck('id')->map(fn ($id) => (string) $id)->sort()->values()->all());
                }

                return [];
            });

        $queriesWithoutEagerLoading = count(DB::getQueryLog());

        DB::flushQueryLog();

        livewire(RepeaterWithSelectBelongsToManyRelationshipEagerLoaded::class, ['record' => $company->fresh()])
            ->assertSchemaStateSet(function (array $state) use ($users) {
                expect($state['teams'])->toHaveCount(2);
                foreach ($state['teams'] as $teamState) {
                    expect(collect($teamState['users'])->sort()->values()->all())
                        ->toBe($users->pluck('id')->map(fn ($id) => (string) $id)->sort()->values()->all());
                }

                return [];
            });

        $queriesWithEagerLoading = count(DB::getQueryLog());
        DB::disableQueryLog();

        $queriesSaved = $queriesWithoutEagerLoading - $queriesWithEagerLoading;
        expect($queriesSaved)->toBe(3, "Expected to save 3 queries with eager loading, but saved {$queriesSaved}");

        $undoRepeaterFake();
    });

    it('can get option labels from a `BelongsToMany` relationship using eager loaded data without additional queries', function (): void {
        $user = User::factory()->create();
        $teams = Team::factory()->count(3)->create();
        $user->teams()->attach($teams);
        $teamIds = $teams->pluck('id')->sort()->values()->all();

        $freshUser = $user->fresh();
        expect($freshUser->relationLoaded('teams'))->toBeFalse();

        DB::enableQueryLog();
        DB::flushQueryLog();

        livewire(SelectWithBelongsToManyRelationship::class, ['record' => $freshUser])
            ->assertFormComponentExists('teams', function (Select $select) use ($teamIds): bool {
                $labels = $select->getOptionLabels();

                expect($labels)->toHaveCount(3);
                expect(collect(array_keys($labels))->sort()->values()->all())->toBe($teamIds);

                return true;
            });

        $queriesWithoutEagerLoading = count(DB::getQueryLog());

        $eagerUser = $user->fresh();
        $eagerUser->load('teams');
        expect($eagerUser->relationLoaded('teams'))->toBeTrue();

        DB::flushQueryLog();

        livewire(SelectWithBelongsToManyRelationship::class, ['record' => $eagerUser])
            ->assertFormComponentExists('teams', function (Select $select) use ($teamIds): bool {
                $labels = $select->getOptionLabels();

                expect($labels)->toHaveCount(3);
                expect(collect(array_keys($labels))->sort()->values()->all())->toBe($teamIds);

                return true;
            });

        $queriesWithEagerLoading = count(DB::getQueryLog());
        DB::disableQueryLog();

        $queriesSaved = $queriesWithoutEagerLoading - $queriesWithEagerLoading;
        expect($queriesSaved)->toBe(3, "Expected to save 3 queries with eager loading, but saved {$queriesSaved}");
    });

    it('can get option label from a `BelongsTo` relationship using eager loaded data without additional queries', function (): void {
        $team = Team::factory()->create();
        $user = User::factory()->create(['team_id' => $team->id]);

        $freshUser = $user->fresh();
        expect($freshUser->relationLoaded('team'))->toBeFalse();

        DB::enableQueryLog();
        DB::flushQueryLog();

        livewire(SelectWithBelongsToRelationship::class, ['record' => $freshUser])
            ->assertFormComponentExists('team_id', function (Select $select) use ($team): bool {
                $label = $select->getOptionLabel();

                expect($label)->toBe($team->name);

                return true;
            });

        $queriesWithoutEagerLoading = count(DB::getQueryLog());

        $eagerUser = $user->fresh();
        $eagerUser->load('team');
        expect($eagerUser->relationLoaded('team'))->toBeTrue();

        DB::flushQueryLog();

        livewire(SelectWithBelongsToRelationship::class, ['record' => $eagerUser])
            ->assertFormComponentExists('team_id', function (Select $select) use ($team): bool {
                $label = $select->getOptionLabel();

                expect($label)->toBe($team->name);

                return true;
            });

        $queriesWithEagerLoading = count(DB::getQueryLog());
        DB::disableQueryLog();

        $queriesSaved = $queriesWithoutEagerLoading - $queriesWithEagerLoading;
        expect($queriesSaved)->toBe(2, "Expected to save 2 queries with eager loading, but saved {$queriesSaved}");
    });
});

class TestComponentWithSelect extends Livewire
{
    public $data = [];

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('number')
                    ->options([
                        'one' => 'One',
                        'two' => 'Two',
                        'three' => 'Three',
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}

class TestComponentWithMultipleSelect extends Livewire
{
    public $data = [];

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('number')
                    ->options([
                        'one' => 'One',
                        'two' => 'Two',
                        'three' => 'Three',
                    ])
                    ->multiple(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}

class TestComponentWithSelectCustomSearchResults extends Livewire
{
    public $data = [];

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('number')
                    ->getSearchResultsUsing(fn (string $search) => collect([
                        'one' => 'One',
                        'two' => 'Two',
                        'three' => 'Three',
                    ])->filter(fn (string $label, string $value): bool => str_contains($label, $search) || str_contains($value, $search)))
                    ->getOptionLabelUsing(fn (string $value): ?string => match ($value) {
                        'one' => 'One',
                        'two' => 'Two',
                        'three' => 'Three',
                        default => null,
                    }),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}

class TestComponentWithMultipleSelectCustomSearchResults extends Livewire
{
    public $data = [];

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('number')
                    ->getSearchResultsUsing(fn (string $search) => collect([
                        'one' => 'One',
                        'two' => 'Two',
                        'three' => 'Three',
                    ])->filter(fn (string $label, string $value): bool => str_contains($label, $search) || str_contains($value, $search)))
                    ->getOptionLabelsUsing(function (array $values): array {
                        $labels = [];

                        foreach ($values as $value) {
                            $labels[$value] = match ($value) {
                                'one' => 'One',
                                'two' => 'Two',
                                'three' => 'Three',
                                default => null,
                            };
                        }

                        return $labels;
                    })
                    ->multiple(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}

class TestComponentWithBelongsToManySelect extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public User $record;

    public function mount(): void
    {
        $this->form->fill($this->record->attributesToArray());
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('teams')
                    ->relationship('teams', 'name')
                    ->preload(),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class TestComponentWithBelongsToManyMultipleSelect extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public User $record;

    public function mount(): void
    {
        $this->form->fill($this->record->attributesToArray());
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('teams')
                    ->relationship('teams', 'name')
                    ->multiple()
                    ->preload(),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class SelectWithBelongsToManyRelationship extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public User $record;

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('teams')
                    ->relationship('teams', 'name')
                    ->multiple()
                    ->preload(),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class SelectWithBelongsToManyRelationshipAndModifyQuery extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public User $record;

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('teams')
                    ->relationship(
                        'teams',
                        'name',
                        modifyQueryUsing: fn ($query) => $query->orderBy('name'),
                    )
                    ->multiple()
                    ->preload(),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class SelectWithBelongsToRelationship extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public User $record;

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('team_id')
                    ->relationship('team', 'name')
                    ->preload(),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class SelectWithHasManyRelationship extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public User $record;

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('posts')
                    ->relationship('posts', 'title')
                    ->multiple()
                    ->preload(),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class RepeaterWithSelectBelongsToRelationship extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public User $record;

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Repeater::make('posts')
                    ->relationship('posts')
                    ->schema([
                        TextInput::make('title'),
                        Select::make('author_id')
                            ->relationship('author', 'name')
                            ->preload(),
                    ]),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class RepeaterWithSelectBelongsToRelationshipEagerLoaded extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public User $record;

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Repeater::make('posts')
                    ->relationship(
                        'posts',
                        modifyQueryUsing: fn ($query) => $query->with('author'),
                    )
                    ->schema([
                        TextInput::make('title'),
                        Select::make('author_id')
                            ->relationship('author', 'name')
                            ->preload(),
                    ]),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class RepeaterWithSelectBelongsToManyRelationship extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public Company $record;

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Repeater::make('teams')
                    ->relationship('teams')
                    ->schema([
                        TextInput::make('name'),
                        Select::make('users')
                            ->relationship('users', 'name')
                            ->multiple(),
                    ]),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class RepeaterWithSelectBelongsToManyRelationshipEagerLoaded extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public Company $record;

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Repeater::make('teams')
                    ->relationship(
                        'teams',
                        modifyQueryUsing: fn ($query) => $query->with('users'),
                    )
                    ->schema([
                        TextInput::make('name'),
                        Select::make('users')
                            ->relationship('users', 'name')
                            ->multiple(),
                    ]),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

describe('browser interactions', function (): void {
    it('can select an option from a `native(false)` select dropdown in the browser', function (): void {
        retry(10, function (): void {
            $this->actingAs(User::factory()->create());

            visit('/select-test')
                ->assertSee('Select Test')
                ->assertSee('Single Select')
                ->assertDontSee('One')
                ->assertDontSee('Two')
                ->click('[data-testid="single-select"] .fi-select-input-btn')
                ->assertSee('One')
                ->assertSee('Two')
                ->click('Two')
                ->assertDontSee('One')
                ->assertSee('Two')
                ->assertNoSmoke()
                ->assertNoAccessibilityIssues();

            visit('/select-test')
                ->inDarkMode()
                ->assertNoAccessibilityIssues();
        });
    });

    it('can select multiple options from a `multiple()` select dropdown in the browser', function (): void {
        retry(10, function (): void {
            $this->actingAs(User::factory()->create());

            visit('/select-test')
                ->assertSee('Multiple Select')
                ->assertDontSee('Apple')
                ->assertDontSee('Cherry')
                ->click('[data-testid="multiple-select"] .fi-select-input-btn')
                ->assertSee('Apple')
                ->click('Apple')
                ->click('Cherry')
                ->keys('[data-testid="multiple-select"] .fi-select-input-btn', 'Escape')
                ->assertSee('Apple')
                ->assertSee('Cherry')
                ->assertNoSmoke();
        });
    });

    it('can navigate options using keyboard in a `native(false)` select dropdown in the browser', function (): void {
        retry(10, function (): void {
            $this->actingAs(User::factory()->create());

            visit('/select-test')
                ->assertSee('Single Select')
                ->assertDontSee('Two')
                ->click('[data-testid="single-select"] .fi-select-input-btn')
                ->assertSee('One')
                ->keys('[data-testid="single-select"] .fi-select-input-option.fi-selected', ['ArrowDown', 'Enter'])
                ->assertDontSee('One')
                ->assertSee('Two')
                ->assertNoSmoke();
        });
    });

    it('can search and select an option in a `searchable()` select dropdown in the browser', function (): void {
        retry(10, function (): void {
            $this->actingAs(User::factory()->create());

            visit('/select-test')
                ->assertSee('Searchable Select')
                ->assertDontSee('Purple')
                ->click('[data-testid="searchable-select"] .fi-select-input-btn')
                ->assertSee('Red')
                ->assertSee('Purple')
                ->type('[data-testid="searchable-select"] .fi-select-input-search-ctn input', 'pur')
                ->assertSee('Purple')
                ->assertDontSee('Red')
                ->click('Purple')
                ->assertSee('Purple')
                ->assertNoSmoke();
        });
    });

    it('can clear a selected value in a `native(false)` select dropdown in the browser', function (): void {
        retry(10, function (): void {
            $this->actingAs(User::factory()->create());

            visit('/select-test')
                ->assertSee('Clearable Select')
                ->assertDontSee('Active')
                ->click('[data-testid="clearable-select"] .fi-select-input-btn')
                ->assertSee('Active')
                ->click('Active')
                ->assertSee('Active')
                ->click('[data-testid="clearable-select"] .fi-select-input-value-remove-btn')
                ->assertDontSee('Active')
                ->assertNoSmoke();
        });
    });

    it('can remove individual items from a `multiple()` select dropdown in the browser', function (): void {
        retry(10, function (): void {
            $this->actingAs(User::factory()->create());

            visit('/select-test')
                ->assertSee('Multiple Select')
                ->assertDontSee('Apple')
                ->assertDontSee('Banana')
                ->click('[data-testid="multiple-select"] .fi-select-input-btn')
                ->assertSee('Apple')
                ->click('Apple')
                ->click('Banana')
                ->keys('[data-testid="multiple-select"] .fi-select-input-btn', 'Escape')
                ->assertSee('Apple')
                ->assertSee('Banana')
                ->click('[data-testid="multiple-select"] [aria-label="Remove Apple"]')
                ->assertDontSee('Apple')
                ->assertSee('Banana')
                ->assertNoSmoke();
        });
    });

    it('shows "no options" message when dynamic options returns empty array', function (): void {
        retry(10, function (): void {
            $this->actingAs(User::factory()->create());

            visit('/select-test')
                ->assertSee('Dynamic Empty Options')
                ->click('[data-testid="dynamic-empty-options-select"] .fi-select-input-btn')
                ->assertSee('No options available')
                ->assertSee('No options available')
                ->assertDontSee('Loading')
                ->assertNoSmoke();
        });
    });

    it('shows options when dynamic options returns options', function (): void {
        retry(10, function (): void {
            $this->actingAs(User::factory()->create());

            visit('/select-test')
                ->assertSee('Dynamic With Options')
                ->click('[data-testid="dynamic-with-options-select"] .fi-select-input-btn')
                ->assertSee('Option 1')
                ->assertSee('Option 1')
                ->assertSee('Option 2')
                ->assertDontSee('No options available')
                ->assertDontSee('Loading')
                ->assertNoSmoke();
        });
    });

    it('shows "no options" message when dynamic options and search returns empty array', function (): void {
        retry(10, function (): void {
            $this->actingAs(User::factory()->create());

            visit('/select-test')
                ->assertSee('Dynamic Options And Search Empty')
                ->click('[data-testid="dynamic-options-and-search-empty-select"] .fi-select-input-btn')
                ->assertSee('No options available')
                ->assertSee('No options available')
                ->assertDontSee('Loading')
                ->assertNoSmoke();
        });
    });

    it('shows "no options" message when static options is empty array', function (): void {
        retry(10, function (): void {
            $this->actingAs(User::factory()->create());

            visit('/select-test')
                ->assertSee('Static Empty Options')
                ->click('[data-testid="static-empty-options-select"] .fi-select-input-btn')
                ->assertSee('No options available')
                ->assertSee('No options available')
                ->assertNoSmoke();
        });
    });

    it('shows options when dynamic options returns non-empty array', function (): void {
        retry(10, function (): void {
            $this->actingAs(User::factory()->create());

            visit('/select-test')
                ->assertSee('Dynamic Options With Results')
                ->click('[data-testid="dynamic-options-with-results-select"] .fi-select-input-btn')
                ->assertSee('Dynamic Option 1')
                ->assertSee('Dynamic Option 1')
                ->assertSee('Dynamic Option 2')
                ->assertDontSee('No options available')
                ->assertDontSee('Loading')
                ->assertNoSmoke();
        });
    });

    it('only adds one remove button when selecting multiple options in sequence', function (): void {
        retry(10, function (): void {
            $this->actingAs(User::factory()->create());

            visit('/select-test')
                ->assertSee('Clearable With Placeholder')
                // Verify no remove button initially (placeholder shown)
                ->assertMissing('[data-testid="clearable-with-placeholder-select"] .fi-select-input-value-remove-btn')
                // Select first option
                ->click('[data-testid="clearable-with-placeholder-select"] .fi-select-input-btn')
                ->assertSee('First')
                ->click('First')
                // Verify exactly one remove button exists
                ->assertVisible('[data-testid="clearable-with-placeholder-select"] .fi-select-input-value-remove-btn')
                ->assertScript('document.querySelectorAll(\'[data-testid="clearable-with-placeholder-select"] .fi-select-input-value-remove-btn\').length', 1)
                // Select second option
                ->click('[data-testid="clearable-with-placeholder-select"] .fi-select-input-btn')
                ->assertSee('Second')
                ->click('Second')
                // Verify still exactly one remove button exists (not duplicated)
                ->assertVisible('[data-testid="clearable-with-placeholder-select"] .fi-select-input-value-remove-btn')
                ->assertScript('document.querySelectorAll(\'[data-testid="clearable-with-placeholder-select"] .fi-select-input-value-remove-btn\').length', 1)
                // Select third option
                ->click('[data-testid="clearable-with-placeholder-select"] .fi-select-input-btn')
                ->assertSee('Third')
                ->click('Third')
                // Verify still exactly one remove button exists
                ->assertScript('document.querySelectorAll(\'[data-testid="clearable-with-placeholder-select"] .fi-select-input-value-remove-btn\').length', 1)
                ->assertNoSmoke();
        });
    });

    it('removes the clear button when selection is cleared', function (): void {
        retry(10, function (): void {
            $this->actingAs(User::factory()->create());

            visit('/select-test')
                ->assertSee('Clearable With Placeholder')
                // Verify no remove button initially
                ->assertMissing('[data-testid="clearable-with-placeholder-select"] .fi-select-input-value-remove-btn')
                // Select an option
                ->click('[data-testid="clearable-with-placeholder-select"] .fi-select-input-btn')
                ->assertSee('First')
                ->click('First')
                // Verify remove button exists
                ->assertVisible('[data-testid="clearable-with-placeholder-select"] .fi-select-input-value-remove-btn')
                // Clear the selection
                ->click('[data-testid="clearable-with-placeholder-select"] .fi-select-input-value-remove-btn')
                // Verify remove button is gone and placeholder is shown
                ->assertMissing('[data-testid="clearable-with-placeholder-select"] .fi-select-input-value-remove-btn')
                ->assertSee('Select an option...')
                ->assertNoSmoke();
        });
    });

    it('adds clearable class only when an option is selected', function (): void {
        retry(10, function (): void {
            $this->actingAs(User::factory()->create());

            visit('/select-test')
                ->assertSee('Clearable With Placeholder')
                // Verify no clearable class initially
                ->assertMissing('[data-testid="clearable-with-placeholder-select"] .fi-select-input-ctn-clearable')
                // Select an option
                ->click('[data-testid="clearable-with-placeholder-select"] .fi-select-input-btn')
                ->assertSee('First')
                ->click('First')
                // Verify clearable class is added
                ->assertVisible('[data-testid="clearable-with-placeholder-select"] .fi-select-input-ctn-clearable')
                // Clear the selection
                ->click('[data-testid="clearable-with-placeholder-select"] .fi-select-input-value-remove-btn')
                // Verify clearable class is removed
                ->assertMissing('[data-testid="clearable-with-placeholder-select"] .fi-select-input-ctn-clearable')
                ->assertNoSmoke();
        });
    });
});

describe('option labels', function (): void {
    it('can get `getOptionLabel()` from flat options', function (): void {
        livewire(TestSelectWithFlatOptions::class)
            ->fillForm(['category' => 'electronics'])
            ->assertFormComponentExists('category', function (Select $select): bool {
                expect($select->getOptionLabel())->toBe('Electronics');

                return true;
            });
    });

    it('can get `getOptionLabel()` from grouped options', function (): void {
        livewire(TestSelectWithGroupedOptions::class)
            ->fillForm(['product' => 'laptop'])
            ->assertFormComponentExists('product', function (Select $select): bool {
                expect($select->getOptionLabel())->toBe('Laptop');

                return true;
            });
    });

    it('returns state as default when option not found in `getOptionLabel()`', function (): void {
        livewire(TestSelectWithFlatOptions::class)
            ->fillForm(['category' => 'unknown'])
            ->assertFormComponentExists('category', function (Select $select): bool {
                expect($select->getOptionLabel())->toBe('unknown');

                return true;
            });
    });

    it('returns `null` when option not found and `withDefault` is `false` in `getOptionLabel()`', function (): void {
        livewire(TestSelectWithFlatOptions::class)
            ->fillForm(['category' => 'unknown'])
            ->assertFormComponentExists('category', function (Select $select): bool {
                expect($select->getOptionLabel(withDefault: false))->toBeNull();

                return true;
            });
    });

    it('can get `getOptionLabel()` using custom `getOptionLabelUsing()` closure', function (): void {
        livewire(TestSelectWithCustomOptionLabel::class)
            ->fillForm(['category' => 'electronics'])
            ->assertFormComponentExists('category', function (Select $select): bool {
                expect($select->getOptionLabel())->toBe('ELECTRONICS');

                return true;
            });
    });

    it('can get `getOptionLabels()` from flat options for multiple select', function (): void {
        livewire(TestMultipleSelectWithFlatOptions::class)
            ->fillForm(['categories' => ['electronics', 'books']])
            ->assertFormComponentExists('categories', function (Select $select): bool {
                expect($select->getOptionLabels())->toBe([
                    'electronics' => 'Electronics',
                    'books' => 'Books',
                ]);

                return true;
            });
    });

    it('can get `getOptionLabels()` from grouped options for multiple select', function (): void {
        livewire(TestMultipleSelectWithGroupedOptions::class)
            ->fillForm(['products' => ['laptop', 'shirt']])
            ->assertFormComponentExists('products', function (Select $select): bool {
                expect($select->getOptionLabels())->toBe([
                    'laptop' => 'Laptop',
                    'shirt' => 'Shirt',
                ]);

                return true;
            });
    });

    it('returns value as default when option not found in `getOptionLabels()`', function (): void {
        livewire(TestMultipleSelectWithFlatOptions::class)
            ->fillForm(['categories' => ['electronics', 'unknown']])
            ->assertFormComponentExists('categories', function (Select $select): bool {
                expect($select->getOptionLabels())->toBe([
                    'electronics' => 'Electronics',
                    'unknown' => 'unknown',
                ]);

                return true;
            });
    });

    it('excludes missing options when `withDefaults` is `false` in `getOptionLabels()`', function (): void {
        livewire(TestMultipleSelectWithFlatOptions::class)
            ->fillForm(['categories' => ['electronics', 'unknown']])
            ->assertFormComponentExists('categories', function (Select $select): bool {
                expect($select->getOptionLabels(withDefaults: false))->toBe([
                    'electronics' => 'Electronics',
                ]);

                return true;
            });
    });

});

describe('options', function (): void {
    it('can get `getOptions()` from static array', function (): void {
        $select = Select::make('category')
            ->options([
                'electronics' => 'Electronics',
                'clothing' => 'Clothing',
            ]);

        expect($select->getOptions())->toBe([
            'electronics' => 'Electronics',
            'clothing' => 'Clothing',
        ]);
    });

    it('can get `getOptions()` from closure', function (): void {
        $select = Select::make('category')
            ->options(fn (): array => [
                'electronics' => 'Electronics',
                'clothing' => 'Clothing',
            ]);

        expect($select->getOptions())->toBe([
            'electronics' => 'Electronics',
            'clothing' => 'Clothing',
        ]);
    });

    it('can get `getOptions()` from enum class string', function (): void {
        $select = Select::make('category')
            ->options(StringBackedEnum::class);

        $options = $select->getOptions();

        expect($options)->toBe([
            'one' => 'One',
            'two' => 'Two',
            'three' => 'Three',
        ]);
    });

    it('can check if option is disabled using `isOptionDisabled()`', function (): void {
        $select = Select::make('category')
            ->options([
                'active' => 'Active',
                'inactive' => 'Inactive',
                'archived' => 'Archived',
            ])
            ->disableOptionWhen(fn (string $value): bool => $value === 'archived');

        expect($select->isOptionDisabled('active', 'Active'))->toBeFalse();
        expect($select->isOptionDisabled('archived', 'Archived'))->toBeTrue();
    });

    it('can get enabled options using `getEnabledOptions()`', function (): void {
        $select = Select::make('category')
            ->options([
                'active' => 'Active',
                'inactive' => 'Inactive',
                'archived' => 'Archived',
            ])
            ->disableOptionWhen(fn (string $value): bool => $value === 'archived');

        expect($select->getEnabledOptions())->toBe([
            'active' => 'Active',
            'inactive' => 'Inactive',
        ]);
    });

    it('can get enabled options from grouped options using `getEnabledOptions()`', function (): void {
        $select = Select::make('category')
            ->options([
                'Status' => [
                    'active' => 'Active',
                    'archived' => 'Archived',
                ],
                'Type' => [
                    'normal' => 'Normal',
                    'premium' => 'Premium',
                ],
            ])
            ->disableOptionWhen(fn (string $value): bool => in_array($value, ['archived', 'premium']));

        expect($select->getEnabledOptions())->toBe([
            'active' => 'Active',
            'normal' => 'Normal',
        ]);
    });

    it('returns `true` for `hasDisabledOptions()` when closure is set', function (): void {
        $select = Select::make('category')
            ->options(['a' => 'A', 'b' => 'B'])
            ->disableOptionWhen(fn (string $value): bool => $value === 'b');

        expect($select->hasDisabledOptions())->toBeTrue();
    });

    it('returns `false` for `hasDisabledOptions()` when no closure is set', function (): void {
        $select = Select::make('category')
            ->options(['a' => 'A', 'b' => 'B']);

        expect($select->hasDisabledOptions())->toBeFalse();
    });

});

describe('JS transform and properties', function (): void {
    it('can transform options for JS using default transformer', function (): void {
        $select = Select::make('category')
            ->options([
                'electronics' => 'Electronics',
                'clothing' => 'Clothing',
            ]);

        $transformed = $select->getOptionsForJs();

        expect($transformed)->toBe([
            ['label' => 'Electronics', 'value' => 'electronics', 'isDisabled' => false],
            ['label' => 'Clothing', 'value' => 'clothing', 'isDisabled' => false],
        ]);
    });

    it('can transform grouped options for JS', function (): void {
        $select = Select::make('category')
            ->options([
                'Electronics' => [
                    'phone' => 'Phone',
                    'laptop' => 'Laptop',
                ],
            ]);

        $transformed = $select->getOptionsForJs();

        expect($transformed)->toBe([
            [
                'label' => 'Electronics',
                'options' => [
                    ['label' => 'Phone', 'value' => 'phone', 'isDisabled' => false],
                    ['label' => 'Laptop', 'value' => 'laptop', 'isDisabled' => false],
                ],
            ],
        ]);
    });

    it('marks disabled options in JS transform', function (): void {
        $select = Select::make('category')
            ->options([
                'active' => 'Active',
                'archived' => 'Archived',
            ])
            ->disableOptionWhen(fn (string $value): bool => $value === 'archived');

        $transformed = $select->getOptionsForJs();

        expect($transformed)->toBe([
            ['label' => 'Active', 'value' => 'active', 'isDisabled' => false],
            ['label' => 'Archived', 'value' => 'archived', 'isDisabled' => true],
        ]);
    });

    it('can use custom `transformOptionsForJsUsing()` callback', function (): void {
        $select = Select::make('category')
            ->options([
                'electronics' => 'Electronics',
            ])
            ->transformOptionsForJsUsing(fn (Select $component, array $options): array => collect($options)
                ->map(fn ($label, $value): array => ['id' => $value, 'name' => $label])
                ->values()
                ->all());

        $transformed = $select->getOptionsForJs();

        expect($transformed)->toBe([
            ['id' => 'electronics', 'name' => 'Electronics'],
        ]);
    });

    it('returns empty array when transforming empty options', function (): void {
        $select = Select::make('category')
            ->options([]);

        expect($select->getOptionsForJs())->toBe([]);
    });

    it('can check `isMultiple()` returns correct value', function (): void {
        $singleSelect = Select::make('category');
        $multipleSelect = Select::make('categories')->multiple();

        expect($singleSelect->isMultiple())->toBeFalse();
        expect($multipleSelect->isMultiple())->toBeTrue();
    });

    it('can check `isSearchable()` defaults to `true` for multiple select', function (): void {
        $singleSelect = Select::make('category');
        $multipleSelect = Select::make('categories')->multiple();

        expect($singleSelect->isSearchable())->toBeFalse();
        expect($multipleSelect->isSearchable())->toBeTrue();
    });

    it('can get search results using `getSearchResultsUsing()`', function (): void {
        $select = Select::make('category')
            ->getSearchResultsUsing(fn (string $search): array => [
                'result1' => "Found: {$search}",
            ]);

        $results = $select->getSearchResults('test');

        expect($results)->toBe([
            'result1' => 'Found: test',
        ]);
    });

    it('returns empty array for `getSearchResults()` when no callback is set', function (): void {
        $select = Select::make('category');

        expect($select->getSearchResults('test'))->toBe([]);
    });

    it('can get options limit using `getOptionsLimit()`', function (): void {
        $defaultSelect = Select::make('category');
        $limitedSelect = Select::make('category')->optionsLimit(100);

        expect($defaultSelect->getOptionsLimit())->toBe(50);
        expect($limitedSelect->getOptionsLimit())->toBe(100);
    });

});

describe('relationship options', function (): void {
    it('can get `getOptions()` from `BelongsTo` relationship', function (): void {
        $users = User::factory()->count(3)->create();

        livewire(TestSelectWithBelongsToRelationship::class)
            ->assertFormComponentExists('author_id', function (Select $select) use ($users): bool {
                $options = $select->getOptions();

                expect($options)->toHaveCount(3);
                expect(array_values($options))->toContain($users[0]->name);
                expect(array_values($options))->toContain($users[1]->name);
                expect(array_values($options))->toContain($users[2]->name);

                return true;
            });
    });

    it('can get `getSearchResults()` from `BelongsTo` relationship', function (): void {
        User::factory()->create(['name' => 'John Doe']);
        User::factory()->create(['name' => 'Jane Smith']);
        User::factory()->create(['name' => 'Bob Johnson']);

        livewire(TestSelectWithSearchableBelongsToRelationship::class)
            ->assertFormComponentExists('author_id', function (Select $select): bool {
                $results = $select->getSearchResults('John');

                expect($results)->toHaveCount(2);
                expect(array_values($results))->toContain('John Doe');
                expect(array_values($results))->toContain('Bob Johnson');

                return true;
            });
    });

    it('can get `getOptionLabel()` from `BelongsTo` relationship with preload', function (): void {
        $author = User::factory()->create(['name' => 'Test Author']);
        $post = Post::factory()->create(['author_id' => $author->id]);

        livewire(TestSelectWithPreloadedBelongsToRelationship::class, ['record' => $post])
            ->assertFormComponentExists('author_id', function (Select $select) use ($author): bool {
                expect($select->getOptionLabel())->toBe($author->name);

                return true;
            });
    });

    it('can get `getOptions()` from `BelongsToMany` relationship with preload', function (): void {
        $user = User::factory()->create();
        $teams = Team::factory()->count(3)->create();

        livewire(TestSelectWithPreloadedBelongsToManyRelationship::class, ['record' => $user])
            ->assertFormComponentExists('teams', function (Select $select) use ($teams): bool {
                $options = $select->getOptions();

                expect($options)->toHaveCount(3);
                expect(array_values($options))->toContain($teams[0]->name);
                expect(array_values($options))->toContain($teams[1]->name);
                expect(array_values($options))->toContain($teams[2]->name);

                return true;
            });
    });

    it('can get `getSearchResults()` from `BelongsToMany` relationship', function (): void {
        Team::factory()->create(['name' => 'Alpha Team']);
        Team::factory()->create(['name' => 'Beta Team']);
        Team::factory()->create(['name' => 'Gamma Group']);

        livewire(TestSelectWithSearchableBelongsToManyRelationship::class)
            ->assertFormComponentExists('teams', function (Select $select): bool {
                $results = $select->getSearchResults('Team');

                expect($results)->toHaveCount(2);
                expect(array_values($results))->toContain('Alpha Team');
                expect(array_values($results))->toContain('Beta Team');

                return true;
            });
    });

    it('can get `getOptionLabels()` from `BelongsToMany` relationship with preload', function (): void {
        $user = User::factory()->create();
        $teams = Team::factory()->count(2)->create();
        $user->teams()->attach($teams);

        livewire(TestSelectWithPreloadedBelongsToManyRelationship::class, ['record' => $user])
            ->assertFormComponentExists('teams', function (Select $select) use ($teams): bool {
                $labels = $select->getOptionLabels();

                expect($labels)->toHaveCount(2);
                expect(array_values($labels))->toContain($teams[0]->name);
                expect(array_values($labels))->toContain($teams[1]->name);

                return true;
            });
    });

    it('returns empty array for `getOptions()` when relationship is searchable without preload', function (): void {
        User::factory()->count(3)->create();

        livewire(TestSelectWithSearchableBelongsToRelationship::class)
            ->assertFormComponentExists('author_id', function (Select $select): bool {
                expect($select->getOptions())->toBe([]);

                return true;
            });
    });

    it('returns options when relationship is searchable with preload', function (): void {
        $users = User::factory()->count(3)->create();
        $post = Post::factory()->create(['author_id' => $users[0]->id]);

        livewire(TestSelectWithPreloadedBelongsToRelationship::class, ['record' => $post])
            ->assertFormComponentExists('author_id', function (Select $select): bool {
                $options = $select->getOptions();

                expect($options)->toHaveCount(3);

                return true;
            });
    });

    it('can use `modifyQueryUsing` to filter relationship options', function (): void {
        User::factory()->create(['name' => 'Admin User']);
        User::factory()->create(['name' => 'Normal User']);
        User::factory()->create(['name' => 'Admin Manager']);

        livewire(TestSelectWithModifiedRelationshipQuery::class)
            ->assertFormComponentExists('author_id', function (Select $select): bool {
                $options = $select->getOptions();

                expect($options)->toHaveCount(2);
                expect(array_values($options))->toContain('Admin User');
                expect(array_values($options))->toContain('Admin Manager');
                expect(array_values($options))->not->toContain('Normal User');

                return true;
            });
    });

    it('can use `getOptionLabelFromRecordUsing()` for custom relationship labels', function (): void {
        User::factory()->create(['name' => 'John', 'email' => 'john@example.com']);
        User::factory()->create(['name' => 'Jane', 'email' => 'jane@example.com']);

        livewire(TestSelectWithCustomRelationshipLabel::class)
            ->assertFormComponentExists('author_id', function (Select $select): bool {
                $options = $select->getOptions();

                expect(array_values($options))->toContain('John (john@example.com)');
                expect(array_values($options))->toContain('Jane (jane@example.com)');

                return true;
            });
    });
});

class TestSelectWithFlatOptions extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('category')
                    ->options([
                        'electronics' => 'Electronics',
                        'clothing' => 'Clothing',
                    ]),
            ])
            ->statePath('data');
    }
}

class TestSelectWithGroupedOptions extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('product')
                    ->options([
                        'Electronics' => [
                            'phone' => 'Phone',
                            'laptop' => 'Laptop',
                        ],
                        'Clothing' => [
                            'shirt' => 'Shirt',
                        ],
                    ]),
            ])
            ->statePath('data');
    }
}

class TestSelectWithCustomOptionLabel extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('category')
                    ->getOptionLabelUsing(static fn (string $value): string => strtoupper($value)),
            ])
            ->statePath('data');
    }
}

class TestMultipleSelectWithFlatOptions extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('categories')
                    ->multiple()
                    ->options([
                        'electronics' => 'Electronics',
                        'clothing' => 'Clothing',
                        'books' => 'Books',
                    ]),
            ])
            ->statePath('data');
    }
}

class TestMultipleSelectWithGroupedOptions extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('products')
                    ->multiple()
                    ->options([
                        'Electronics' => [
                            'phone' => 'Phone',
                            'laptop' => 'Laptop',
                        ],
                        'Clothing' => [
                            'shirt' => 'Shirt',
                        ],
                    ]),
            ])
            ->statePath('data');
    }
}

class TestSelectWithBelongsToRelationship extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public ?Post $record = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('author_id')
                    ->relationship('author', 'name'),
            ])
            ->model($this->record ?? Post::class)
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class TestSelectWithSearchableBelongsToRelationship extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('author_id')
                    ->relationship('author', 'name')
                    ->searchable(),
            ])
            ->model(Post::class)
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class TestSelectWithPreloadedBelongsToRelationship extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public Post $record;

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('author_id')
                    ->relationship('author', 'name')
                    ->preload(),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class TestSelectWithBelongsToManyRelationship extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public ?User $record = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('teams')
                    ->relationship('teams', 'name')
                    ->multiple(),
            ])
            ->model($this->record ?? User::class)
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class TestSelectWithSearchableBelongsToManyRelationship extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('teams')
                    ->relationship('teams', 'name')
                    ->multiple()
                    ->searchable(),
            ])
            ->model(User::class)
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class TestSelectWithPreloadedBelongsToManyRelationship extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public User $record;

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('teams')
                    ->relationship('teams', 'name')
                    ->multiple()
                    ->preload(),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class TestSelectWithModifiedRelationshipQuery extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('author_id')
                    ->relationship(
                        'author',
                        'name',
                        modifyQueryUsing: fn ($query) => $query->where('name', 'like', 'Admin%'),
                    ),
            ])
            ->model(Post::class)
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class TestSelectWithCustomRelationshipLabel extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('author_id')
                    ->relationship('author', 'name')
                    ->getOptionLabelFromRecordUsing(fn (User $record): string => "{$record->name} ({$record->email})"),
            ])
            ->model(Post::class)
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class TestComponentWithBelongsToRelationshipValidation extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('author_id')
                    ->relationship('author', 'name')
                    ->preload(),
            ])
            ->model(Post::class)
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class TestComponentWithSearchableBelongsToRelationshipValidation extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('author_id')
                    ->relationship('author', 'name')
                    ->searchable(),
            ])
            ->model(Post::class)
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class TestComponentWithBelongsToManyRelationshipValidation extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public User $record;

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('teams')
                    ->relationship('teams', 'name')
                    ->multiple()
                    ->preload(),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class TestComponentWithSearchableBelongsToManyRelationshipValidation extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public User $record;

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('teams')
                    ->relationship('teams', 'name')
                    ->multiple()
                    ->searchable(),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class TestComponentWithDisabledOptions extends Livewire
{
    public $data = [];

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'pending' => 'Pending',
                        'archived' => 'Archived',
                    ])
                    ->disableOptionWhen(fn (string $value): bool => $value === 'archived'),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}

class TestComponentWithMultipleDisabledOptions extends Livewire
{
    public $data = [];

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('statuses')
                    ->options([
                        'active' => 'Active',
                        'pending' => 'Pending',
                        'archived' => 'Archived',
                    ])
                    ->multiple()
                    ->disableOptionWhen(fn (string $value): bool => $value === 'archived'),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}

it('can set `boolean()` options', function (): void {
    $select = Select::make('active')
        ->boolean();

    $options = $select->getOptions();

    expect($options)->toHaveCount(2);
    expect($options[1])->toBe('Yes');
    expect($options[0])->toBe('No');
});

it('can set custom labels for `boolean()`', function (): void {
    $select = Select::make('active')
        ->boolean(trueLabel: 'Enabled', falseLabel: 'Disabled');

    $options = $select->getOptions();

    expect($options[1])->toBe('Enabled');
    expect($options[0])->toBe('Disabled');
});

it('can set `reorderable()`', function (): void {
    $select = Select::make('items');

    expect($select->isReorderable())->toBeFalse();

    $select->reorderable();

    expect($select->isReorderable())->toBeTrue();
});

it('can set `position()`', function (): void {
    $select = Select::make('category');

    expect($select->getPosition())->toBeNull();

    $select->position('bottom');

    expect($select->getPosition())->toBe('bottom');
});

it('can set `forceSearchCaseInsensitive()`', function (): void {
    $select = Select::make('category');

    expect($select->isSearchForcedCaseInsensitive())->toBeNull();

    $select->forceSearchCaseInsensitive();

    expect($select->isSearchForcedCaseInsensitive())->toBeTrue();
});

it('can disable `wrapOptionLabels()`', function (): void {
    $select = Select::make('category');

    expect($select->canOptionLabelsWrap())->toBeTrue();

    $select->wrapOptionLabels(false);

    expect($select->canOptionLabelsWrap())->toBeFalse();
});

it('can set `dynamicOptions()`', function (): void {
    $select = Select::make('category');

    expect($select->hasDynamicOptions())->toBeFalse();

    $select->dynamicOptions();

    expect($select->hasDynamicOptions())->toBeTrue();
});

it('can set `maxItemsMessage()`', function (): void {
    $select = Select::make('items')
        ->multiple()
        ->maxItemsMessage('Too many items');

    expect($select->getMaxItemsMessage())->toBe('Too many items');
});

it('returns `createOption` for `getCreateOptionActionName()`', function (): void {
    $select = Select::make('category');

    expect($select->getCreateOptionActionName())->toBe('createOption');
});

it('returns `editOption` for `getEditOptionActionName()`', function (): void {
    $select = Select::make('category');

    expect($select->getEditOptionActionName())->toBe('editOption');
});

it('returns `false` for `hasCreateOptionActionFormSchema()` by default', function (): void {
    $select = Select::make('category');

    expect($select->hasCreateOptionActionFormSchema())->toBeFalse();
});

it('returns `true` for `hasCreateOptionActionFormSchema()` when set', function (): void {
    $select = Select::make('category')
        ->createOptionForm([
            TextInput::make('name'),
        ]);

    expect($select->hasCreateOptionActionFormSchema())->toBeTrue();
});

it('returns `false` for `hasEditOptionActionFormSchema()` by default', function (): void {
    $select = Select::make('category');

    expect($select->hasEditOptionActionFormSchema())->toBeFalse();
});

it('returns `true` for `hasEditOptionActionFormSchema()` when set', function (): void {
    $select = Select::make('category')
        ->editOptionForm([
            TextInput::make('name'),
        ]);

    expect($select->hasEditOptionActionFormSchema())->toBeTrue();
});

it('can set `createOptionUsing()` and retrieve with `getCreateOptionUsing()`', function (): void {
    $select = Select::make('category');

    expect($select->getCreateOptionUsing())->toBeNull();

    $callback = static fn (array $data): int => 1;
    $select->createOptionUsing($callback);

    expect($select->getCreateOptionUsing())->toBe($callback);
});

it('can set `updateOptionUsing()` and retrieve with `getUpdateOptionUsing()`', function (): void {
    $select = Select::make('category');

    expect($select->getUpdateOptionUsing())->toBeNull();

    $callback = static function (array $data): void {};
    $select->updateOptionUsing($callback);

    expect($select->getUpdateOptionUsing())->toBe($callback);
});

it('can set `createOptionModalHeading()`', function (): void {
    $select = Select::make('category');

    expect($select->getCreateOptionModalHeading())->toBeNull();

    $select->createOptionModalHeading('New Category');

    expect($select->getCreateOptionModalHeading())->toBe('New Category');
});

it('can set `editOptionModalHeading()`', function (): void {
    $select = Select::make('category');

    expect($select->getEditOptionModalHeading())->toBeNull();

    $select->editOptionModalHeading('Edit Category');

    expect($select->getEditOptionModalHeading())->toBe('Edit Category');
});

it('can set `boolean()` with custom `$placeholder`', function (): void {
    $select = Select::make('active')
        ->boolean(placeholder: 'Choose...');

    $options = $select->getOptions();

    expect($options)->toHaveCount(2);
    expect($select->getPlaceholder())->toBe('Choose...');
});

it('returns fluent `$this` from `manageOptionForm()`', function (): void {
    $select = Select::make('category');

    $result = $select->manageOptionForm([
        TextInput::make('name'),
    ]);

    expect($result)->toBe($select);
    expect($select->hasCreateOptionActionFormSchema())->toBeTrue();
    expect($select->hasEditOptionActionFormSchema())->toBeTrue();
});

it('can set `reorderable()` with a `Closure`', function (): void {
    $select = Select::make('tags')
        ->multiple()
        ->reorderable(static fn (): bool => true);

    expect($select->isReorderable())->toBeTrue();
});

it('can set `optionsLimit()` with a `Closure`', function (): void {
    $select = Select::make('category')
        ->optionsLimit(static fn (): int => 200);

    expect($select->getOptionsLimit())->toBe(200);
});

it('returns `false` for `hasOptionLabelFromRecordUsingCallback()` by default', function (): void {
    $select = Select::make('category');

    expect($select->hasOptionLabelFromRecordUsingCallback())->toBeFalse();
});

it('returns `true` for `hasOptionLabelFromRecordUsingCallback()` when set', function (): void {
    $select = Select::make('category')
        ->getOptionLabelFromRecordUsing(static fn ($record): string => $record->name);

    expect($select->hasOptionLabelFromRecordUsingCallback())->toBeTrue();
});

describe('Closure support', function (): void {
    it('can set `multiple()` with a `Closure`', function (): void {
        $select = Select::make('tags')
            ->multiple(static fn (): bool => true);

        expect($select->isMultiple())->toBeTrue();
    });

    it('can set `position()` with a `Closure`', function (): void {
        $select = Select::make('status')
            ->position(static fn (): string => 'above');

        expect($select->getPosition())->toBe('above');
    });

    it('can set `dynamicOptions()` with a `Closure`', function (): void {
        $select = Select::make('status')
            ->dynamicOptions(static fn (): bool => true);

        expect($select->hasDynamicOptions())->toBeTrue();
    });

    it('can set `maxItemsMessage()` with a `Closure`', function (): void {
        $select = Select::make('tags')
            ->maxItemsMessage(static fn (): string => 'Too many!');

        expect($select->getMaxItemsMessage())->toBe('Too many!');
    });
});

describe('relationship metadata', function (): void {
    it('returns `false` for `hasRelationship()` by default', function (): void {
        $select = Select::make('team_id');

        expect($select->hasRelationship())->toBeFalse();
    });

    it('returns `true` for `hasRelationship()` after `relationship()` is called', function (): void {
        $select = Select::make('team_id')
            ->relationship('team', 'name');

        expect($select->hasRelationship())->toBeTrue();
    });

    it('returns `null` for `getRelationshipName()` by default', function (): void {
        $select = Select::make('team_id');

        expect($select->getRelationshipName())->toBeNull();
    });

    it('uses field name when `relationship()` name is `null`', function (): void {
        $select = Select::make('team')
            ->relationship(titleAttribute: 'name');

        expect($select->getRelationshipName())->toBe('team');
    });

    it('returns `null` for `getRelationshipTitleAttribute()` by default', function (): void {
        $select = Select::make('team_id');

        expect($select->getRelationshipTitleAttribute())->toBeNull();
    });
});

describe('search columns', function (): void {
    it('returns `null` for `getSearchColumns()` by default', function (): void {
        $select = Select::make('team_id');

        expect($select->getSearchColumns())->toBeNull();
    });

    it('can set search columns via `searchable()` with array', function (): void {
        $select = Select::make('team_id')
            ->searchable(['name', 'email']);

        expect($select->getSearchColumns())->toBe(['name', 'email']);
        expect($select->isSearchable())->toBeTrue();
    });
});

describe('`hasDynamicOptions()` logic', function (): void {
    it('returns `false` by default', function (): void {
        $select = Select::make('status');

        expect($select->hasDynamicOptions())->toBeFalse();
    });

    it('can be cleared with `null` to revert to default', function (): void {
        $select = Select::make('status')
            ->dynamicOptions()
            ->dynamicOptions(null);

        expect($select->hasDynamicOptions())->toBeFalse();
    });
});

describe('saveRelationshipsUsing BelongsToMany with pivot data', function (): void {
    it('can save `BelongsToMany` with `pivotData()` via `syncWithPivotValues()`', function (): void {
        $user = User::factory()->create();
        $teams = Team::factory()->count(2)->create();

        livewire(TestSelectWithBelongsToManyAndPivotData::class, ['record' => $user])
            ->fillForm(['teams' => $teams->pluck('id')->map(fn ($id) => (string) $id)->all()])
            ->call('save');

        $pivotRows = DB::table('team_user')
            ->where('user_id', $user->id)
            ->get();

        expect($pivotRows)->toHaveCount(2);
        expect($pivotRows->first()->role)->toBe('editor');
        expect($pivotRows->last()->role)->toBe('editor');
    });

    it('can detach and re-sync `BelongsToMany` with `pivotData()`', function (): void {
        $user = User::factory()->create();
        $teams = Team::factory()->count(3)->create();
        $user->teams()->attach($teams);

        livewire(TestSelectWithBelongsToManyAndPivotData::class, ['record' => $user])
            ->fillForm(['teams' => $teams->take(1)->pluck('id')->map(fn ($id) => (string) $id)->all()])
            ->call('save');

        $pivotRows = DB::table('team_user')
            ->where('user_id', $user->id)
            ->get();

        expect($pivotRows)->toHaveCount(1);
        expect($pivotRows->first()->role)->toBe('editor');
    });
});

describe('rendering', function (): void {
    it('can render with static `options()`', function (): void {
        livewire(RenderSelectWithStaticOptions::class)
            ->assertSuccessful()
            ->assertSeeHtml('Electronics')
            ->assertSeeHtml('Clothing');
    });

    it('can render with `options()` from `Closure`', function (): void {
        livewire(RenderSelectWithClosureOptions::class)
            ->assertSuccessful()
            ->assertSeeHtml('Electronics');
    });

    it('can render with `options()` from enum', function (): void {
        livewire(RenderSelectWithEnumOptions::class)
            ->assertSuccessful()
            ->assertSeeHtml('One')
            ->assertSeeHtml('Two');
    });

    it('can render with `multiple()`', function (): void {
        livewire(RenderSelectWithMultiple::class)
            ->assertSuccessful();
    });

    it('can render with `multiple()` set via `Closure`', function (): void {
        livewire(RenderSelectWithClosureMultiple::class)
            ->assertSuccessful();
    });

    it('can render with `searchable()`', function (): void {
        livewire(RenderSelectWithSearchable::class)
            ->assertSuccessful();
    });

    it('can render with `boolean()`', function (): void {
        livewire(RenderSelectWithBoolean::class)
            ->assertSuccessful()
            ->assertSeeHtml('Yes')
            ->assertSeeHtml('No');
    });

    it('can render with `boolean()` custom labels', function (): void {
        livewire(RenderSelectWithBooleanCustomLabels::class)
            ->assertSuccessful()
            ->assertSeeHtml('Enabled')
            ->assertSeeHtml('Disabled');
    });

    it('can render with `boolean()` custom placeholder', function (): void {
        livewire(RenderSelectWithBooleanCustomPlaceholder::class)
            ->assertSuccessful()
            ->assertSeeHtml('Choose...');
    });

    it('can render with `reorderable()`', function (): void {
        livewire(RenderSelectWithReorderable::class)
            ->assertSuccessful();
    });

    it('can render with `reorderable()` set via `Closure`', function (): void {
        livewire(RenderSelectWithClosureReorderable::class)
            ->assertSuccessful();
    });

    it('can render with `position()`', function (): void {
        livewire(RenderSelectWithPosition::class)
            ->assertSuccessful();
    });

    it('can render with `position()` set via `Closure`', function (): void {
        livewire(RenderSelectWithClosurePosition::class)
            ->assertSuccessful();
    });

    it('can render with `wrapOptionLabels(false)`', function (): void {
        livewire(RenderSelectWithNoWrapOptionLabels::class)
            ->assertSuccessful();
    });

    it('can render with `dynamicOptions()`', function (): void {
        livewire(RenderSelectWithDynamicOptions::class)
            ->assertSuccessful();
    });

    it('can render with `dynamicOptions()` set via `Closure`', function (): void {
        livewire(RenderSelectWithClosureDynamicOptions::class)
            ->assertSuccessful();
    });

    it('can render with `maxItemsMessage()`', function (): void {
        livewire(RenderSelectWithMaxItemsMessage::class)
            ->assertSuccessful();
    });

    it('can render with `maxItemsMessage()` set via `Closure`', function (): void {
        livewire(RenderSelectWithClosureMaxItemsMessage::class)
            ->assertSuccessful();
    });

    it('can render with `optionsLimit()` set via `Closure`', function (): void {
        livewire(RenderSelectWithClosureOptionsLimit::class)
            ->assertSuccessful();
    });

    it('can render with `disableOptionWhen()`', function (): void {
        livewire(RenderSelectWithDisabledOption::class)
            ->assertSuccessful()
            ->assertSeeHtml('Active')
            ->assertSeeHtml('Archived');
    });

    it('can render with `allowHtml()`', function (): void {
        livewire(RenderSelectWithAllowHtml::class)
            ->assertSuccessful();
    });

    it('can render with `native(false)`', function (): void {
        livewire(RenderSelectWithNonNative::class)
            ->assertSuccessful();
    });

    it('can render with `createOptionForm()`', function (): void {
        livewire(RenderSelectWithCreateOptionForm::class)
            ->assertSuccessful();
    });

    it('can render with `editOptionForm()`', function (): void {
        livewire(RenderSelectWithEditOptionForm::class)
            ->assertSuccessful();
    });

    it('can render with grouped options', function (): void {
        livewire(RenderSelectWithGroupedOptions::class)
            ->assertSuccessful()
            ->assertSeeHtml('Phone')
            ->assertSeeHtml('Laptop');
    });

    it('can render with `placeholder()`', function (): void {
        livewire(RenderSelectWithPlaceholder::class)
            ->assertSuccessful()
            ->assertSeeHtml('Select an option...');
    });
});

class RenderSelectWithStaticOptions extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Select::make('category')->options(['electronics' => 'Electronics', 'clothing' => 'Clothing']),
        ])->statePath('data');
    }
}

class RenderSelectWithClosureOptions extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Select::make('category')->options(static fn (): array => ['electronics' => 'Electronics']),
        ])->statePath('data');
    }
}

class RenderSelectWithEnumOptions extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Select::make('category')->options(StringBackedEnum::class),
        ])->statePath('data');
    }
}

class RenderSelectWithMultiple extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Select::make('categories')->options(['a' => 'A', 'b' => 'B'])->multiple(),
        ])->statePath('data');
    }
}

class RenderSelectWithClosureMultiple extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Select::make('categories')->options(['a' => 'A', 'b' => 'B'])->multiple(static fn (): bool => true),
        ])->statePath('data');
    }
}

class RenderSelectWithSearchable extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Select::make('category')->options(['a' => 'A', 'b' => 'B'])->searchable(),
        ])->statePath('data');
    }
}

class RenderSelectWithBoolean extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([Select::make('active')->boolean()])->statePath('data');
    }
}

class RenderSelectWithBooleanCustomLabels extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Select::make('active')->boolean(trueLabel: 'Enabled', falseLabel: 'Disabled'),
        ])->statePath('data');
    }
}

class RenderSelectWithBooleanCustomPlaceholder extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Select::make('active')->boolean(placeholder: 'Choose...'),
        ])->statePath('data');
    }
}

class RenderSelectWithReorderable extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Select::make('tags')->options(['a' => 'A', 'b' => 'B'])->multiple()->reorderable(),
        ])->statePath('data');
    }
}

class RenderSelectWithClosureReorderable extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Select::make('tags')->options(['a' => 'A', 'b' => 'B'])->multiple()->reorderable(static fn (): bool => true),
        ])->statePath('data');
    }
}

class RenderSelectWithPosition extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Select::make('category')->options(['a' => 'A'])->searchable()->position('bottom'),
        ])->statePath('data');
    }
}

class RenderSelectWithClosurePosition extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Select::make('category')->options(['a' => 'A'])->searchable()->position(static fn (): string => 'above'),
        ])->statePath('data');
    }
}

class RenderSelectWithNoWrapOptionLabels extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Select::make('category')->options(['a' => 'A'])->searchable()->wrapOptionLabels(false),
        ])->statePath('data');
    }
}

class RenderSelectWithDynamicOptions extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Select::make('category')->options(['a' => 'A'])->searchable()->dynamicOptions(),
        ])->statePath('data');
    }
}

class RenderSelectWithClosureDynamicOptions extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Select::make('category')->options(['a' => 'A'])->searchable()->dynamicOptions(static fn (): bool => true),
        ])->statePath('data');
    }
}

class RenderSelectWithMaxItemsMessage extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Select::make('tags')->options(['a' => 'A', 'b' => 'B'])->multiple()->maxItemsMessage('Too many items'),
        ])->statePath('data');
    }
}

class RenderSelectWithClosureMaxItemsMessage extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Select::make('tags')->options(['a' => 'A'])->multiple()->maxItemsMessage(static fn (): string => 'Too many!'),
        ])->statePath('data');
    }
}

class RenderSelectWithClosureOptionsLimit extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Select::make('category')->options(['a' => 'A'])->searchable()->optionsLimit(static fn (): int => 200),
        ])->statePath('data');
    }
}

class RenderSelectWithDisabledOption extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Select::make('status')->options(['active' => 'Active', 'archived' => 'Archived'])
                ->disableOptionWhen(static fn (string $value): bool => $value === 'archived'),
        ])->statePath('data');
    }
}

class RenderSelectWithAllowHtml extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Select::make('category')->options(['a' => '<b>Bold</b>'])->allowHtml(),
        ])->statePath('data');
    }
}

class RenderSelectWithNonNative extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Select::make('category')->options(['a' => 'A', 'b' => 'B'])->native(false),
        ])->statePath('data');
    }
}

class RenderSelectWithCreateOptionForm extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Select::make('category')
                ->options(['a' => 'A'])
                ->searchable()
                ->createOptionForm([TextInput::make('name')]),
        ])->statePath('data');
    }
}

class RenderSelectWithEditOptionForm extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Select::make('category')
                ->options(['a' => 'A'])
                ->searchable()
                ->editOptionForm([TextInput::make('name')]),
        ])->statePath('data');
    }
}

class RenderSelectWithGroupedOptions extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Select::make('product')->options([
                'Electronics' => ['phone' => 'Phone', 'laptop' => 'Laptop'],
                'Clothing' => ['shirt' => 'Shirt'],
            ]),
        ])->statePath('data');
    }
}

class RenderSelectWithPlaceholder extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Select::make('category')
                ->options(['a' => 'A', 'b' => 'B'])
                ->placeholder('Select an option...'),
        ])->statePath('data');
    }
}

class TestSelectWithBelongsToManyAndPivotData extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public User $record;

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('teams')
                    ->relationship('teams', 'name')
                    ->multiple()
                    ->preload()
                    ->pivotData(['role' => 'editor']),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class SelectWithHasOneRelationship extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public User $record;

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('publishedPost')
                    ->relationship('publishedPost', 'title')
                    ->preload(),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class SelectWithHasManyRelationshipAndModifyQuery extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public static ?\Closure $onModify = null;

    public $data = [];

    public User $record;

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('posts')
                    ->relationship(
                        'posts',
                        'title',
                        modifyQueryUsing: function ($query) {
                            (static::$onModify)?->__invoke();

                            return $query;
                        },
                    )
                    ->multiple()
                    ->preload(),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class SelectWithHasManyThroughRelationship extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public Team $record;

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('posts')
                    ->relationship('posts', 'title')
                    ->multiple()
                    ->preload(),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class SelectWithBelongsToThroughRelationship extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public User $record;

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('company')
                    ->relationship('company', 'name')
                    ->preload(),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class SelectWithBelongsToManyRelationshipAndExplicitLimit extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('teams')
                    ->relationship(
                        'teams',
                        'name',
                        modifyQueryUsing: fn ($query) => $query->limit(2),
                    )
                    ->searchable()
                    ->multiple(),
            ])
            ->model(User::class)
            ->statePath('data');
    }
}

class SelectWithBelongsToManyRelationshipAndExistingOrder extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('teams')
                    ->relationship(
                        'teams',
                        'name',
                        modifyQueryUsing: fn ($query) => $query->orderBy('name', 'desc'),
                    )
                    ->searchable()
                    ->multiple(),
            ])
            ->model(User::class)
            ->statePath('data');
    }
}

class SelectWithBelongsToManyRelationshipAndOptionLabelFromRecord extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('teams')
                    ->relationship('teams', 'name')
                    ->getOptionLabelFromRecordUsing(fn (Team $record): string => "Custom: {$record->name}")
                    ->searchable()
                    ->multiple(),
            ])
            ->model(User::class)
            ->statePath('data');
    }
}

class SelectWithJsonPathTitleAttribute extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public User $record;

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('posts')
                    ->relationship('posts', 'json->title')
                    ->searchable()
                    ->multiple(),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}
