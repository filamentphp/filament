<?php

use Filament\Actions\EditAction;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Resources\Tenancy\TenantScopedUsers\TenantScopedUserResource;
use Filament\Tests\Fixtures\Resources\Tickets\Pages\EditTicket;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsRelationManager;
use Filament\Tests\Fixtures\Resources\Tickets\TicketResource;
use Filament\Tests\Panels\Resources\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

use function Filament\Tests\livewire;

uses(TestCase::class);

class RelationManagerTenantScopeHydrationOwnerPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, User $owner): bool
    {
        return true;
    }

    public function update(User $user, User $owner): bool
    {
        return true;
    }
}

class RelationManagerTenantScopeHydrationPostPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function update(User $user, Post $post): bool
    {
        return true;
    }
}

class RelationManagerTenantScopeHydrationOwnerPage extends EditRecord
{
    protected static string $resource = TenantScopedUserResource::class;
}

class RelationManagerTenantScopeHydrationNonResourcePage extends Page
{
    protected string $view = 'pages.settings';
}

class TenantScopedPostsRelationManager extends RelationManager
{
    public static int | string | null $deniedOwnerKey = null;

    protected static string $relationship = 'posts';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return ((string) $ownerRecord->getKey() !== (string) static::$deniedOwnerKey)
            && TenantScopedUserResource::getEloquentQuery()->whereKey($ownerRecord->getKey())->exists()
            && parent::canViewForRecord($ownerRecord, $pageClass);
    }

    public function table(Table $table): Table
    {
        return $table->recordActions([EditAction::make()]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('title')->required()]);
    }
}

beforeEach(function (): void {
    TenantScopedPostsRelationManager::$deniedOwnerKey = null;

    Gate::policy(User::class, RelationManagerTenantScopeHydrationOwnerPolicy::class);
    Gate::policy(Post::class, RelationManagerTenantScopeHydrationPostPolicy::class);

    $panel = Filament::getPanel('tenancy');
    Filament::setCurrentPanel($panel);
    Filament::setTenant(null);
    TenantScopedUserResource::registerTenancyModelGlobalScope($panel);
});

function relationManagerTenantScopeHydrationFixture(): array
{
    $tenantA = Team::factory()->create();
    $tenantB = Team::factory()->create();
    $owner = User::factory()->create();
    $owner->teams()->attach($tenantA);
    $post = Post::factory()->create(['author_id' => $owner->getKey()]);

    Filament::setTenant($tenantA);

    return [$tenantA, $tenantB, $owner, $post];
}

it('can reject a moved owner through `canViewForRecord()` before an action runs', function (): void {
    [, $tenantB, $owner, $post] = relationManagerTenantScopeHydrationFixture();
    $originalTitle = $post->title;
    $component = livewire(TenantScopedPostsRelationManager::class, [
        'ownerRecord' => $owner,
        'pageClass' => RelationManagerTenantScopeHydrationOwnerPage::class,
    ])
        ->assertSuccessful()
        ->mountAction(TestAction::make(EditAction::class)->table($post))
        ->fillForm(['title' => 'must-not-cross-tenant-boundary']);

    $owner->teams()->sync([$tenantB->getKey()]);
    $owner->unsetRelation('teams');

    expect(TenantScopedUserResource::getEloquentQuery()->find($owner->getKey()))->toBeNull();

    $component
        ->callMountedAction()
        ->assertForbidden();

    expect($post->refresh()->title)->toBe($originalTitle);
});

it('can scope a lazy relation manager owner in `canViewForRecord()` outside a resource page', function (): void {
    [, $tenantB, $owner, $post] = relationManagerTenantScopeHydrationFixture();
    $component = livewire(TenantScopedPostsRelationManager::class, [
        'ownerRecord' => $owner,
        'pageClass' => RelationManagerTenantScopeHydrationNonResourcePage::class,
        'lazy' => true,
    ])->assertSuccessful();

    expect(preg_match(
        "/__lazyLoad\\('([^']+)'\\)/",
        html_entity_decode($component->html()),
        $matches,
    ))->toBe(1);

    $component
        ->call('__lazyLoad', $matches[1])
        ->assertSuccessful()
        ->assertCanSeeTableRecords([$post]);

    $owner->teams()->sync([$tenantB->getKey()]);
    $owner->unsetRelation('teams');

    $component
        ->set('tableSearch', 'search')
        ->assertForbidden();
});

it('can reject a moved owner in `canViewForRecord()` before a lazy mount', function (): void {
    [, $tenantB, $owner] = relationManagerTenantScopeHydrationFixture();
    $component = livewire(TenantScopedPostsRelationManager::class, [
        'ownerRecord' => $owner,
        'pageClass' => RelationManagerTenantScopeHydrationOwnerPage::class,
        'lazy' => true,
    ])->assertSuccessful();

    expect(preg_match(
        "/__lazyLoad\\('([^']+)'\\)/",
        html_entity_decode($component->html()),
        $matches,
    ))->toBe(1);

    $owner->teams()->sync([$tenantB->getKey()]);
    $owner->unsetRelation('teams');

    $component
        ->call('__lazyLoad', $matches[1])
        ->assertForbidden();
});

it('can reject a moved owner in `canViewForRecord()` before calls other than `__lazyLoad()`', function (): void {
    [, $tenantB, $owner] = relationManagerTenantScopeHydrationFixture();
    $component = livewire(TenantScopedPostsRelationManager::class, [
        'ownerRecord' => $owner,
        'pageClass' => RelationManagerTenantScopeHydrationOwnerPage::class,
        'lazy' => true,
    ])->assertSuccessful();

    $owner->teams()->sync([$tenantB->getKey()]);
    $owner->unsetRelation('teams');

    $component
        ->set('tableSearch', 'search')
        ->assertForbidden();
});

it('retains the original owner and action target when another signed lazy payload is replayed', function (): void {
    [$tenant, , $owner, $post] = relationManagerTenantScopeHydrationFixture();
    $otherOwner = User::factory()->create();
    $otherOwner->teams()->attach($tenant);
    $otherPost = Post::factory()->create(['author_id' => $otherOwner->getKey()]);
    $otherTitle = $otherPost->title;
    $component = livewire(TenantScopedPostsRelationManager::class, [
        'ownerRecord' => $owner,
        'pageClass' => RelationManagerTenantScopeHydrationOwnerPage::class,
        'lazy' => true,
    ])->assertSuccessful();
    $otherComponent = livewire(TenantScopedPostsRelationManager::class, [
        'ownerRecord' => $otherOwner,
        'pageClass' => RelationManagerTenantScopeHydrationOwnerPage::class,
        'lazy' => true,
    ])->assertSuccessful();

    expect(preg_match(
        "/__lazyLoad\\('([^']+)'\\)/",
        html_entity_decode($otherComponent->html()),
        $matches,
    ))->toBe(1);

    TenantScopedPostsRelationManager::$deniedOwnerKey = $otherOwner->getKey();

    $component
        ->call('__lazyLoad', $matches[1])
        ->assertSuccessful()
        ->assertCanSeeTableRecords([$post])
        ->assertCanNotSeeTableRecords([$otherPost]);

    expect($component->instance()->getOwnerRecord()->is($owner))->toBeTrue();

    $component
        ->callAction(TestAction::make(EditAction::class)->table($post), [
            'title' => 'updated original owner post',
        ])
        ->assertSuccessful()
        ->assertHasNoFormErrors();

    expect($post->refresh()->title)->toBe('updated original owner post')
        ->and($otherPost->refresh()->title)->toBe($otherTitle);
});

it('loads a lazy relation manager while its owner remains in the tenant', function (): void {
    [, , $owner, $post] = relationManagerTenantScopeHydrationFixture();
    $component = livewire(TenantScopedPostsRelationManager::class, [
        'ownerRecord' => $owner,
        'pageClass' => RelationManagerTenantScopeHydrationOwnerPage::class,
        'lazy' => true,
    ])->assertSuccessful();

    expect(preg_match(
        "/__lazyLoad\\('([^']+)'\\)/",
        html_entity_decode($component->html()),
        $matches,
    ))->toBe(1);

    $component
        ->call('__lazyLoad', $matches[1])
        ->assertSuccessful()
        ->assertCanSeeTableRecords([$post]);
});

it('allows a relation manager action while its owner remains in the tenant', function (): void {
    [, , $owner, $post] = relationManagerTenantScopeHydrationFixture();

    livewire(TenantScopedPostsRelationManager::class, [
        'ownerRecord' => $owner,
        'pageClass' => RelationManagerTenantScopeHydrationOwnerPage::class,
    ])
        ->callAction(TestAction::make(EditAction::class)->table($post), [
            'title' => 'updated in tenant',
        ])
        ->assertSuccessful()
        ->assertHasNoFormErrors();

    expect($post->refresh()->title)->toBe('updated in tenant');
});

it('preserves a soft-deleted owner approved by its resource on mount and later requests', function (bool $isLazy): void {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
    $ticket = Ticket::factory()->create();
    $ticket->delete();
    $owner = TicketResource::resolveRecordRouteBinding($ticket->getKey());

    expect($owner)->not->toBeNull();

    $component = livewire(DepartmentsRelationManager::class, [
        'ownerRecord' => $owner,
        'pageClass' => EditTicket::class,
        'lazy' => $isLazy,
    ])->assertSuccessful();

    if ($isLazy) {
        expect(preg_match(
            "/__lazyLoad\\('([^']+)'\\)/",
            html_entity_decode($component->html()),
            $matches,
        ))->toBe(1);

        $component->call('__lazyLoad', $matches[1])->assertSuccessful();
    }

    $component->set('tableSearch', 'Support')->assertSuccessful();

    expect($component->instance()->getOwnerRecord()->is($ticket))->toBeTrue()
        ->and($component->instance()->getOwnerRecord()->trashed())->toBeTrue();
})->with([false, true]);
