<?php

use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Tests\Fixtures\Models\Company;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Resources\Companies\Resources\CompanyTeamResource;
use Filament\Tests\Fixtures\Resources\Companies\Resources\CompanyTeamResource\Pages\CreateCompanyTeam;
use Filament\Tests\Fixtures\Resources\Companies\Resources\CompanyTeamResource\Pages\EditCompanyTeam;
use Filament\Tests\Fixtures\Resources\Companies\Resources\CompanyTeamResource\Pages\ListCompanyTeams;
use Filament\Tests\Fixtures\Resources\Companies\Resources\CompanyTeamResource\Pages\ViewCompanyTeam;
use Filament\Tests\Fixtures\Resources\Users\Resources\UserPostResource;
use Filament\Tests\Fixtures\Resources\Users\Resources\UserPostResource\Pages\CreateUserPost;
use Filament\Tests\Fixtures\Resources\Users\Resources\UserPostResource\Pages\EditUserPost;
use Filament\Tests\Fixtures\Resources\Users\Resources\UserPostResource\Pages\ListUserPosts;
use Filament\Tests\Fixtures\Resources\Users\Resources\UserPostResource\Pages\ViewUserPost;
use Filament\Tests\Panels\Resources\TestCase;

use function Filament\Tests\livewire;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\assertSoftDeleted;

uses(TestCase::class);

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

// Non-soft-deletable nested resource tests (Company -> Team)

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
