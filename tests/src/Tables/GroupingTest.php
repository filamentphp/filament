<?php

use Filament\Tables;
use Filament\Tests\Fixtures\Livewire\PostsTable;
use Filament\Tests\Fixtures\Livewire\UsersTable;
use Filament\Tests\Fixtures\Models\Company;
use Filament\Tests\Fixtures\Models\Image;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Profile;
use Filament\Tests\Fixtures\Models\Setting;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Tables\TestCase;
use Livewire\Features\SupportTesting\Testable;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can group a table', function (): void {
    $posts = Post::factory()->count(20)->create();

    livewire(PostsTable::class)
        ->tap(function (Testable $testable): void {
            /** @var PostsTable $livewire */
            $livewire = $testable->instance();

            $table = $livewire->getTable();

            expect($table)
                ->getGrouping()->toBeNull();

            $groups = $table->getGroups();

            expect($groups['author.name'])
                ->getLabel()->toBe('Dynamic label');
        })
        ->set('tableGrouping', 'author.name')
        ->tap(function (Testable $testable): void {
            /** @var PostsTable $livewire */
            $livewire = $testable->instance();

            $table = $livewire->getTable();

            expect($table)
                ->getGrouping()->toBeInstanceOf(Tables\Grouping\Group::class)
                ->and($table->getGrouping())
                ->getLabel()->toBe('Dynamic label');
        });
});

it('can group records by column', function (): void {
    // Create posts with different titles to group by
    Post::factory()->create(['title' => 'Apple Post']);
    Post::factory()->create(['title' => 'Banana Post']);
    Post::factory()->create(['title' => 'Apple Post']);
    Post::factory()->create(['title' => 'Cherry Post']);
    Post::factory()->create(['title' => 'Banana Post']);

    $sortedPosts = Post::query()->orderBy('title')->orderBy('id')->get();

    livewire(PostsTable::class)
        ->set('tableGrouping', 'title')
        ->assertCanSeeTableRecords($sortedPosts, inOrder: true);
});

it('can group records by relationship', function (): void {
    // Create users with specific names to control order
    $userAlice = User::factory()->create(['name' => 'Alice']);
    $userBob = User::factory()->create(['name' => 'Bob']);
    $userCharlie = User::factory()->create(['name' => 'Charlie']);

    // Create posts with those authors
    Post::factory()->create(['author_id' => $userBob->id]);
    Post::factory()->create(['author_id' => $userAlice->id]);
    Post::factory()->create(['author_id' => $userCharlie->id]);
    Post::factory()->create(['author_id' => $userAlice->id]);
    Post::factory()->create(['author_id' => $userBob->id]);

    $sortedPosts = Post::query()
        ->orderBy(
            User::query()
                ->select('name')
                ->whereColumn('users.id', 'posts.author_id')
                ->limit(1)
        )
        ->orderBy('posts.id')
        ->get();

    livewire(PostsTable::class)
        ->set('tableGrouping', 'author.name')
        ->assertCanSeeTableRecords($sortedPosts, inOrder: true);
});

it('can group records by nested relationship', function (): void {
    // Create teams with specific names to control order
    $teamAlpha = Team::factory()->create(['name' => 'Alpha Team']);
    $teamBeta = Team::factory()->create(['name' => 'Beta Team']);
    $teamGamma = Team::factory()->create(['name' => 'Gamma Team']);

    // Create users with teams
    $userWithAlpha = User::factory()->create(['team_id' => $teamAlpha->id]);
    $userWithBeta = User::factory()->create(['team_id' => $teamBeta->id]);
    $userWithGamma = User::factory()->create(['team_id' => $teamGamma->id]);

    // Create posts with those authors
    Post::factory()->create(['author_id' => $userWithBeta->id]);
    Post::factory()->create(['author_id' => $userWithAlpha->id]);
    Post::factory()->create(['author_id' => $userWithGamma->id]);
    Post::factory()->create(['author_id' => $userWithAlpha->id]);
    Post::factory()->create(['author_id' => $userWithBeta->id]);

    $sortedPosts = Post::query()
        ->orderBy(
            Team::query()
                ->select('teams.name')
                ->whereColumn('teams.id', 'users.team_id')
                ->join('users', 'users.team_id', '=', 'teams.id')
                ->whereColumn('users.id', 'posts.author_id')
                ->limit(1)
        )
        ->orderBy('posts.id')
        ->get();

    livewire(PostsTable::class)
        ->set('tableGrouping', 'author.team.name')
        ->assertCanSeeTableRecords($sortedPosts, inOrder: true);
});

it('can group records by `BelongsTo` -> `HasOne` relationship', function (): void {
    // Create posts with unique profile bios
    $bios = ['Alpha bio', 'Beta bio', 'Gamma bio', 'Delta bio', 'Epsilon bio'];
    foreach ($bios as $bio) {
        $user = User::factory()->has(
            Profile::factory()->state(['bio' => $bio]),
            'profile'
        )->create();
        Post::factory()->create(['author_id' => $user->id]);
    }

    $sortedPosts = Post::query()
        ->orderBy(
            Profile::query()
                ->select('bio')
                ->whereColumn('profiles.user_id', 'users.id')
                ->join('users', 'users.id', '=', 'profiles.user_id')
                ->whereColumn('users.id', 'posts.author_id')
                ->limit(1)
        )
        ->orderBy('posts.id')
        ->get();

    livewire(PostsTable::class)
        ->set('tableGrouping', 'author.profile.bio')
        ->assertCanSeeTableRecords($sortedPosts, inOrder: true);
});

it('can group records by `BelongsTo` -> `HasOne` -> `BelongsTo` relationship', function (): void {
    // Create posts with users that have profiles linked to companies
    $companyNames = ['Acme Corp', 'Beta Inc', 'Gamma LLC', 'Delta Co', 'Epsilon Ltd'];
    foreach ($companyNames as $companyName) {
        $company = Company::factory()->create(['name' => $companyName]);
        $user = User::factory()->has(
            Profile::factory()->for($company, 'company'),
            'profile'
        )->create();
        Post::factory()->create(['author_id' => $user->id]);
    }

    $sortedPosts = Post::query()
        ->orderBy(
            Company::query()
                ->select('companies.name')
                ->whereColumn('companies.id', 'profiles.company_id')
                ->join('profiles', 'profiles.company_id', '=', 'companies.id')
                ->join('users', 'users.id', '=', 'profiles.user_id')
                ->whereColumn('users.id', 'posts.author_id')
                ->limit(1)
        )
        ->orderBy('posts.id')
        ->get();

    livewire(PostsTable::class)
        ->set('tableGrouping', 'author.profile.company.name')
        ->assertCanSeeTableRecords($sortedPosts, inOrder: true);
});

it('can group records by `HasOne` -> `BelongsTo` relationship', function (): void {
    // Create users with profiles linked to different companies
    $companyNames = ['Alpha Corp', 'Beta Corp', 'Gamma Corp', 'Delta Corp', 'Epsilon Corp'];
    foreach ($companyNames as $companyName) {
        $company = Company::factory()->create(['name' => $companyName]);
        User::factory()->has(
            Profile::factory()->for($company, 'company'),
            'profile'
        )->create();
    }

    $sortedUsers = User::query()
        ->orderBy(
            Company::query()
                ->select('companies.name')
                ->whereColumn('companies.id', 'profiles.company_id')
                ->join('profiles', 'profiles.company_id', '=', 'companies.id')
                ->whereColumn('profiles.user_id', 'users.id')
                ->limit(1)
        )
        ->orderBy('users.id')
        ->get();

    livewire(UsersTable::class)
        ->set('tableGrouping', 'profile.company.name')
        ->assertCanSeeTableRecords($sortedUsers, inOrder: true);
});

it('can group records by `HasOne` -> `HasOne` relationship', function (): void {
    // Create users with profiles that have settings
    $themes = ['alpha-theme', 'beta-theme', 'gamma-theme', 'delta-theme', 'epsilon-theme'];
    foreach ($themes as $theme) {
        User::factory()->has(
            Profile::factory()->has(
                Setting::factory()->state(['theme' => $theme]),
                'setting'
            ),
            'profile'
        )->create();
    }

    $sortedUsers = User::query()
        ->orderBy(
            Setting::query()
                ->select('theme')
                ->whereColumn('settings.profile_id', 'profiles.id')
                ->join('profiles', 'profiles.id', '=', 'settings.profile_id')
                ->whereColumn('profiles.user_id', 'users.id')
                ->limit(1)
        )
        ->orderBy('users.id')
        ->get();

    livewire(UsersTable::class)
        ->set('tableGrouping', 'profile.setting.theme')
        ->assertCanSeeTableRecords($sortedUsers, inOrder: true);
});

it('can group records by `MorphOne` relationship', function (): void {
    $urls = ['alpha.jpg', 'beta.jpg', 'gamma.jpg', 'delta.jpg', 'epsilon.jpg'];
    foreach ($urls as $url) {
        $user = User::factory()->create();
        Image::factory()->create([
            'url' => $url,
            'imageable_type' => User::class,
            'imageable_id' => $user->id,
        ]);
    }

    $sortedUsers = User::query()
        ->orderBy(
            Image::query()
                ->select('url')
                ->whereColumn('images.imageable_id', 'users.id')
                ->where('images.imageable_type', User::class)
                ->limit(1)
        )
        ->orderBy('users.id')
        ->get();

    livewire(UsersTable::class)
        ->set('tableGrouping', 'image.url')
        ->assertCanSeeTableRecords($sortedUsers, inOrder: true);
});

it('can group records with nullable `BelongsTo` relationship', function (): void {
    $userAlpha = User::factory()->create(['name' => 'Alpha']);
    $userBeta = User::factory()->create(['name' => 'Beta']);

    $postWithAlpha = Post::factory()->create(['author_id' => $userAlpha->id]);
    $postWithBeta = Post::factory()->create(['author_id' => $userBeta->id]);
    $postWithoutAuthor1 = Post::factory()->create(['author_id' => null]);
    $postWithoutAuthor2 = Post::factory()->create(['author_id' => null]);

    $allPosts = collect([$postWithAlpha, $postWithBeta, $postWithoutAuthor1, $postWithoutAuthor2]);

    // Just verify grouping doesn't crash with nullable relationships
    livewire(PostsTable::class)
        ->set('tableGrouping', 'author.name')
        ->assertCanSeeTableRecords($allPosts);
});

it('can group records with nullable `HasOne` relationship', function (): void {
    $userWithProfile1 = User::factory()->has(
        Profile::factory()->state(['bio' => 'Alpha bio']),
        'profile'
    )->create();

    $userWithProfile2 = User::factory()->has(
        Profile::factory()->state(['bio' => 'Beta bio']),
        'profile'
    )->create();

    $userWithoutProfile1 = User::factory()->create();
    $userWithoutProfile2 = User::factory()->create();

    $allUsers = collect([$userWithProfile1, $userWithProfile2, $userWithoutProfile1, $userWithoutProfile2]);

    // Just verify grouping doesn't crash with nullable relationships
    livewire(UsersTable::class)
        ->set('tableGrouping', 'profile.bio')
        ->assertCanSeeTableRecords($allUsers);
});

it('can group records with nullable `MorphOne` relationship', function (): void {
    $userWithImage1 = User::factory()->create();
    Image::factory()->create([
        'url' => 'alpha.jpg',
        'imageable_type' => User::class,
        'imageable_id' => $userWithImage1->id,
    ]);

    $userWithImage2 = User::factory()->create();
    Image::factory()->create([
        'url' => 'beta.jpg',
        'imageable_type' => User::class,
        'imageable_id' => $userWithImage2->id,
    ]);

    $userWithoutImage1 = User::factory()->create();
    $userWithoutImage2 = User::factory()->create();

    $allUsers = collect([$userWithImage1, $userWithImage2, $userWithoutImage1, $userWithoutImage2]);

    // Just verify grouping doesn't crash with nullable relationships
    livewire(UsersTable::class)
        ->set('tableGrouping', 'image.url')
        ->assertCanSeeTableRecords($allUsers);
});

it('can group records with nullable nested `BelongsTo` -> `HasOne` relationship', function (): void {
    $userWithProfile = User::factory()->has(
        Profile::factory()->state(['bio' => 'Alpha bio']),
        'profile'
    )->create();
    $postWithAuthorAndProfile = Post::factory()->create(['author_id' => $userWithProfile->id]);

    $userWithoutProfile = User::factory()->create();
    $postWithAuthorNoProfile = Post::factory()->create(['author_id' => $userWithoutProfile->id]);

    $postWithoutAuthor = Post::factory()->create(['author_id' => null]);

    $allPosts = collect([$postWithAuthorAndProfile, $postWithAuthorNoProfile, $postWithoutAuthor]);

    // Just verify grouping doesn't crash with nullable nested relationships
    livewire(PostsTable::class)
        ->set('tableGrouping', 'author.profile.bio')
        ->assertCanSeeTableRecords($allPosts);
});

it('can group records with nullable nested `BelongsTo` -> `HasOne` -> `BelongsTo` relationship', function (): void {
    $company = Company::factory()->create(['name' => 'Acme Corp']);
    $userWithProfileAndCompany = User::factory()->has(
        Profile::factory()->for($company, 'company'),
        'profile'
    )->create();
    $postComplete = Post::factory()->create(['author_id' => $userWithProfileAndCompany->id]);

    $userWithProfileNoCompany = User::factory()->has(
        Profile::factory()->state(['company_id' => null]),
        'profile'
    )->create();
    $postNoCompany = Post::factory()->create(['author_id' => $userWithProfileNoCompany->id]);

    $userNoProfile = User::factory()->create();
    $postNoProfile = Post::factory()->create(['author_id' => $userNoProfile->id]);

    $postNoAuthor = Post::factory()->create(['author_id' => null]);

    $allPosts = collect([$postComplete, $postNoCompany, $postNoProfile, $postNoAuthor]);

    // Just verify grouping doesn't crash with nullable nested relationships
    livewire(PostsTable::class)
        ->set('tableGrouping', 'author.profile.company.name')
        ->assertCanSeeTableRecords($allPosts);
});
