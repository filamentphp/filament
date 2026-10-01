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

    it('scopes a lazy page table widget parent through global scopes', function (): void {
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

            $component
                ->call('loadPageTable')
                ->assertNotFound();

            expect(ListDeepNestedUsers::$hasBooted)->toBeFalse()
                ->and(ListDeepNestedUsers::$hasMounted)->toBeFalse();
        } finally {
            Team::setAllGlobalScopes($globalScopes);
        }
    });

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
            ->orderBy('id')
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

class NamedCompanyTeamResource extends CompanyTeamResource
{
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

    protected string $view = 'pages.settings';

    public function loadPageTable(): void
    {
        $this->pageTableRecordsCount = $this->getPageTableQuery()->count();
    }

    protected function getTablePage(): string
    {
        return ListDeepNestedUsers::class;
    }
}

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
        Team::factory()->count(10)->create([
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
            ->orderBy('id')
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
