<?php

use Filament\Facades\Filament;
use Filament\Pages\Page as PanelPage;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Resources\Posts\Pages\CreatePost;
use Filament\Tests\Fixtures\Resources\Posts\Pages\EditPost;
use Filament\Tests\Fixtures\Resources\Posts\PostResource;
use Filament\Tests\Panels\Resources\TestCase;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Gate;

use function Filament\Tests\livewire;

uses(TestCase::class);

class TenantScopeHydrationPostPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Post $post): bool
    {
        return true;
    }

    public function update(User $user, Post $post): bool
    {
        app()->instance('tenant-scope-hydration-policy-reached', true);

        return app('tenant-scope-hydration-policy-allowed');
    }
}

beforeEach(function (): void {
    Gate::policy(Post::class, TenantScopeHydrationPostPolicy::class);
    Gate::policy(ModelRouteBindingHydrationPost::class, TenantScopeHydrationPostPolicy::class);
    app()->instance('tenant-scope-hydration-policy-allowed', true);
    app()->instance('tenant-scope-hydration-policy-reached', false);
    app()->instance('tenant-scope-hydration-model-route-binding-allowed', true);
    app()->instance('tenant-scope-hydration-page-resolver-allowed', true);
    app()->instance('tenant-scope-hydration-resource-query-allowed', true);
    app()->instance('tenant-scope-hydration-resource-route-binding-allowed', true);

    $panel = Filament::getPanel('tenancy');
    Filament::setCurrentPanel($panel);
    Filament::setTenant(null);
    PostResource::registerTenancyModelGlobalScope($panel);
});

function tenantScopeHydrationFixture(): array
{
    $tenantA = Team::factory()->create();
    $tenantB = Team::factory()->create();
    $author = User::factory()->create(['team_id' => $tenantA->getKey()]);
    $post = Post::factory()->create(['author_id' => $author->getKey()]);

    Filament::setTenant($tenantA);

    return [$tenantA, $tenantB, $author, $post];
}

it('rejects a stale resource page snapshot after the record moves to another tenant', function (): void {
    [, $tenantB, $author, $post] = tenantScopeHydrationFixture();
    $originalTitle = $post->title;

    $page = livewire(EditPost::class, ['record' => $post->getKey()])
        ->assertSuccessful();

    $author->update(['team_id' => $tenantB->getKey()]);

    expect(PostResource::getEloquentQuery()->find($post->getKey()))->toBeNull();

    $page
        ->fillForm(['title' => 'must-not-cross-tenant-boundary'])
        ->assertNotFound();

    expect(Post::withoutGlobalScopes()->findOrFail($post->getKey())->title)->toBe($originalTitle);
});

it('allows a resource page snapshot while the record remains in the tenant', function (): void {
    [, , , $post] = tenantScopeHydrationFixture();

    livewire(EditPost::class, ['record' => $post->getKey()])
        ->fillForm(['title' => 'updated in tenant'])
        ->call('save')
        ->assertSuccessful()
        ->assertHasNoFormErrors();

    expect($post->refresh()->title)->toBe('updated in tenant');
});

it('still enforces the policy after scoped restoration', function (): void {
    [, , , $post] = tenantScopeHydrationFixture();
    $originalTitle = $post->title;
    $page = livewire(EditPost::class, ['record' => $post->getKey()]);

    app()->instance('tenant-scope-hydration-policy-allowed', false);

    $page
        ->fillForm(['title' => 'must-not-cross-policy-boundary'])
        ->assertNotFound();

    expect($post->refresh()->title)->toBe($originalTitle);
});

it('rejects a scope-excluded record before it reaches the policy', function (): void {
    [, $tenantB, $author, $post] = tenantScopeHydrationFixture();
    $page = livewire(EditPost::class, ['record' => $post->getKey()])
        ->assertSuccessful();

    app()->instance('tenant-scope-hydration-policy-reached', false);
    $author->update(['team_id' => $tenantB->getKey()]);

    $page
        ->set('data.title', 'must-not-reach-policy')
        ->assertNotFound();

    expect(app('tenant-scope-hydration-policy-reached'))->toBeFalse();
});

it('rejects a scope-excluded record before it reaches `boot()`', function (): void {
    [, $tenantB, $author, $post] = tenantScopeHydrationFixture();
    $page = livewire(EditPostWithBootHook::class, ['record' => $post->getKey()])
        ->assertSuccessful();

    EditPostWithBootHook::$hasBootedWithRecord = false;
    $author->update(['team_id' => $tenantB->getKey()]);

    $page
        ->set('data.title', 'must-not-reach-boot')
        ->assertNotFound();

    expect(EditPostWithBootHook::$hasBootedWithRecord)->toBeFalse();
});

it('rejects a scope-excluded custom resource page record before it reaches `canAccess()`', function (): void {
    [, , , $post] = tenantScopeHydrationFixture();
    $page = livewire(CustomScopedHydrationPostPage::class, ['record' => $post->getKey()])
        ->assertSuccessful();

    app()->instance('tenant-scope-hydration-custom-page-access-reached', false);
    app()->instance('tenant-scope-hydration-resource-query-allowed', false);

    $page
        ->call('$refresh')
        ->assertNotFound();

    expect(app('tenant-scope-hydration-custom-page-access-reached'))->toBeFalse();
});

it('restores a resource page record through the resource query', function (): void {
    [, , , $post] = tenantScopeHydrationFixture();
    app()->instance('tenant-scope-hydration-resource-query-allowed', true);
    $page = livewire(EditScopedHydrationPost::class, ['record' => $post->getKey()])
        ->assertSuccessful();

    app()->instance('tenant-scope-hydration-resource-query-allowed', false);

    $page
        ->set('data.title', 'must-not-cross-resource-query-boundary')
        ->assertNotFound();
});

it('preserves record route binding query modifications during restoration', function (): void {
    [, , , $post] = tenantScopeHydrationFixture();
    app()->instance('tenant-scope-hydration-resource-query-allowed', true);
    $post->delete();

    livewire(EditScopedHydrationPost::class, ['record' => $post->getKey()])
        ->assertSuccessful()
        ->set('data.title', 'soft-deleted record')
        ->assertSuccessful();
});

it('restores a resource page record using the write connection', function (): void {
    [, , , $post] = tenantScopeHydrationFixture();
    $page = livewire(EditScopedHydrationPost::class, ['record' => $post->getKey()])
        ->assertSuccessful();

    app()->instance('tenant-scope-hydration-used-write-connection', false);

    $page
        ->set('data.title', 'still in tenant')
        ->assertSuccessful();

    expect(app('tenant-scope-hydration-used-write-connection'))->toBeTrue();
});

it('restores a resource page record through the resource route binding resolver', function (): void {
    [, , , $post] = tenantScopeHydrationFixture();
    app()->instance('tenant-scope-hydration-resource-query-allowed', true);
    $page = livewire(EditScopedHydrationPost::class, ['record' => $post->getKey()])
        ->assertSuccessful();

    app()->instance('tenant-scope-hydration-resource-route-binding-allowed', false);

    $page
        ->set('data.title', 'must-not-cross-resource-route-binding-boundary')
        ->assertNotFound();
});

it('restores a resource page record through the model route binding resolver', function (): void {
    [, , , $post] = tenantScopeHydrationFixture();
    app()->instance('tenant-scope-hydration-resource-query-allowed', true);
    $page = livewire(EditModelRouteBindingHydrationPost::class, ['record' => $post->getKey()])
        ->assertSuccessful();

    app()->instance('tenant-scope-hydration-model-route-binding-allowed', false);

    $page
        ->set('data.title', 'must-not-cross-model-route-binding-boundary')
        ->assertNotFound();
});

it('restores a resource page record through the page record resolver', function (): void {
    [, , , $post] = tenantScopeHydrationFixture();
    $page = livewire(EditPageResolverHydrationPost::class, ['record' => $post->getKey()])
        ->assertSuccessful();

    app()->instance('tenant-scope-hydration-page-resolver-allowed', false);

    $page
        ->set('data.title', 'must-not-cross-page-resolver-boundary')
        ->assertNotFound();
});

it('rejects a stale create page snapshot after its created record moves to another tenant', function (): void {
    [, $tenantB, $author, $post] = tenantScopeHydrationFixture();
    $component = livewire(CreatePost::class)
        ->fillForm([
            'author_id' => $author->getKey(),
            'content' => $post->content,
            'tags' => $post->tags,
            'title' => 'created record',
            'rating' => $post->rating,
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertRedirect();

    expect($component->instance()->getRecord())->toBeInstanceOf(Post::class);

    $author->update(['team_id' => $tenantB->getKey()]);

    $component
        ->set('data.title', 'must-not-cross-tenant-boundary')
        ->assertNotFound();
});

it('preserves Livewire restoration for user-defined model properties', function (): void {
    [, $tenantB, $author, $post] = tenantScopeHydrationFixture();
    $otherAuthor = User::factory()->create(['team_id' => $author->team_id]);
    $otherPost = Post::factory()->create(['author_id' => $otherAuthor->getKey()]);

    $page = livewire(EditPostWithUserDefinedModelProperty::class, [
        'record' => $post->getKey(),
        'otherPost' => $otherPost,
    ])->assertSuccessful();

    $otherAuthor->update(['team_id' => $tenantB->getKey()]);

    $page
        ->set('data.title', 'updated in tenant')
        ->assertSuccessful();

    expect($page->instance()->otherPost->is($otherPost))->toBeTrue();
});

it('preserves in-memory changes after restoring a page with `InteractsWithRecord`', function (): void {
    [, , , $post] = tenantScopeHydrationFixture();
    $originalTitle = $post->title;
    $component = livewire(EditPostWithRecordMutation::class, ['record' => $post->getKey()]);
    EditPostWithRecordMutation::$recordResolutions = 0;

    $component
        ->call('captureRecordTitle')
        ->assertSuccessful()
        ->assertSet('capturedRecordTitle', 'unsaved title from boot');

    expect(EditPostWithRecordMutation::$recordResolutions)->toBe(1)
        ->and($post->refresh()->title)->toBe($originalTitle);
});

it('revalidates a custom resource page record replaced after hydration before an action', function (bool $isReplacementAllowed): void {
    [, $otherTenant, $author, $post] = tenantScopeHydrationFixture();
    $replacementAuthor = User::factory()->create([
        'team_id' => $isReplacementAllowed ? $author->team_id : $otherTenant->getKey(),
    ]);
    $replacementPost = Post::factory()->create(['author_id' => $replacementAuthor->getKey()]);
    $originalTitle = $post->title;
    $replacementTitle = $replacementPost->title;
    $component = livewire(CustomPostPageWithoutRecordTrait::class, ['record' => $post->getKey()])
        ->assertSuccessful();

    $component->update(
        calls: [['method' => 'saveRecordTitle', 'params' => []]],
        updates: ['selectedRecordKey' => $replacementPost->getKey()],
    );

    if ($isReplacementAllowed) {
        $component->assertSuccessful();
    } else {
        $component->assertNotFound();
    }

    expect($post->refresh()->title)->toBe($originalTitle)
        ->and($replacementPost->refresh()->title)->toBe($isReplacementAllowed ? 'updated replacement' : $replacementTitle);
})->with([true, false]);

it('does not intercept `resolveScopedModelProperties()` on an unrelated panel page', function (): void {
    livewire(UnrelatedModelRestorationPage::class)
        ->call('$refresh')
        ->assertSuccessful()
        ->assertSet('hasResolvedModelProperties', false)
        ->call('resolveScopedModelProperties')
        ->assertSuccessful()
        ->assertSet('hasResolvedModelProperties', true);
});

class UnrelatedModelRestorationPage extends PanelPage
{
    protected string $view = 'pages.settings';

    public bool $hasResolvedModelProperties = false;

    public function resolveScopedModelProperties(): void
    {
        $this->hasResolvedModelProperties = true;
    }
}

class EditPostWithUserDefinedModelProperty extends EditPost
{
    public ?Post $otherPost = null;
}

class EditPostWithRecordMutation extends EditPost
{
    public static int $recordResolutions = 0;

    public ?string $capturedRecordTitle = null;

    public function boot(): void
    {
        if (isset($this->record) && ($this->record instanceof Model)) {
            $this->record->title = 'unsaved title from boot';
        }
    }

    public function captureRecordTitle(): void
    {
        $this->capturedRecordTitle = $this->record->title;
    }

    protected function resolveRecord(int | string $key): Model
    {
        static::$recordResolutions++;

        return parent::resolveRecord($key);
    }
}

class CustomPostPageWithoutRecordTrait extends Page
{
    protected static string $resource = PostResource::class;

    protected string $view = 'pages.settings';

    public Model | int | string | null $record = null;

    public int $selectedRecordKey = 0;

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    public function updatedSelectedRecordKey(): void
    {
        $this->record = Post::withoutGlobalScopes()->findOrFail($this->selectedRecordKey);
    }

    public function saveRecordTitle(): void
    {
        $this->record->update(['title' => 'updated replacement']);
    }
}

class EditPostWithBootHook extends EditPost
{
    public static bool $hasBootedWithRecord = false;

    public function boot(): void
    {
        static::$hasBootedWithRecord = isset($this->record) && ($this->record instanceof Post);
    }
}

class CustomScopedHydrationPostPage extends Page
{
    use InteractsWithRecord;

    protected static string $resource = ScopedHydrationPostResource::class;

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    /** @param array<string, mixed> $parameters */
    public static function canAccess(array $parameters = []): bool
    {
        app()->instance('tenant-scope-hydration-custom-page-access-reached', true);

        return true;
    }
}

class ScopedHydrationPostResource extends PostResource
{
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->when(
                ! app('tenant-scope-hydration-resource-query-allowed'),
                fn (Builder $query): Builder => $query->whereRaw('1 = 0'),
            );
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScope(SoftDeletingScope::class);
    }

    public static function resolveRecordRouteBinding(int | string $key, ?Closure $modifyQuery = null): ?Model
    {
        if (! app('tenant-scope-hydration-resource-route-binding-allowed')) {
            return null;
        }

        if ($modifyQuery) {
            $originalModifyQuery = $modifyQuery;
            $modifyQuery = function (Builder $query) use ($originalModifyQuery): Builder {
                $query = $originalModifyQuery($query) ?? $query;
                app()->instance('tenant-scope-hydration-used-write-connection', $query->getQuery()->useWritePdo);

                return $query;
            };
        }

        return parent::resolveRecordRouteBinding($key, $modifyQuery);
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public static function getUrl(?string $name = null, array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null, bool $shouldGuessMissingParameters = false, ?string $configuration = null): string
    {
        return PostResource::getUrl($name, $parameters, $isAbsolute, $panel, $tenant, $shouldGuessMissingParameters, $configuration);
    }
}

class EditScopedHydrationPost extends EditPost
{
    protected static string $resource = ScopedHydrationPostResource::class;
}

class ModelRouteBindingHydrationPost extends Post
{
    protected $table = 'posts';

    public function resolveRouteBindingQuery($query, $value, $field = null)
    {
        return parent::resolveRouteBindingQuery($query, $value, $field)
            ->when(
                ! app('tenant-scope-hydration-model-route-binding-allowed'),
                fn (Builder $query): Builder => $query->whereRaw('1 = 0'),
            );
    }
}

class ModelRouteBindingHydrationPostResource extends ScopedHydrationPostResource
{
    protected static ?string $model = ModelRouteBindingHydrationPost::class;
}

class EditModelRouteBindingHydrationPost extends EditPost
{
    protected static string $resource = ModelRouteBindingHydrationPostResource::class;
}

class EditPageResolverHydrationPost extends EditPost
{
    protected static string $resource = ScopedHydrationPostResource::class;

    protected function resolveRecord(int | string $key): Model
    {
        abort_unless(app('tenant-scope-hydration-page-resolver-allowed'), 404);

        return parent::resolveRecord($key);
    }
}
