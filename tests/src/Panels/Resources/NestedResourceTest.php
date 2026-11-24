<?php

use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\ParentResourceRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Models\Company;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Resources\Companies\CompanyResource;
use Filament\Tests\Fixtures\Resources\Companies\Resources\CompanyTeamResource;
use Filament\Tests\Fixtures\Resources\Companies\Resources\CompanyTeamResource\Pages\CreateCompanyTeam;
use Filament\Tests\Fixtures\Resources\Companies\Resources\CompanyTeamResource\Pages\EditCompanyTeam;
use Filament\Tests\Fixtures\Resources\Companies\Resources\CompanyTeamResource\Pages\ListCompanyTeams;
use Filament\Tests\Fixtures\Resources\Companies\Resources\CompanyTeamResource\Pages\ViewCompanyTeam;
use Filament\Tests\Fixtures\Resources\Tickets\Resources\TicketDepartmentResource;
use Filament\Tests\Fixtures\Resources\Tickets\TicketResource;
use Filament\Tests\Fixtures\Resources\Users\Resources\UserPostResource;
use Filament\Tests\Fixtures\Resources\Users\Resources\UserPostResource\Pages\CreateUserPost;
use Filament\Tests\Fixtures\Resources\Users\Resources\UserPostResource\Pages\EditUserPost;
use Filament\Tests\Fixtures\Resources\Users\Resources\UserPostResource\Pages\ListUserPosts;
use Filament\Tests\Fixtures\Resources\Users\Resources\UserPostResource\Pages\ViewUserPost;
use Filament\Tests\Fixtures\Resources\Users\UserResource;
use Filament\Tests\Panels\Resources\TestCase;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

use function Filament\Tests\livewire;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\assertSoftDeleted;

uses(TestCase::class);

describe('nested resource index URLs', function (): void {
    it('resolves the parent relation page when the page key matches the relationship name', function (): void {
        $parentRecord = Ticket::factory()->create();

        expect(TicketDepartmentResource::getIndexUrl([
            'ticket' => $parentRecord,
        ]))->toBe(TicketResource::getUrl('departments', [
            'record' => $parentRecord,
        ]));
    });

    it('prefers an explicit page name when the relation page uses a custom key', function (): void {
        $parentRecord = User::factory()->create();

        expect(UserPostResource::getIndexUrl([
            'author' => $parentRecord,
        ]))->toBe(UserResource::getUrl('managePosts', [
            'record' => $parentRecord,
        ]));
    });

    it('can restore the relationship page name with `page(null)`', function (): void {
        $registration = UserPostResource::getParentResourceRegistration();

        expect($registration->getPageName())->toBe('managePosts')
            ->and($registration->page(null)->getPageName())->toBe('posts');
    });

    it('falls back to the parent view page when the explicit page does not exist', function (): void {
        $parentRecord = Company::factory()->create();

        expect(CompanyTeamResource::getIndexUrl([
            'company' => $parentRecord,
        ]))->toBe(CompanyResource::getUrl('view', [
            'relation' => 'teams',
            'record' => $parentRecord,
        ]));
    });
});

describe('soft-deletable nested resource', function (): void {
    it('can render with the navigation hierarchy in breadcrumbs regardless of panel navigation visibility', function (): void {
        $parentRecord = User::factory()->create();
        $url = UserPostResource::getUrl('index', [
            'author' => $parentRecord,
        ]);

        $panel = Filament::getCurrentOrDefaultPanel()
            ->breadcrumbs(hasNavigationHierarchy: true);

        $this->get($url)->assertSuccessful();

        $panel->navigation(false);

        $this->get($url)->assertSuccessful();
    });

    it('can render list page', function (): void {
        $parentRecord = User::factory()->create();

        $this->get(UserPostResource::getUrl('index', [
            'author' => $parentRecord,
        ]))->assertSuccessful();
    });

    it('can list records', function (): void {
        $parentRecord = User::factory()->create();
        $posts = Post::factory()->count(10)->create([
            'author_id' => $parentRecord->getKey(),
        ]);

        livewire(ListUserPosts::class, [
            'parentRecord' => $parentRecord,
        ])
            ->assertCanSeeTableRecords($posts);
    });

    it('can render create page', function (): void {
        $parentRecord = User::factory()->create();

        $this->get(UserPostResource::getUrl('create', [
            'author' => $parentRecord,
        ]))->assertSuccessful();
    });

    it('rejects a scope-excluded supplied parent before creating or saving records', function (string $pageClass, string $action): void {
        $parentRecord = User::factory()->create();
        $post = Post::factory()->create(['author_id' => $parentRecord->getKey()]);
        $originalTitle = $post->title;
        $component = livewire($pageClass, [
            'parentRecord' => $parentRecord,
            ...(($action === 'save') ? ['record' => $post->getKey()] : []),
        ])->fillForm(['title' => 'must-not-write-for-excluded-parent'])->assertSuccessful();
        $globalScopes = User::getAllGlobalScopes();

        try {
            User::addGlobalScope(
                'exclude-supplied-parent',
                static fn (Builder $query): Builder => $query->whereKeyNot($parentRecord->getKey()),
            );

            $component->call($action)->assertNotFound();

            expect($post->refresh()->title)->toBe($originalTitle);
            assertDatabaseMissing(Post::class, ['title' => 'must-not-write-for-excluded-parent']);
        } finally {
            User::setAllGlobalScopes($globalScopes);
        }
    })->with([
        [CreateUserPost::class, 'create'],
        [EditUserPost::class, 'save'],
    ]);

    it('can create', function (): void {
        $parentRecord = User::factory()->create();
        $newData = Post::factory()->make();

        livewire(CreateUserPost::class, [
            'parentRecord' => $parentRecord,
        ])
            ->fillForm([
                'content' => $newData->content,
                'tags' => $newData->tags,
                'title' => $newData->title,
                'rating' => $newData->rating,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(Post::class, [
            'author_id' => $parentRecord->getKey(),
            'title' => $newData->title,
            'content' => $newData->content,
            'rating' => $newData->rating,
        ]);
    });

    it('can validate input on create', function (): void {
        $parentRecord = User::factory()->create();

        livewire(CreateUserPost::class, [
            'parentRecord' => $parentRecord,
        ])
            ->fillForm([
                'title' => null,
            ])
            ->call('create')
            ->assertHasFormErrors(['title' => 'required']);
    });

    it('can render view page', function (): void {
        $parentRecord = User::factory()->create();
        $post = Post::factory()->create([
            'author_id' => $parentRecord->getKey(),
        ]);

        $this->get(UserPostResource::getUrl('view', [
            'author' => $parentRecord,
            'record' => $post,
        ]))->assertSuccessful();
    });

    it('can retrieve data on view page', function (): void {
        $parentRecord = User::factory()->create();
        $post = Post::factory()->create([
            'author_id' => $parentRecord->getKey(),
        ]);

        livewire(ViewUserPost::class, [
            'parentRecord' => $parentRecord,
            'record' => $post->getKey(),
        ])
            ->assertSchemaStateSet([
                'content' => $post->content,
                'tags' => $post->tags,
                'title' => $post->title,
            ]);
    });

    it('can render edit page', function (): void {
        $parentRecord = User::factory()->create();
        $post = Post::factory()->create([
            'author_id' => $parentRecord->getKey(),
        ]);

        $this->get(UserPostResource::getUrl('edit', [
            'author' => $parentRecord,
            'record' => $post,
        ]))->assertSuccessful();
    });

    it('can retrieve data on edit page', function (): void {
        $parentRecord = User::factory()->create();
        $post = Post::factory()->create([
            'author_id' => $parentRecord->getKey(),
        ]);

        livewire(EditUserPost::class, [
            'parentRecord' => $parentRecord,
            'record' => $post->getKey(),
        ])
            ->assertSchemaStateSet([
                'content' => $post->content,
                'tags' => $post->tags,
                'title' => $post->title,
                'rating' => $post->rating,
            ]);
    });

    it('rejects a stale edit page snapshot after the record moves to another parent', function (): void {
        $parentRecord = User::factory()->create();
        $newParentRecord = User::factory()->create();
        $post = Post::factory()->create([
            'author_id' => $parentRecord->getKey(),
        ]);
        $page = livewire(EditUserPost::class, [
            'parentRecord' => $parentRecord,
            'record' => $post->getKey(),
        ])->assertSuccessful();

        $post->update(['author_id' => $newParentRecord->getKey()]);

        $page
            ->set('data.title', 'must-not-cross-parent-boundary')
            ->assertNotFound();
    });

    it('validates the parent resource scope when `resolveParentRecord()` is overridden', function (): void {
        $parentRecord = User::factory()->create();
        $post = Post::factory()->create([
            'author_id' => $parentRecord->getKey(),
        ]);
        EditUserPostWithCustomParentResolver::$testParentRecordRouteParameters = [
            'author' => $parentRecord->getRouteKey(),
        ];

        livewire(EditUserPostWithCustomParentResolver::class, [
            'record' => $post->getKey(),
        ])
            ->set('data.title', 'still scoped to parent')
            ->assertSuccessful();
    });

    it('rejects parent substitution from an overridden `resolveParentRecord()`', function (): void {
        $parentRecord = User::factory()->create();
        $replacementParentRecord = User::factory()->create();
        $post = Post::factory()->create([
            'author_id' => $parentRecord->getKey(),
        ]);
        EditUserPostWithCustomParentResolver::$testParentRecordRouteParameters = [
            'author' => $parentRecord->getRouteKey(),
        ];
        $page = livewire(EditUserPostWithCustomParentResolver::class, [
            'record' => $post->getKey(),
        ])->assertSuccessful();

        EditUserPostWithCustomParentResolver::$replacementParentRecord = $replacementParentRecord;

        try {
            $page
                ->set('data.title', 'must-not-use-replacement-parent')
                ->assertNotFound();
        } finally {
            EditUserPostWithCustomParentResolver::$replacementParentRecord = null;
            EditUserPostWithCustomParentResolver::$testParentRecordRouteParameters = [];
        }
    });

    it('rejects a stale edit page snapshot after an ancestor moves to another parent', function (): void {
        $company = Company::factory()->create();
        $newCompany = Company::factory()->create();
        $team = Team::factory()->create(['company_id' => $company->getKey()]);
        $user = User::factory()->create();
        $user->teams()->attach($team);
        EditDeepNestedUser::$testParentRecordRouteParameters = [
            'company' => $company->getRouteKey(),
            'team' => $team->name,
        ];
        $page = livewire(EditDeepNestedUser::class, [
            'parentRecord' => $team,
            'record' => $user->getKey(),
        ])->assertSuccessful();

        $team->update(['company_id' => $newCompany->getKey()]);

        $page
            ->set('data.name', 'must-not-cross-ancestor-boundary')
            ->assertNotFound();
    });

    it('uses the current parent when its route key resolves to another record', function (): void {
        $company = Company::factory()->create();
        $newCompany = Company::factory()->create();
        $team = Team::factory()->create([
            'company_id' => $company->getKey(),
            'name' => 'Shared team route key',
        ]);
        CreateDeepNestedUser::$testParentRecordRouteParameters = [
            'company' => $company->getRouteKey(),
            'team' => $team->name,
        ];
        $page = livewire(CreateDeepNestedUser::class, [
            'parentRecord' => $team,
        ])->assertSuccessful();

        $currentTeam = Team::factory()->create([
            'company_id' => $company->getKey(),
            'name' => 'Shared team route key',
        ]);
        $team->update(['company_id' => $newCompany->getKey()]);

        $page
            ->set('data.name', 'uses-current-parent')
            ->assertSuccessful()
            ->assertSet('parentRecord.id', $currentTeam->getKey());
    });

    it('validates a lazy page table widget parent before the synthetic page boots or mounts', function (): void {
        $company = Company::factory()->create();
        $team = Team::factory()->create([
            'company_id' => $company->getKey(),
            'name' => 'Page table widget team',
        ]);
        $user = User::factory()->create();
        $user->teams()->attach($team);
        $component = livewire(DeepNestedPageTableWidget::class, [
            'lazy' => true,
            'parentRecord' => $team,
        ])->assertSuccessful();

        expect(preg_match(
            "/__lazyLoad\\('([^']+)'\\)/",
            html_entity_decode($component->html()),
            $matches,
        ))->toBe(1);

        $component
            ->call('__lazyLoad', $matches[1])
            ->call('loadPageTable')
            ->assertSet('pageTableRecordsCount', 1)
            ->assertSuccessful();

        expect(ListDeepNestedUsers::$hasBooted)->toBeTrue()
            ->and(ListDeepNestedUsers::$hasMounted)->toBeTrue();

        ListDeepNestedUsers::$hasBooted = false;
        ListDeepNestedUsers::$hasMounted = false;
        $globalScopes = Team::getAllGlobalScopes();

        try {
            Team::addGlobalScope(
                'exclude-page-table-widget-parent',
                fn (Builder $query): Builder => $query->whereKeyNot($team->getKey()),
            );

            expect(fn () => $component->call('loadPageTable'))->toThrow(ModelNotFoundException::class);

            expect(ListDeepNestedUsers::$hasBooted)->toBeFalse()
                ->and(ListDeepNestedUsers::$hasMounted)->toBeFalse();
        } finally {
            Team::setAllGlobalScopes($globalScopes);
        }
    });

    it('resolves a supplied parent using its resource binding, write connection, and primary key', function (): void {
        $company = Company::factory()->create();
        Team::factory()->create(['company_id' => $company->getKey(), 'name' => 'Shared route key']);
        $team = Team::factory()->create(['company_id' => $company->getKey(), 'name' => 'Shared route key']);
        $page = app(CreateDeepNestedUser::class);
        $page->parentRecord = $team;
        CreateDeepNestedUser::$testParentRecordRouteParameters = ['unrelated' => 'parameter'];

        try {
            $page->mountParentRecord();

            expect($page->getParentRecord()->is($team))->toBeTrue()
                ->and($page->getParentRecord())->not->toBe($team)
                ->and(app('nested-parent-used-write-connection'))->toBeTrue();

            app()->instance('nested-parent-binding-denied', true);
            $deniedPage = app(CreateDeepNestedUser::class);
            $deniedPage->parentRecord = $team;

            expect(fn () => $deniedPage->mountParentRecord())->toThrow(ModelNotFoundException::class);
        } finally {
            CreateDeepNestedUser::$testParentRecordRouteParameters = [];
        }
    });

    it('rejects a replacement model returned by a supplied parent resource binding', function (): void {
        $company = Company::factory()->create();
        $team = Team::factory()->create(['company_id' => $company->getKey()]);
        $replacementTeam = Team::factory()->create(['company_id' => $company->getKey()]);
        app()->instance('nested-parent-binding-replacement', $replacementTeam);
        CreateDeepNestedUser::$testParentRecordRouteParameters = [];
        $page = app(CreateDeepNestedUser::class);
        $page->parentRecord = $team;

        expect(fn () => $page->mountParentRecord())->toThrow(ModelNotFoundException::class);
    });

    it('does not use a supplied parent to bypass incomplete ancestor route parameters', function (): void {
        $company = Company::factory()->create();
        $team = Team::factory()->create(['company_id' => $company->getKey()]);
        $page = app(CreateDeepNestedUser::class);
        $page->parentRecord = $team;
        CreateDeepNestedUser::$testParentRecordRouteParameters = ['team' => $team->name];

        try {
            expect(fn () => $page->mountParentRecord())->toThrow(ModelNotFoundException::class);
        } finally {
            CreateDeepNestedUser::$testParentRecordRouteParameters = [];
        }
    });

    it('allows a supplied soft-deleted parent approved by its resource', function (): void {
        $ticket = Ticket::factory()->create();
        $ticket->delete();
        $page = app(ListTicketDepartments::class);
        $page->parentRecord = $ticket;

        $page->mountParentRecord();

        expect($page->getParentRecord()->is($ticket))->toBeTrue()
            ->and($page->getParentRecord()->trashed())->toBeTrue();
    });

    it('retains the resolved synthetic page parent including mount parameter overrides', function (bool $hasOverride): void {
        $company = Company::factory()->create();
        $team = Team::factory()->create(['company_id' => $company->getKey()]);
        $overrideTeam = Team::factory()->create(['company_id' => $company->getKey()]);
        $widget = app(DeepNestedPageTableWidget::class);
        $widget->parentRecord = $team;
        $widget->parentRecordOverride = $hasOverride ? $overrideTeam : null;
        $expectedParent = $hasOverride ? $overrideTeam : $team;

        $page = $widget->getResolvedTablePage();

        expect($page->getParentRecord()->is($expectedParent))->toBeTrue()
            ->and($page->getParentRecord())->not->toBe($expectedParent);
    })->with([false, true]);

    it('can save', function (): void {
        $parentRecord = User::factory()->create();
        $post = Post::factory()->create([
            'author_id' => $parentRecord->getKey(),
        ]);
        $newData = Post::factory()->make();

        livewire(EditUserPost::class, [
            'parentRecord' => $parentRecord,
            'record' => $post->getKey(),
        ])
            ->fillForm([
                'content' => $newData->content,
                'tags' => $newData->tags,
                'title' => $newData->title,
                'rating' => $newData->rating,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($post->refresh())
            ->content->toBe($newData->content)
            ->tags->toBe($newData->tags)
            ->title->toBe($newData->title)
            ->rating->toBe($newData->rating);
    });

    it('can validate input on edit', function (): void {
        $parentRecord = User::factory()->create();
        $post = Post::factory()->create([
            'author_id' => $parentRecord->getKey(),
        ]);

        livewire(EditUserPost::class, [
            'parentRecord' => $parentRecord,
            'record' => $post->getKey(),
        ])
            ->fillForm([
                'title' => null,
            ])
            ->call('save')
            ->assertHasFormErrors(['title' => 'required']);
    });

    it('can delete from edit page', function (): void {
        $parentRecord = User::factory()->create();
        $post = Post::factory()->create([
            'author_id' => $parentRecord->getKey(),
        ]);

        livewire(EditUserPost::class, [
            'parentRecord' => $parentRecord,
            'record' => $post->getKey(),
        ])
            ->callAction(DeleteAction::class);

        assertSoftDeleted($post);
    });

    it('can delete from table', function (): void {
        $parentRecord = User::factory()->create();
        $post = Post::factory()->create([
            'author_id' => $parentRecord->getKey(),
        ]);

        livewire(ListUserPosts::class, [
            'parentRecord' => $parentRecord,
        ])
            ->callTableAction(DeleteAction::class, $post);

        assertSoftDeleted($post);
    });

    it('can bulk delete from table', function (): void {
        $parentRecord = User::factory()->create();
        $posts = Post::factory()->count(10)->create([
            'author_id' => $parentRecord->getKey(),
        ]);

        livewire(ListUserPosts::class, [
            'parentRecord' => $parentRecord,
        ])
            ->callTableBulkAction(DeleteBulkAction::class, $posts);

        foreach ($posts as $post) {
            assertSoftDeleted($post);
        }
    });

    it('can search records', function (): void {
        $parentRecord = User::factory()->create();
        $posts = Post::factory()->count(10)->create([
            'author_id' => $parentRecord->getKey(),
        ]);

        $title = $posts->first()->title;

        livewire(ListUserPosts::class, [
            'parentRecord' => $parentRecord,
        ])
            ->searchTable($title)
            ->assertCanSeeTableRecords($posts->where('title', $title))
            ->assertCanNotSeeTableRecords($posts->where('title', '!=', $title));
    });

    it('can sort records by title', function (): void {
        $parentRecord = User::factory()->create();
        Post::factory()->count(10)->create([
            'author_id' => $parentRecord->getKey(),
        ]);

        $sortedAsc = Post::query()
            ->where('author_id', $parentRecord->getKey())
            ->orderBy('title')
            ->orderBy('id')
            ->get();
        $sortedDesc = Post::query()
            ->where('author_id', $parentRecord->getKey())
            ->orderByDesc('title')
            ->orderByDesc('id')
            ->get();

        livewire(ListUserPosts::class, [
            'parentRecord' => $parentRecord,
        ])
            ->sortTable('title')
            ->assertCanSeeTableRecords($sortedAsc, inOrder: true)
            ->sortTable('title', 'desc')
            ->assertCanSeeTableRecords($sortedDesc, inOrder: true);
    });

    it('only lists records belonging to parent', function (): void {
        $parentRecord = User::factory()->create();
        $otherParentRecord = User::factory()->create();

        $postsForParent = Post::factory()->count(5)->create([
            'author_id' => $parentRecord->getKey(),
        ]);
        $postsForOtherParent = Post::factory()->count(5)->create([
            'author_id' => $otherParentRecord->getKey(),
        ]);

        livewire(ListUserPosts::class, [
            'parentRecord' => $parentRecord,
        ])
            ->assertCanSeeTableRecords($postsForParent)
            ->assertCanNotSeeTableRecords($postsForOtherParent);
    });

});

describe('non-soft-deletable nested resource', function (): void {
    it('does not require parent record `view` or `update` access', function (): void {
        Gate::policy(Company::class, NestedCompanyPolicy::class);
        Gate::policy(Team::class, NestedTeamPolicy::class);

        app()->instance('can-view-any-nested-company', true);

        $parentRecord = Company::factory()->create();
        $team = Team::factory()->create([
            'company_id' => $parentRecord->getKey(),
        ]);

        $this->get(CompanyTeamResource::getUrl('edit', [
            'company' => $parentRecord,
            'record' => $team,
        ]))->assertSuccessful();
    });

    it('re-authorizes the parent resource on Livewire requests', function (): void {
        Gate::policy(Company::class, NestedCompanyPolicy::class);
        Gate::policy(Team::class, NestedTeamPolicy::class);

        app()->instance('can-view-any-nested-company', true);

        $parentRecord = Company::factory()->create();
        $team = Team::factory()->create([
            'company_id' => $parentRecord->getKey(),
        ]);

        $component = livewire(EditCompanyTeam::class, [
            'parentRecord' => $parentRecord,
            'record' => $team->getKey(),
        ])->assertSuccessful();

        app()->instance('can-view-any-nested-company', false);

        $component
            ->set('data.name', 'New name')
            ->assertForbidden();
    });

    it('can render list page for non-soft-deletable nested resource', function (): void {
        $parentRecord = Company::factory()->create();

        $this->get(CompanyTeamResource::getUrl('index', [
            'company' => $parentRecord,
        ]))->assertSuccessful();
    });

    it('can list records for non-soft-deletable nested resource', function (): void {
        $parentRecord = Company::factory()->create();
        $teams = Team::factory()->count(10)->create([
            'company_id' => $parentRecord->getKey(),
        ]);

        livewire(ListCompanyTeams::class, [
            'parentRecord' => $parentRecord,
        ])
            ->assertCanSeeTableRecords($teams);
    });

    it('can render create page for non-soft-deletable nested resource', function (): void {
        $parentRecord = Company::factory()->create();

        $this->get(CompanyTeamResource::getUrl('create', [
            'company' => $parentRecord,
        ]))->assertSuccessful();
    });

    it('can create for non-soft-deletable nested resource', function (): void {
        $parentRecord = Company::factory()->create();
        $newData = Team::factory()->make();

        livewire(CreateCompanyTeam::class, [
            'parentRecord' => $parentRecord,
        ])
            ->fillForm([
                'name' => $newData->name,
                'description' => $newData->description,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(Team::class, [
            'company_id' => $parentRecord->getKey(),
            'name' => $newData->name,
            'description' => $newData->description,
        ]);
    });

    it('can validate input on create for non-soft-deletable nested resource', function (): void {
        $parentRecord = Company::factory()->create();

        livewire(CreateCompanyTeam::class, [
            'parentRecord' => $parentRecord,
        ])
            ->fillForm([
                'name' => null,
            ])
            ->call('create')
            ->assertHasFormErrors(['name' => 'required']);
    });

    it('can render view page for non-soft-deletable nested resource', function (): void {
        $parentRecord = Company::factory()->create();
        $team = Team::factory()->create([
            'company_id' => $parentRecord->getKey(),
        ]);

        $this->get(CompanyTeamResource::getUrl('view', [
            'company' => $parentRecord,
            'record' => $team,
        ]))->assertSuccessful();
    });

    it('can retrieve data on view page for non-soft-deletable nested resource', function (): void {
        $parentRecord = Company::factory()->create();
        $team = Team::factory()->create([
            'company_id' => $parentRecord->getKey(),
        ]);

        livewire(ViewCompanyTeam::class, [
            'parentRecord' => $parentRecord,
            'record' => $team->getKey(),
        ])
            ->assertSchemaStateSet([
                'name' => $team->name,
                'description' => $team->description,
            ]);
    });

    it('can render edit page for non-soft-deletable nested resource', function (): void {
        $parentRecord = Company::factory()->create();
        $team = Team::factory()->create([
            'company_id' => $parentRecord->getKey(),
        ]);

        $this->get(CompanyTeamResource::getUrl('edit', [
            'company' => $parentRecord,
            'record' => $team,
        ]))->assertSuccessful();
    });

    it('can retrieve data on edit page for non-soft-deletable nested resource', function (): void {
        $parentRecord = Company::factory()->create();
        $team = Team::factory()->create([
            'company_id' => $parentRecord->getKey(),
        ]);

        livewire(EditCompanyTeam::class, [
            'parentRecord' => $parentRecord,
            'record' => $team->getKey(),
        ])
            ->assertSchemaStateSet([
                'name' => $team->name,
                'description' => $team->description,
            ]);
    });

    it('can save for non-soft-deletable nested resource', function (): void {
        $parentRecord = Company::factory()->create();
        $team = Team::factory()->create([
            'company_id' => $parentRecord->getKey(),
        ]);
        $newData = Team::factory()->make();

        livewire(EditCompanyTeam::class, [
            'parentRecord' => $parentRecord,
            'record' => $team->getKey(),
        ])
            ->fillForm([
                'name' => $newData->name,
                'description' => $newData->description,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($team->refresh())
            ->name->toBe($newData->name)
            ->description->toBe($newData->description);
    });

    it('can validate input on edit for non-soft-deletable nested resource', function (): void {
        $parentRecord = Company::factory()->create();
        $team = Team::factory()->create([
            'company_id' => $parentRecord->getKey(),
        ]);

        livewire(EditCompanyTeam::class, [
            'parentRecord' => $parentRecord,
            'record' => $team->getKey(),
        ])
            ->fillForm([
                'name' => null,
            ])
            ->call('save')
            ->assertHasFormErrors(['name' => 'required']);
    });

    it('can delete from edit page for non-soft-deletable nested resource', function (): void {
        $parentRecord = Company::factory()->create();
        $team = Team::factory()->create([
            'company_id' => $parentRecord->getKey(),
        ]);

        livewire(EditCompanyTeam::class, [
            'parentRecord' => $parentRecord,
            'record' => $team->getKey(),
        ])
            ->callAction(DeleteAction::class);

        assertDatabaseMissing(Team::class, [
            'id' => $team->getKey(),
        ]);
    });

    it('can delete from table for non-soft-deletable nested resource', function (): void {
        $parentRecord = Company::factory()->create();
        $team = Team::factory()->create([
            'company_id' => $parentRecord->getKey(),
        ]);

        livewire(ListCompanyTeams::class, [
            'parentRecord' => $parentRecord,
        ])
            ->callTableAction(DeleteAction::class, $team);

        assertDatabaseMissing(Team::class, [
            'id' => $team->getKey(),
        ]);
    });

    it('can bulk delete from table for non-soft-deletable nested resource', function (): void {
        $parentRecord = Company::factory()->create();
        $teams = Team::factory()->count(10)->create([
            'company_id' => $parentRecord->getKey(),
        ]);

        livewire(ListCompanyTeams::class, [
            'parentRecord' => $parentRecord,
        ])
            ->callTableBulkAction(DeleteBulkAction::class, $teams);

        foreach ($teams as $team) {
            assertDatabaseMissing(Team::class, [
                'id' => $team->getKey(),
            ]);
        }
    });

    it('can search records for non-soft-deletable nested resource', function (): void {
        $parentRecord = Company::factory()->create();
        $teams = Team::factory()->count(10)->create([
            'company_id' => $parentRecord->getKey(),
        ]);

        $name = $teams->first()->name;

        livewire(ListCompanyTeams::class, [
            'parentRecord' => $parentRecord,
        ])
            ->searchTable($name)
            ->assertCanSeeTableRecords($teams->where('name', $name))
            ->assertCanNotSeeTableRecords($teams->where('name', '!=', $name));
    });

    it('can sort records by name for non-soft-deletable nested resource', function (): void {
        $parentRecord = Company::factory()->create();
        Team::factory()->count(10)
            ->sequence(['name' => 'Walker Ltd'], ['name' => 'Wolff-Greenfelder'])
            ->create([
                'company_id' => $parentRecord->getKey(),
            ]);

        $sortedAsc = Team::query()
            ->where('company_id', $parentRecord->getKey())
            ->orderBy('name')
            ->orderBy('id')
            ->get();
        $sortedDesc = Team::query()
            ->where('company_id', $parentRecord->getKey())
            ->orderByDesc('name')
            ->orderByDesc('id')
            ->get();

        livewire(ListCompanyTeams::class, [
            'parentRecord' => $parentRecord,
        ])
            ->sortTable('name')
            ->assertCanSeeTableRecords($sortedAsc, inOrder: true)
            ->sortTable('name', 'desc')
            ->assertCanSeeTableRecords($sortedDesc, inOrder: true);
    });

    it('only lists records belonging to parent for non-soft-deletable nested resource', function (): void {
        $parentRecord = Company::factory()->create();
        $otherParentRecord = Company::factory()->create();

        $teamsForParent = Team::factory()->count(5)->create([
            'company_id' => $parentRecord->getKey(),
        ]);
        $teamsForOtherParent = Team::factory()->count(5)->create([
            'company_id' => $otherParentRecord->getKey(),
        ]);

        livewire(ListCompanyTeams::class, [
            'parentRecord' => $parentRecord,
        ])
            ->assertCanSeeTableRecords($teamsForParent)
            ->assertCanNotSeeTableRecords($teamsForOtherParent);
    });
});

describe('multi-level nested resource authorization', function (): void {
    beforeEach(function (): void {
        Gate::policy(Company::class, NestedCompanyPolicy::class);
        Gate::policy(Team::class, NestedTeamPolicy::class);
        Gate::policy(User::class, NestedUserPolicy::class);

        app()->instance('can-view-any-nested-company', true);
        app()->instance('can-access-nested-parent-record', true);

        $panel = Filament::getCurrentOrDefaultPanel();

        Route::middleware([...$panel->getMiddleware(), ...$panel->getAuthMiddleware()])
            ->name("filament.{$panel->getId()}.")
            ->prefix($panel->getPath())
            ->group(fn () => NestedTeamUserResource::registerRoutes($panel));
    });

    it('requires outer ancestor resource access', function (): void {
        $company = Company::factory()->create();
        $team = Team::factory()->create([
            'company_id' => $company->getKey(),
        ]);
        $record = User::factory()->create();
        $team->users()->attach($record);

        app()->instance('can-view-any-nested-company', false);

        $this->get(NestedTeamUserResource::getUrl('edit', [
            'company' => $company,
            'team' => $team,
            'record' => $record,
        ]))->assertForbidden();
    });

    it('re-authorizes outer ancestor resources on Livewire requests', function (): void {
        $company = Company::factory()->create();
        $team = Team::factory()->create([
            'company_id' => $company->getKey(),
        ]);
        $record = User::factory()->create();
        $team->users()->attach($record);

        $component = livewire(EditNestedTeamUser::class, [
            'parentRecord' => $team,
            'record' => $record->getKey(),
        ])->assertSuccessful();

        app()->instance('can-view-any-nested-company', false);

        $component
            ->set('data.name', 'New name')
            ->assertForbidden();
    });

    it('re-runs custom parent record authorization on Livewire requests', function (): void {
        $company = Company::factory()->create();
        $team = Team::factory()->create([
            'company_id' => $company->getKey(),
        ]);
        $record = User::factory()->create();
        $team->users()->attach($record);

        $component = livewire(EditNestedTeamUser::class, [
            'parentRecord' => $team,
            'record' => $record->getKey(),
        ])->assertSuccessful();

        app()->instance('can-access-nested-parent-record', false);

        $component
            ->set('data.name', 'New name')
            ->assertForbidden();
    });
});

describe('parent relationship membership', function (): void {
    beforeEach(function (): void {
        User::resolveRelationUsing('nestedPosts', static fn (User $user): HasMany => $user->posts()
            ->join('users as reviewers', 'reviewers.id', '=', 'posts.sort')
            ->where('reviewers.name', 'Allowed reviewer')
            ->withGlobalScope('nested-post-membership', static fn (Builder $query): Builder => $query
                ->where(fn (Builder $query): Builder => $query->where('is_published', true)->orWhere('rating', '>=', 4))
                ->orderByRaw('posts.id + ?', [1]))
            ->orderByRaw('posts.id + ?', [0]));

        Team::resolveRelationUsing('nestedUsers', static fn (Team $team): BelongsToMany => $team->users()
            ->wherePivotIn('role', ['member', 'owner'])
            ->orderByRaw('users.id + ?', [0]));

        $panel = Filament::getCurrentOrDefaultPanel();

        Route::middleware([...$panel->getMiddleware(), ...$panel->getAuthMiddleware()])
            ->name("filament.{$panel->getId()}.")
            ->prefix($panel->getPath())
            ->group(function () use ($panel): void {
                ConstrainedUserPostResource::registerRoutes($panel);
                ConstrainedTeamUserResource::registerRoutes($panel);
                DeepNestedPostResource::registerRoutes($panel);
            });
    });

    it('uses the full one-to-many relationship while retaining resource scopes', function (): void {
        $author = User::factory()->create();
        $reviewer = User::factory()->create(['name' => 'Allowed reviewer']);
        $otherReviewer = User::factory()->create(['name' => 'Excluded reviewer']);
        $included = Post::factory()->create(['author_id' => $author->id, 'sort' => $reviewer->id, 'is_published' => true, 'rating' => 1]);
        $alternative = Post::factory()->create(['author_id' => $author->id, 'sort' => $reviewer->id, 'is_published' => false, 'rating' => 4]);
        $excluded = collect([
            Post::factory()->create(['author_id' => $author->id, 'sort' => $reviewer->id, 'is_published' => false, 'rating' => 3]),
            Post::factory()->create(['author_id' => $author->id, 'sort' => $otherReviewer->id, 'is_published' => true]),
            Post::factory()->create(['sort' => $reviewer->id, 'is_published' => false, 'rating' => 4]),
            Post::factory()->create(['author_id' => $author->id, 'sort' => $reviewer->id, 'title' => 'resource-excluded']),
        ]);

        livewire(ListConstrainedUserPosts::class, ['parentRecord' => $author])
            ->assertCanSeeTableRecords([$included, $alternative])
            ->assertCanNotSeeTableRecords($excluded);

        foreach ([$included, $alternative] as $post) {
            livewire(EditConstrainedUserPost::class, ['parentRecord' => $author, 'record' => $post->id])
                ->assertSuccessful();
        }

        livewire(EditConstrainedUserPost::class, ['parentRecord' => $author, 'record' => $alternative->id])
            ->fillForm(['title' => 'Allowed relationship update'])
            ->call('save')
            ->assertHasNoFormErrors();
        expect($alternative->refresh()->title)->toBe('Allowed relationship update');

        foreach ($excluded as $post) {
            $this->get(ConstrainedUserPostResource::getUrl('edit', ['author' => $author, 'record' => $post]))->assertNotFound();
        }

        $component = livewire(EditConstrainedUserPost::class, ['parentRecord' => $author, 'record' => $included->id])
            ->fillForm(['title' => 'must-not-save-archived-post']);
        $included->update(['is_published' => false, 'rating' => 3]);

        $component->call('save')->assertNotFound();
        assertDatabaseMissing(Post::class, ['title' => 'must-not-save-archived-post']);
    });

    it('honors the parent relationship soft-delete choice', function (bool $withTrashed): void {
        User::resolveRelationUsing('nestedPosts', static fn (User $user): HasMany => $withTrashed ? $user->posts()->withTrashed() : $user->posts());
        $author = User::factory()->create();
        $post = Post::factory()->create(['author_id' => $author->id]);
        $post->delete();

        $response = $this->get(ConstrainedUserPostResource::getUrl('edit', ['author' => $author, 'record' => $post]));

        $withTrashed ? $response->assertSuccessful() : $response->assertNotFound();
    })->with([false, true]);

    it('uses pivot membership for listing, resolution, and later saves', function (): void {
        $company = Company::factory()->create();
        $team = Team::factory()->create(['company_id' => $company->id]);
        $included = User::factory()->create();
        $excluded = User::factory()->create();
        $unrelated = User::factory()->create();
        $team->users()->attach($included, ['role' => 'member']);
        $team->users()->attach($excluded, ['role' => 'withdrawn']);

        livewire(ListConstrainedTeamUsers::class, ['parentRecord' => $team])
            ->assertCanSeeTableRecords([$included])
            ->assertCanNotSeeTableRecords([$excluded, $unrelated]);

        foreach ([$excluded, $unrelated] as $user) {
            $this->get(ConstrainedTeamUserResource::getUrl('edit', ['company' => $team->company, 'team' => $team, 'record' => $user]))->assertNotFound();
        }

        $component = livewire(EditConstrainedTeamUser::class, ['parentRecord' => $team, 'record' => $included->id])
            ->fillForm(['name' => 'must-not-save-withdrawn-member']);
        $team->users()->updateExistingPivot($included, ['role' => 'withdrawn']);

        $component->call('save')->assertNotFound();
        expect($included->refresh()->name)->not->toBe('must-not-save-withdrawn-member');
    });

    it('isolates relationship `orWhere()` clauses from the requested record key', function (): void {
        User::resolveRelationUsing('nestedPosts', static fn (User $user): HasMany => $user->posts()
            ->where('rating', 5)
            ->orWhere('title', 'Explicitly included'));
        $author = User::factory()->create();
        $included = Post::factory()->create(['author_id' => $author->id, 'rating' => 5]);
        $alternative = Post::factory()->create(['title' => 'Explicitly included']);
        $excluded = Post::factory()->create(['author_id' => $author->id, 'rating' => 1]);

        livewire(ListConstrainedUserPosts::class, ['parentRecord' => $author])
            ->assertCanSeeTableRecords([$included, $alternative])
            ->assertCanNotSeeTableRecords([$excluded]);
        livewire(EditConstrainedUserPost::class, ['parentRecord' => $author, 'record' => $alternative->id])->assertSuccessful();
        $this->get(ConstrainedUserPostResource::getUrl('edit', ['author' => $author, 'record' => $excluded]))->assertNotFound();
    });

    it('preserves the real inverse collection during record policy authorization', function (): void {
        Gate::policy(User::class, NestedCollectionUserPolicy::class);
        $team = Team::factory()->create();
        $otherTeam = Team::factory()->create();
        $unrelatedTeam = Team::factory()->create();
        $user = User::factory()->create();
        $user->teams()->attach([$team->id, $otherTeam->id]);
        $team->users()->updateExistingPivot($user, ['role' => 'member']);
        app()->instance('nested-expected-team-ids', [$team->id, $otherTeam->id]);
        app()->instance('nested-unrelated-team-id', $unrelatedTeam->id);

        $component = livewire(EditConstrainedTeamUser::class, ['parentRecord' => $team, 'record' => $user->id])
            ->assertSuccessful();

        expect($component->instance()->getRecord()->teams)->toBeInstanceOf(Collection::class)
            ->and($component->instance()->getRecord()->teams->modelKeys())->toEqualCanonicalizing([$team->id, $otherTeam->id]);

        $component->fillForm(['name' => 'Allowed collection policy update'])->call('save')->assertHasNoFormErrors();
        expect($user->refresh()->name)->toBe('Allowed collection policy update');
    });

    it('keeps route-selected ancestors for breadcrumbs across to-one and to-many levels', function (): void {
        Gate::policy(User::class, NestedCollectionUserPolicy::class);
        $company = Company::factory()->create(['name' => 'Selected company']);
        $team = Team::factory()->create(['company_id' => $company->id, 'name' => 'Selected team']);
        $otherTeam = Team::factory()->create(['name' => 'Other team']);
        $unrelatedTeam = Team::factory()->create();
        $author = User::factory()->create(['name' => 'Nested author']);
        $author->teams()->attach([$otherTeam->id, $team->id]);
        $team->users()->updateExistingPivot($author, ['role' => 'member']);
        $post = Post::factory()->create(['author_id' => $author->id]);
        app()->instance('nested-expected-team-ids', [$team->id, $otherTeam->id]);
        app()->instance('nested-unrelated-team-id', $unrelatedTeam->id);
        app()->instance('nested-post-route-parameters', ['company' => $company->id, 'team' => $team->id, 'author' => $author->id]);

        $this->get(DeepNestedPostResource::getUrl('edit', ['company' => $company, 'team' => $team, 'author' => $author, 'record' => $post]))
            ->assertSuccessful()
            ->assertSee(CompanyResource::getUrl('view', ['record' => $company]), escape: false)
            ->assertSee(CompanyTeamResource::getUrl('view', ['company' => $company, 'record' => $team]), escape: false)
            ->assertSee(ConstrainedTeamUserResource::getUrl('edit', ['company' => $company, 'team' => $team, 'record' => $author]), escape: false);

        $component = livewire(EditDeepNestedPost::class, ['record' => $post->id])->assertSuccessful();
        $page = $component->instance();

        expect($page->getParentRecord()->teams)->toBeInstanceOf(Collection::class)
            ->and($page->getParentRecord()->teams->modelKeys())->toEqualCanonicalizing([$team->id, $otherTeam->id])
            ->and($page->getRecord()->author->is($author))->toBeTrue();

        $component->set('data.title', 'Still nested')->assertSuccessful();
        expect($component->instance()->getParentRecord()->teams)->toBeInstanceOf(Collection::class);

        $team->users()->updateExistingPivot($author, ['role' => 'withdrawn']);
        $component->call('save')->assertNotFound();
        assertDatabaseMissing(Post::class, ['title' => 'Still nested']);
    });
});

class NestedCollectionUserPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function update(User $user, User $record): bool
    {
        $parents = $record->teams;

        return ($parents instanceof Collection)
            && ($parents->pluck('id')->sort()->values()->all() === collect(app('nested-expected-team-ids'))->sort()->values()->all())
            && ($parents->where('id', app('nested-unrelated-team-id'))->count() === 0)
            && ($parents->firstWhere('id', app('nested-unrelated-team-id')) === null);
    }
}

class ConstrainedUserPostResource extends UserPostResource
{
    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        return UserResource::asParent(static::class)->relationship('nestedPosts')->inverseRelationship('author');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withTrashed()->where('title', '!=', 'resource-excluded');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListConstrainedUserPosts::route('/'),
            'edit' => EditConstrainedUserPost::route('/{record}/edit'),
        ];
    }
}

class ListConstrainedUserPosts extends ListRecords
{
    protected static string $resource = ConstrainedUserPostResource::class;
}

class EditConstrainedUserPost extends EditRecord
{
    protected static string $resource = ConstrainedUserPostResource::class;
}

class ConstrainedTeamUserResource extends UserResource
{
    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        return CompanyTeamResource::asParent(static::class)->relationship('nestedUsers')->inverseRelationship('teams');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListConstrainedTeamUsers::route('/'),
            'edit' => EditConstrainedTeamUser::route('/{record}/edit'),
        ];
    }
}

class ListConstrainedTeamUsers extends ListRecords
{
    protected static string $resource = ConstrainedTeamUserResource::class;
}

class EditConstrainedTeamUser extends EditRecord
{
    protected static string $resource = ConstrainedTeamUserResource::class;
}

class DeepNestedPostResource extends UserPostResource
{
    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        return ConstrainedTeamUserResource::asParent(static::class)->relationship('posts')->inverseRelationship('author');
    }

    public static function getPages(): array
    {
        return ['edit' => EditDeepNestedPost::route('/{record}/edit')];
    }
}

class EditDeepNestedPost extends EditRecord
{
    protected static string $resource = DeepNestedPostResource::class;

    protected function getParentRecordRouteParameters(): array
    {
        return app('nested-post-route-parameters');
    }
}

class NestedCompanyPolicy
{
    public function viewAny(User $user): bool
    {
        return app('can-view-any-nested-company');
    }

    public function view(User $user, Company $record): bool
    {
        return false;
    }

    public function update(User $user, Company $record): bool
    {
        return false;
    }
}

class NestedTeamPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function update(User $user, Team $record): bool
    {
        return true;
    }
}

class NestedUserPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function update(User $user, User $record): bool
    {
        return true;
    }
}

class NestedTeamUserResource extends Resource
{
    protected static ?string $model = User::class;

    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        return CompanyTeamResource::asParent(static::class)
            ->relationship('users')
            ->inverseRelationship('teams');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('email')->required(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'edit' => EditNestedTeamUser::route('/{record}/edit'),
        ];
    }
}

class EditNestedTeamUser extends EditRecord
{
    protected static string $resource = NestedTeamUserResource::class;

    protected function authorizeParentRecordAccess(): void
    {
        parent::authorizeParentRecordAccess();

        abort_unless(app('can-access-nested-parent-record'), 403);
    }
}

class NamedCompanyTeamResource extends CompanyTeamResource
{
    public static function resolveRecordRouteBinding(int | string $key, ?Closure $modifyQuery = null): ?Model
    {
        if (app()->bound('nested-parent-binding-replacement')) {
            return app('nested-parent-binding-replacement');
        }

        return parent::resolveRecordRouteBinding($key, function (Builder $query) use ($modifyQuery): Builder {
            $query = $modifyQuery ? $modifyQuery($query) : $query;
            app()->instance('nested-parent-used-write-connection', $query->getQuery()->useWritePdo);

            return app()->bound('nested-parent-binding-denied') ? $query->whereRaw('1 = 0') : $query;
        });
    }

    public static function getRecordRouteKeyName(): ?string
    {
        return 'name';
    }
}

class EditUserPostWithCustomParentResolver extends EditUserPost
{
    public static ?User $replacementParentRecord = null;

    /** @var array<string, int | string> */
    public static array $testParentRecordRouteParameters = [];

    protected function getParentRecordRouteParameters(): array
    {
        return static::$testParentRecordRouteParameters;
    }

    protected function resolveParentRecord(array $parameters): User
    {
        if (static::$replacementParentRecord) {
            return static::$replacementParentRecord;
        }

        return User::query()->findOrFail($parameters['author']);
    }
}

class DeepNestedUserResource extends UserResource
{
    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        return NamedCompanyTeamResource::asParent(static::class)
            ->relationship('users')
            ->inverseRelationship('teams');
    }
}

class CreateDeepNestedUser extends CreateRecord
{
    /** @var array<string, int | string> */
    public static array $testParentRecordRouteParameters = [];

    protected static string $resource = DeepNestedUserResource::class;

    protected function getParentRecordRouteParameters(): array
    {
        return static::$testParentRecordRouteParameters;
    }
}

class EditDeepNestedUser extends EditRecord
{
    /** @var array<string, int | string> */
    public static array $testParentRecordRouteParameters = [];

    protected static string $resource = DeepNestedUserResource::class;

    protected function getParentRecordRouteParameters(): array
    {
        return static::$testParentRecordRouteParameters;
    }
}

class ListDeepNestedUsers extends ListRecords
{
    public static bool $hasBooted = false;

    public static bool $hasMounted = false;

    protected static string $resource = DeepNestedUserResource::class;

    public function boot(): void
    {
        static::$hasBooted = true;
    }

    public function mount(): void
    {
        static::$hasMounted = true;

        parent::mount();
    }
}

class DeepNestedPageTableWidget extends Widget
{
    use InteractsWithPageTable;

    public int $pageTableRecordsCount = 0;

    public ?Model $parentRecordOverride = null;

    protected string $view = 'pages.settings';

    protected function getTablePageMountParameters(): array
    {
        return $this->parentRecordOverride ? ['parentRecord' => $this->parentRecordOverride] : [];
    }

    public function getResolvedTablePage(): ListDeepNestedUsers
    {
        return $this->getTablePageInstance();
    }

    public function loadPageTable(): void
    {
        $this->pageTableRecordsCount = $this->getPageTableQuery()->count();
    }

    protected function getTablePage(): string
    {
        return ListDeepNestedUsers::class;
    }
}

class ListTicketDepartments extends ListRecords
{
    protected static string $resource = TicketDepartmentResource::class;
}
