<?php

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tests\Fixtures\Livewire\CustomDataTable;
use Filament\Tests\Fixtures\Livewire\PostsTable;
use Filament\Tests\Fixtures\Livewire\PostsTableWithQualifiedColumns;
use Filament\Tests\Fixtures\Livewire\PostsTableWithTableSearchableColumns;
use Filament\Tests\Fixtures\Livewire\UsersTable;
use Filament\Tests\Fixtures\Livewire\UsersWithTeamTable;
use Filament\Tests\Fixtures\Models\Company;
use Filament\Tests\Fixtures\Models\Image;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Profile;
use Filament\Tests\Fixtures\Models\Setting;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Tables\TestCase;
use Illuminate\Support\Str;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render text column', function (): void {
    Post::factory()->count(10)->create();

    livewire(PostsTable::class)
        ->assertCanRenderTableColumn('title');
});

it('can render text column with relationship', function (): void {
    Post::factory()->count(10)->create();

    livewire(PostsTable::class)
        ->assertCanRenderTableColumn('author.name');
});

it('can sort records', function (): void {
    Post::factory()->count(10)->create();

    $sortedAsc = Post::query()->orderBy('title')->orderBy('id')->get();
    $sortedDesc = Post::query()->orderByDesc('title')->orderBy('id')->get();

    livewire(PostsTable::class)
        ->sortTable('title')
        ->assertCanSeeTableRecords($sortedAsc, inOrder: true)
        ->sortTable('title', 'desc')
        ->assertCanSeeTableRecords($sortedDesc, inOrder: true);
});

it('can sort records with relationship', function (): void {
    Post::factory()->count(10)->create();

    $sortedAsc = Post::query()
        ->orderBy(
            User::query()
                ->select('name')
                ->whereColumn('users.id', 'posts.author_id')
                ->limit(1)
        )
        ->orderBy('posts.id')
        ->get();

    $sortedDesc = Post::query()
        ->orderByDesc(
            User::query()
                ->select('name')
                ->whereColumn('users.id', 'posts.author_id')
                ->limit(1)
        )
        ->orderBy('posts.id')
        ->get();

    livewire(PostsTable::class)
        ->sortTable('author.name')
        ->assertCanSeeTableRecords($sortedAsc, inOrder: true)
        ->sortTable('author.name', 'desc')
        ->assertCanSeeTableRecords($sortedDesc, inOrder: true);
});

it('can sort records with nested relationship', function (): void {
    Post::factory()->count(5)->state(fn (): array => [
        'author_id' => User::factory()->state([
            'team_id' => Team::factory(),
        ]),
    ])->create();

    $sortedAsc = Post::query()
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

    $sortedDesc = Post::query()
        ->orderByDesc(
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
        ->sortTable('author.team.name')
        ->assertCanSeeTableRecords($sortedAsc, inOrder: true)
        ->sortTable('author.team.name', 'desc')
        ->assertCanSeeTableRecords($sortedDesc, inOrder: true);
});

it('can sort records with JSON column', function (): void {
    $posts = Post::factory()->count(10)->state(fn (): array => [
        'json' => ['foo' => Str::random()],
    ])->create();

    // Get database-sorted results to match actual query behavior
    $sortedAsc = Post::query()->orderBy('json->foo')->orderBy('id')->get();
    $sortedDesc = Post::query()->orderByDesc('json->foo')->orderBy('id')->get();

    livewire(PostsTable::class)
        ->sortTable('json.foo')
        ->assertCanSeeTableRecords($sortedAsc, inOrder: true)
        ->sortTable('json.foo', 'desc')
        ->assertCanSeeTableRecords($sortedDesc, inOrder: true);
});

it('can sort records with nested JSON column', function (): void {
    $posts = Post::factory()->count(10)->state(fn (): array => [
        'json' => ['bar' => ['baz' => Str::random()]],
    ])->create();

    // Get database-sorted results to match actual query behavior
    $sortedAsc = Post::query()->orderBy('json->bar->baz')->orderBy('id')->get();
    $sortedDesc = Post::query()->orderByDesc('json->bar->baz')->orderBy('id')->get();

    livewire(PostsTable::class)
        ->sortTable('json.bar.baz')
        ->assertCanSeeTableRecords($sortedAsc, inOrder: true)
        ->sortTable('json.bar.baz', 'desc')
        ->assertCanSeeTableRecords($sortedDesc, inOrder: true);
});

it('can sort records with relationship JSON column', function (): void {
    $posts = Post::factory()->count(10)->state(fn (): array => [
        'author_id' => User::factory()->state(['json' => ['foo' => Str::random()]]),
    ])->create();

    // Get database-sorted results using subquery orderBy
    $sortedAsc = Post::query()
        ->orderBy(
            User::query()
                ->select('json->foo')
                ->whereColumn('users.id', 'posts.author_id')
                ->limit(1)
        )
        ->orderBy('posts.id')
        ->get();

    $sortedDesc = Post::query()
        ->orderByDesc(
            User::query()
                ->select('json->foo')
                ->whereColumn('users.id', 'posts.author_id')
                ->limit(1)
        )
        ->orderBy('posts.id')
        ->get();

    livewire(PostsTable::class)
        ->sortTable('author.json.foo')
        ->assertCanSeeTableRecords($sortedAsc, inOrder: true)
        ->sortTable('author.json.foo', 'desc')
        ->assertCanSeeTableRecords($sortedDesc, inOrder: true);
});

it('can sort records with relationship nested JSON column', function (): void {
    $posts = Post::factory()->count(10)->state(fn (): array => [
        'author_id' => User::factory()->state(['json' => ['bar' => ['baz' => Str::random()]]]),
    ])->create();

    // Get database-sorted results using subquery orderBy
    $sortedAsc = Post::query()
        ->orderBy(
            User::query()
                ->select('json->bar->baz')
                ->whereColumn('users.id', 'posts.author_id')
                ->limit(1)
        )
        ->orderBy('posts.id')
        ->get();

    $sortedDesc = Post::query()
        ->orderByDesc(
            User::query()
                ->select('json->bar->baz')
                ->whereColumn('users.id', 'posts.author_id')
                ->limit(1)
        )
        ->orderBy('posts.id')
        ->get();

    livewire(PostsTable::class)
        ->sortTable('author.json.bar.baz')
        ->assertCanSeeTableRecords($sortedAsc, inOrder: true)
        ->sortTable('author.json.bar.baz', 'desc')
        ->assertCanSeeTableRecords($sortedDesc, inOrder: true);
});

it('can search records', function (): void {
    $posts = Post::factory()->count(10)->create();

    $title = $posts->first()->title;

    livewire(PostsTable::class)
        ->searchTable($title)
        ->assertCanSeeTableRecords($posts->where('title', $title))
        ->assertCanNotSeeTableRecords($posts->where('title', '!=', $title));
});

it('can search individual column records', function (): void {
    $posts = Post::factory()->count(10)->create();

    $content = $posts->first()->content;

    livewire(PostsTable::class)
        ->searchTableColumns(['content' => $content])
        ->assertCanSeeTableRecords($posts->where('content', $content))
        ->assertCanNotSeeTableRecords($posts->where('content', '!=', $content));
});

it('can search posts with relationship', function (): void {
    $posts = Post::factory()->count(10)->create();

    $author = $posts->first()->author->name;

    livewire(PostsTable::class)
        ->searchTable($author)
        ->assertCanSeeTableRecords($posts->where('author.name', $author))
        ->assertCanNotSeeTableRecords($posts->where('author.name', '!=', $author));
});

it('can search posts with JSON column', function (): void {
    $search = Str::random();

    $matchingPosts = Post::factory()->count(5)->create([
        'json' => ['foo' => $search],
    ]);

    $notMatchingPosts = Post::factory()->count(5)->create([
        'json' => ['foo' => Str::random()],
    ]);

    livewire(PostsTable::class)
        ->searchTable($search)
        ->assertCanSeeTableRecords($matchingPosts)
        ->assertCanNotSeeTableRecords($notMatchingPosts);
});

it('can search posts with nested JSON column', function (): void {
    $search = Str::random();

    $matchingPosts = Post::factory()->count(5)->create([
        'json' => ['bar' => ['baz' => $search]],
    ]);

    $notMatchingPosts = Post::factory()->count(5)->create([
        'json' => ['bar' => ['baz' => Str::random()]],
    ]);

    livewire(PostsTable::class)
        ->searchTable($search)
        ->assertCanSeeTableRecords($matchingPosts)
        ->assertCanNotSeeTableRecords($notMatchingPosts);
});

it('can search posts with relationship JSON column', function (): void {
    $search = Str::random();

    $matchingAuthor = User::factory()
        ->create(['json' => ['foo' => $search]]);

    $matchingPosts = Post::factory()
        ->for($matchingAuthor, 'author')
        ->count(5)
        ->create();

    $notMatchingPosts = Post::factory()
        ->count(5)
        ->create();

    livewire(PostsTable::class)
        ->searchTable($search)
        ->assertCanSeeTableRecords($matchingPosts)
        ->assertCanNotSeeTableRecords($notMatchingPosts);
});

it('can search posts with relationship nested JSON column', function (): void {
    $search = Str::random();

    $matchingAuthor = User::factory()
        ->create(['json' => ['bar' => ['baz' => $search]]]);

    $matchingPosts = Post::factory()
        ->for($matchingAuthor, 'author')
        ->count(5)
        ->create();

    $notMatchingPosts = Post::factory()
        ->count(5)
        ->create();

    livewire(PostsTable::class)
        ->searchTable($search)
        ->assertCanSeeTableRecords($matchingPosts)
        ->assertCanNotSeeTableRecords($notMatchingPosts);
});

it('can search individual column records with relationship', function (): void {
    $posts = Post::factory()->count(10)->create();

    $authorEmail = $posts->first()->author->email;

    livewire(PostsTable::class)
        ->searchTableColumns(['author.email' => $authorEmail])
        ->assertCanSeeTableRecords($posts->where('author.email', $authorEmail))
        ->assertCanNotSeeTableRecords($posts->where('author.email', '!=', $authorEmail));
});

it('can search multiple individual columns', function (): void {
    $posts = Post::factory()->count(10)->create();

    $content = $posts->first()->content;
    $authorEmail = $posts->first()->author->email;

    livewire(PostsTable::class)
        ->searchTableColumns([
            'content' => $content,
            'author.email' => $authorEmail,
        ])
        ->assertCanSeeTableRecords($posts->where('author.email', $authorEmail))
        ->assertCanNotSeeTableRecords($posts->where('author.email', '!=', $authorEmail));
});

it('can hide a column', function (): void {
    livewire(PostsTable::class)
        ->assertTableColumnVisible('visible')
        ->assertTableColumnHidden('hidden');
});

it('can call a column action', function (): void {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->callTableColumnAction('title', $post)
        ->assertDispatched('title-action-called');
});

it('can call a column action object', function (): void {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->callTableAction('column-action-object', $post)
        ->assertDispatched('column-action-object-called');
});

it('can state whether a column has the correct value with Eloquent records', function (): void {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->assertTableColumnStateSet('with_state', 'correct state', $post)
        ->assertTableColumnStateNotSet('with_state', 'incorrect state', $post);
});

it('can state whether a column has the correct value with custom data', function (): void {
    livewire(CustomDataTable::class)
        ->assertTableColumnStateSet('title', 'Second item', 2)
        ->assertTableColumnStateNotSet('title', 'incorrect state', 2);
});

it('can state whether a column with `counts()` has the correct value', function (): void {
    $user = User::factory()
        ->has(Post::factory()->count(3))
        ->create();

    livewire(UsersTable::class)
        ->assertTableColumnStateSet('posts_count', 3, $user)
        ->assertTableColumnStateNotSet('posts_count', 0, $user);
});

it('can state whether a column has the correct formatted value with Eloquent records', function (): void {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->assertTableColumnFormattedStateSet('formatted_state', 'formatted state', $post)
        ->assertTableColumnFormattedStateNotSet('formatted_state', 'incorrect formatted state', $post);
});

it('can state whether a column has the correct formatted value with custom data', function (): void {
    livewire(CustomDataTable::class)
        ->assertTableColumnFormattedStateSet('formatted_state', 'formatted state', 1)
        ->assertTableColumnFormattedStateNotSet('formatted_state', 'incorrect formatted state', 1);
});

it('can output JSON values', function (): void {
    $post = Post::factory()->create([
        'json' => ['foo' => 'bar'],
    ]);

    livewire(PostsTable::class)
        ->assertTableColumnStateSet('json.foo', 'bar', $post);
});

it('can output nested JSON values', function (): void {
    $post = Post::factory()->create([
        'json' => ['bar' => ['baz' => 'qux']],
    ]);

    livewire(PostsTable::class)
        ->assertTableColumnStateSet('json.bar.baz', 'qux', $post);
});

it('can output relationship JSON values', function (): void {
    $post = Post::factory()->create([
        'author_id' => User::factory()->state([
            'json' => ['foo' => 'bar'],
        ]),
    ]);

    livewire(PostsTable::class)
        ->assertTableColumnStateSet('author.json.foo', 'bar', $post);
});

it('can output relationship nested JSON values', function (): void {
    $post = Post::factory()->create([
        'author_id' => User::factory()->state([
            'json' => ['bar' => ['baz' => 'qux']],
        ]),
    ]);

    livewire(PostsTable::class)
        ->assertTableColumnStateSet('author.json.bar.baz', 'qux', $post);
});

it('can output values in a JSON array column of objects', function (): void {
    $post = Post::factory()->create([
        'json_array_of_objects' => [
            ['value' => 'foo'],
            ['value' => 'bar'],
            ['value' => 'baz'],
        ],
    ]);

    livewire(PostsTable::class)
        ->assertTableColumnStateSet('json_array_of_objects.*.value', ['foo', 'bar', 'baz'], $post);
});

it('can output values in a JSON column with a non-relationship accessor method', function (): void {
    $post = Post::factory()->create([
        'config' => ['setting' => 'foo'],
    ]);

    livewire(PostsTable::class)
        ->assertTableColumnStateSet('config.setting', 'foo', $post);
});

it('can state whether a column has extra attributes with Eloquent records', function (): void {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->assertTableColumnHasExtraAttributes('extra_attributes', ['class' => 'text-danger-500'], $post)
        ->assertTableColumnDoesNotHaveExtraAttributes('extra_attributes', ['class' => 'text-primary-500'], $post);
});

it('can state whether a column has extra attributes with custom data', function (): void {
    livewire(CustomDataTable::class)
        ->assertTableColumnHasExtraAttributes('extra_attributes', ['class' => 'text-danger-500'], 1)
        ->assertTableColumnDoesNotHaveExtraAttributes('extra_attributes', ['class' => 'text-primary-500'], 1);
});

it('can state whether a column has a description with Eloquent records', function (): void {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->assertTableColumnHasDescription('with_description', 'description below', $post)
        ->assertTableColumnHasDescription('with_description', 'description above', $post, 'above')
        ->assertTableColumnDoesNotHaveDescription('with_description', 'incorrect description below', $post)
        ->assertTableColumnDoesNotHaveDescription('with_description', 'incorrect description above', $post, 'above');
});

it('can state whether a column has a description with custom data', function (): void {
    livewire(CustomDataTable::class)
        ->assertTableColumnHasDescription('with_description', 'description below', 1)
        ->assertTableColumnHasDescription('with_description', 'description above', 1, 'above')
        ->assertTableColumnDoesNotHaveDescription('with_description', 'incorrect description below', 1)
        ->assertTableColumnDoesNotHaveDescription('with_description', 'incorrect description above', 1, 'above');
});

it('can state whether a select column has options with Eloquent records', function (): void {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->assertTableSelectColumnHasOptions('with_options', ['red' => 'Red', 'blue' => 'Blue'], $post)
        ->assertTableSelectColumnDoesNotHaveOptions('with_options', ['one' => 'One', 'two' => 'Two'], $post);
});

it('can state whether a select column has options with custom data', function (): void {
    livewire(CustomDataTable::class)
        ->assertTableSelectColumnHasOptions('with_options', ['red' => 'Red', 'blue' => 'Blue'], 2)
        ->assertTableSelectColumnDoesNotHaveOptions('with_options', ['one' => 'One', 'two' => 'Two'], 2);
});

it('can assert that a column exists with the given configuration', function (): void {
    $publishedPost = Post::factory()->create([
        'is_published' => true,
    ]);

    livewire(PostsTable::class)
        ->assertTableColumnExists('title2', function (TextColumn $column) {
            return $column->isSortable() &&
                $column->isSearchable() &&
                $column->getPrefix() == 'published';
        }, $publishedPost);

    $unpublishedPost = Post::factory()->create([
        'is_published' => false,
    ]);

    livewire(PostsTable::class)
        ->assertTableColumnExists('title2', function (TextColumn $column) {
            return $column->getPrefix() == 'unpublished';
        }, $unpublishedPost);

    $this->expectException('PHPUnit\Framework\ExpectationFailedException');
    $this->expectExceptionMessage('Failed asserting that a column with the name [title] and provided configuration exists on the [' . PostsTable::class . '] component');

    livewire(PostsTable::class)
        ->assertTableColumnExists('title', function (TextColumn $column) {
            return $column->isTime();
        }, $publishedPost);
});

it('can automatically detect boolean cast attribute in icon column', function (): void {
    $post = Post::factory()
        ->create(['is_published' => false]);

    livewire(PostsTable::class)
        ->assertTableColumnExists('is_published', function (IconColumn $column) {
            return $column->isBoolean();
        }, $post);
});

it('can toggle all table columns', function (): void {
    Post::factory()->create();

    livewire(PostsTable::class)
        ->assertSuccessful()
        ->assertCountTableRecords(1)
        ->assertDontSeeText('Toggleable column state')
        ->toggleAllTableColumns()
        ->assertSeeText('Toggleable column state')
        ->toggleAllTableColumns(false)
        ->assertDontSeeText('Toggleable column state');
});

it('can search and sort by relationship column when both tables have the same column name', function (): void {
    $teamAlpha = Team::factory()->create(['name' => 'Team Alpha']);
    $teamBeta = Team::factory()->create(['name' => 'Team Beta']);

    $userAlice = User::factory()->create([
        'name' => 'Alice',
        'team_id' => $teamAlpha->id,
    ]);

    $userBob = User::factory()->create([
        'name' => 'Bob',
        'team_id' => $teamBeta->id,
    ]);

    livewire(UsersWithTeamTable::class)
        ->searchTable('Alice')
        ->sortTable('team.name')
        ->assertCanSeeTableRecords([$userAlice])
        ->assertCanNotSeeTableRecords([$userBob]);
});

it('can search and sort by nested relationship column when tables have the same column name, with a qualified column', function (): void {
    // Create teams (teams table has 'name' column)
    $teamAlpha = Team::factory()->create(['name' => 'Team Alpha']);
    $teamBeta = Team::factory()->create(['name' => 'Team Beta']);
    $teamGamma = Team::factory()->create(['name' => 'Team Gamma']);

    // Create users with names and team assignments (users table has 'name' column)
    $userAlice = User::factory()->create([
        'name' => 'Alice',
        'team_id' => $teamAlpha->id,
    ]);

    $userBob = User::factory()->create([
        'name' => 'Bob',
        'team_id' => $teamBeta->id,
    ]);

    $userCharlie = User::factory()->create([
        'name' => 'Charlie',
        'team_id' => $teamGamma->id,
    ]);

    // Create posts with those authors (posts table has searchable 'title' column)
    $postByAlice = Post::factory()->create([
        'author_id' => $userAlice->id,
        'title' => 'Alice\'s Post',
    ]);

    $postByBob = Post::factory()->create([
        'author_id' => $userBob->id,
        'title' => 'Bob\'s Post',
    ]);

    $postByCharlie = Post::factory()->create([
        'author_id' => $userCharlie->id,
        'title' => 'Charlie\'s Post',
    ]);

    livewire(PostsTableWithQualifiedColumns::class)
        ->searchTable('Alice')
        ->sortTable('author.team.name')
        ->assertCanSeeTableRecords([$postByAlice])
        ->assertCanNotSeeTableRecords([$postByBob, $postByCharlie]);
});

it('can sort records with `BelongsTo` -> `HasOne` relationship', function (): void {
    Post::factory()->count(5)->state(fn (): array => [
        'author_id' => User::factory()->has(
            Profile::factory()->state(fn (): array => [
                'bio' => fake()->sentence(),
            ]),
            'profile'
        ),
    ])->create();

    $sortedAsc = Post::query()
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

    $sortedDesc = Post::query()
        ->orderByDesc(
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
        ->sortTable('author.profile.bio')
        ->assertCanSeeTableRecords($sortedAsc, inOrder: true)
        ->sortTable('author.profile.bio', 'desc')
        ->assertCanSeeTableRecords($sortedDesc, inOrder: true);
});

it('can sort records with `BelongsTo` -> `HasOne` -> `BelongsTo` relationship', function (): void {
    Post::factory()->count(5)->state(fn (): array => [
        'author_id' => User::factory()->has(
            Profile::factory()->state(fn (): array => [
                'company_id' => Company::factory(),
            ]),
            'profile'
        ),
    ])->create();

    $sortedAsc = Post::query()
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

    $sortedDesc = Post::query()
        ->orderByDesc(
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
        ->sortTable('author.profile.company.name')
        ->assertCanSeeTableRecords($sortedAsc, inOrder: true)
        ->sortTable('author.profile.company.name', 'desc')
        ->assertCanSeeTableRecords($sortedDesc, inOrder: true);
});

it('can search records with `BelongsTo` -> `HasOne` relationship', function (): void {
    $searchBio = 'unique bio for testing';

    $matchingUser = User::factory()->has(
        Profile::factory()->state([
            'bio' => $searchBio,
        ]),
        'profile'
    )->create();

    $matchingPost = Post::factory()->for($matchingUser, 'author')->create();

    $nonMatchingPosts = Post::factory()->count(3)->create();

    livewire(PostsTable::class)
        ->searchTable($searchBio)
        ->assertCanSeeTableRecords([$matchingPost])
        ->assertCanNotSeeTableRecords($nonMatchingPosts);
});

it('can search records with `BelongsTo` -> `HasOne` -> `BelongsTo` relationship', function (): void {
    $searchCompany = 'Acme Corporation Testing';

    $company = Company::factory()->create([
        'name' => $searchCompany,
    ]);

    $matchingUser = User::factory()->has(
        Profile::factory()->for($company, 'company'),
        'profile'
    )->create();

    $matchingPost = Post::factory()->for($matchingUser, 'author')->create();

    $nonMatchingPosts = Post::factory()->count(3)->create();

    livewire(PostsTable::class)
        ->searchTable($searchCompany)
        ->assertCanSeeTableRecords([$matchingPost])
        ->assertCanNotSeeTableRecords($nonMatchingPosts);
});

it('can sort records with `HasOne` -> `BelongsTo` relationship', function (): void {
    for ($i = 0; $i < 5; $i++) {
        $company = Company::factory()->create(['name' => 'Company ' . chr(65 + $i)]);  // A, B, C, D, E
        User::factory()->has(
            Profile::factory()->for($company, 'company'),
            'profile'
        )->create();
    }

    $sortedAsc = User::query()
        ->orderBy(
            Company::query()
                ->select('name')
                ->whereColumn('companies.id', 'profiles.company_id')
                ->join('profiles', 'profiles.company_id', '=', 'companies.id')
                ->whereColumn('profiles.user_id', 'users.id')
                ->limit(1)
        )
        ->orderBy('users.id')
        ->get();

    $sortedDesc = User::query()
        ->orderByDesc(
            Company::query()
                ->select('name')
                ->whereColumn('companies.id', 'profiles.company_id')
                ->join('profiles', 'profiles.company_id', '=', 'companies.id')
                ->whereColumn('profiles.user_id', 'users.id')
                ->limit(1)
        )
        ->orderBy('users.id')
        ->get();

    livewire(UsersTable::class)
        ->sortTable('profile.company.name')
        ->assertCanSeeTableRecords($sortedAsc, inOrder: true)
        ->sortTable('profile.company.name', 'desc')
        ->assertCanSeeTableRecords($sortedDesc, inOrder: true);
});

it('can sort records with `HasOne` -> `HasOne` relationship', function (): void {
    $themes = ['alpha', 'beta', 'gamma', 'delta', 'epsilon'];

    foreach ($themes as $theme) {
        User::factory()->has(
            Profile::factory()->has(
                Setting::factory()->state(['theme' => $theme]),
                'setting'
            ),
            'profile'
        )->create();
    }

    $sortedAsc = User::query()
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

    $sortedDesc = User::query()
        ->orderByDesc(
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
        ->sortTable('profile.setting.theme')
        ->assertCanSeeTableRecords($sortedAsc, inOrder: true)
        ->sortTable('profile.setting.theme', 'desc')
        ->assertCanSeeTableRecords($sortedDesc, inOrder: true);
});

it('can search records with `HasOne` -> `BelongsTo` relationship', function (): void {
    $searchCompany = 'TechCorp Testing Inc';

    $company = Company::factory()->create(['name' => $searchCompany]);

    $matchingUser = User::factory()->has(
        Profile::factory()->for($company, 'company'),
        'profile'
    )->create();

    $nonMatchingUsers = User::factory()->count(3)->create();

    livewire(UsersTable::class)
        ->searchTable($searchCompany)
        ->assertCanSeeTableRecords([$matchingUser])
        ->assertCanNotSeeTableRecords($nonMatchingUsers);
});

it('can search records with `HasOne` -> `HasOne` relationship', function (): void {
    $searchTheme = 'midnight-purple';

    $matchingUser = User::factory()->has(
        Profile::factory()->has(
            Setting::factory()->state(['theme' => $searchTheme]),
            'setting'
        ),
        'profile'
    )->create();

    $nonMatchingUsers = User::factory()->count(3)->create();

    livewire(UsersTable::class)
        ->searchTable($searchTheme)
        ->assertCanSeeTableRecords([$matchingUser])
        ->assertCanNotSeeTableRecords($nonMatchingUsers);
});

it('can sort records with `MorphOne` relationship', function (): void {
    $urls = ['alpha.jpg', 'beta.jpg', 'gamma.jpg', 'delta.jpg', 'epsilon.jpg'];

    foreach ($urls as $url) {
        $user = User::factory()->create();
        Image::factory()->create([
            'url' => $url,
            'imageable_type' => User::class,
            'imageable_id' => $user->id,
        ]);
    }

    $sortedAsc = User::query()
        ->orderBy(
            Image::query()
                ->select('url')
                ->whereColumn('images.imageable_id', 'users.id')
                ->where('images.imageable_type', User::class)
                ->limit(1)
        )
        ->orderBy('users.id')
        ->get();

    $sortedDesc = User::query()
        ->orderByDesc(
            Image::query()
                ->select('url')
                ->whereColumn('images.imageable_id', 'users.id')
                ->where('images.imageable_type', User::class)
                ->limit(1)
        )
        ->orderBy('users.id')
        ->get();

    livewire(UsersTable::class)
        ->sortTable('image.url')
        ->assertCanSeeTableRecords($sortedAsc, inOrder: true)
        ->sortTable('image.url', 'desc')
        ->assertCanSeeTableRecords($sortedDesc, inOrder: true);
});

it('can sort records with `BelongsTo` -> `MorphOne` relationship', function (): void {
    $urls = ['alpha.jpg', 'beta.jpg', 'gamma.jpg', 'delta.jpg', 'epsilon.jpg'];

    foreach ($urls as $url) {
        $user = User::factory()->create();
        Image::factory()->create([
            'url' => $url,
            'imageable_type' => User::class,
            'imageable_id' => $user->id,
        ]);
        Post::factory()->create(['author_id' => $user->id]);
    }

    $sortedAsc = Post::query()
        ->orderBy(
            Image::query()
                ->select('url')
                ->whereColumn('images.imageable_id', 'users.id')
                ->where('images.imageable_type', User::class)
                ->join('users', 'users.id', '=', 'images.imageable_id')
                ->whereColumn('users.id', 'posts.author_id')
                ->limit(1)
        )
        ->orderBy('posts.id')
        ->get();

    $sortedDesc = Post::query()
        ->orderByDesc(
            Image::query()
                ->select('url')
                ->whereColumn('images.imageable_id', 'users.id')
                ->where('images.imageable_type', User::class)
                ->join('users', 'users.id', '=', 'images.imageable_id')
                ->whereColumn('users.id', 'posts.author_id')
                ->limit(1)
        )
        ->orderBy('posts.id')
        ->get();

    livewire(PostsTable::class)
        ->sortTable('author.image.url')
        ->assertCanSeeTableRecords($sortedAsc, inOrder: true)
        ->sortTable('author.image.url', 'desc')
        ->assertCanSeeTableRecords($sortedDesc, inOrder: true);
});

it('can sort records with `BelongsTo` -> `HasOne` -> `MorphOne` relationship', function (): void {
    $altTexts = ['alt alpha', 'alt beta', 'alt gamma', 'alt delta', 'alt epsilon'];

    foreach ($altTexts as $altText) {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);
        Image::factory()->create([
            'alt_text' => $altText,
            'imageable_type' => Profile::class,
            'imageable_id' => $profile->id,
        ]);
        Post::factory()->create(['author_id' => $user->id]);
    }

    $sortedAsc = Post::query()
        ->orderBy(
            Image::query()
                ->select('alt_text')
                ->whereColumn('images.imageable_id', 'profiles.id')
                ->where('images.imageable_type', Profile::class)
                ->join('profiles', 'profiles.id', '=', 'images.imageable_id')
                ->join('users', 'users.id', '=', 'profiles.user_id')
                ->whereColumn('users.id', 'posts.author_id')
                ->limit(1)
        )
        ->orderBy('posts.id')
        ->get();

    $sortedDesc = Post::query()
        ->orderByDesc(
            Image::query()
                ->select('alt_text')
                ->whereColumn('images.imageable_id', 'profiles.id')
                ->where('images.imageable_type', Profile::class)
                ->join('profiles', 'profiles.id', '=', 'images.imageable_id')
                ->join('users', 'users.id', '=', 'profiles.user_id')
                ->whereColumn('users.id', 'posts.author_id')
                ->limit(1)
        )
        ->orderBy('posts.id')
        ->get();

    livewire(PostsTable::class)
        ->sortTable('author.profile.image.alt_text')
        ->assertCanSeeTableRecords($sortedAsc, inOrder: true)
        ->sortTable('author.profile.image.alt_text', 'desc')
        ->assertCanSeeTableRecords($sortedDesc, inOrder: true);
});

it('can search records with `MorphOne` relationship', function (): void {
    $searchUrl = 'unique-image-url.jpg';

    $matchingUser = User::factory()->create();
    Image::factory()->create([
        'url' => $searchUrl,
        'imageable_type' => User::class,
        'imageable_id' => $matchingUser->id,
    ]);

    $nonMatchingUsers = User::factory()->count(3)->create();

    livewire(UsersTable::class)
        ->searchTable($searchUrl)
        ->assertCanSeeTableRecords([$matchingUser])
        ->assertCanNotSeeTableRecords($nonMatchingUsers);
});

it('can search records with `BelongsTo` -> `MorphOne` relationship', function (): void {
    $searchUrl = 'unique-author-image.jpg';

    $matchingUser = User::factory()->create();
    Image::factory()->create([
        'url' => $searchUrl,
        'imageable_type' => User::class,
        'imageable_id' => $matchingUser->id,
    ]);

    $matchingPost = Post::factory()->for($matchingUser, 'author')->create();
    $nonMatchingPosts = Post::factory()->count(3)->create();

    livewire(PostsTable::class)
        ->searchTable($searchUrl)
        ->assertCanSeeTableRecords([$matchingPost])
        ->assertCanNotSeeTableRecords($nonMatchingPosts);
});

it('can search records with `BelongsTo` -> `HasOne` -> `MorphOne` relationship', function (): void {
    $searchAltText = 'unique profile image alt text';

    $matchingUser = User::factory()->create();
    $matchingProfile = Profile::factory()->create(['user_id' => $matchingUser->id]);
    Image::factory()->create([
        'alt_text' => $searchAltText,
        'imageable_type' => Profile::class,
        'imageable_id' => $matchingProfile->id,
    ]);

    $matchingPost = Post::factory()->for($matchingUser, 'author')->create();
    $nonMatchingPosts = Post::factory()->count(3)->create();

    livewire(PostsTable::class)
        ->searchTable($searchAltText)
        ->assertCanSeeTableRecords([$matchingPost])
        ->assertCanNotSeeTableRecords($nonMatchingPosts);
});

it('can sort records with nullable `BelongsTo` relationship', function (): void {
    // Create posts with authors
    $userAlice = User::factory()->create(['name' => 'Alice']);
    $userBob = User::factory()->create(['name' => 'Bob']);

    $postWithAlice = Post::factory()->create(['author_id' => $userAlice->id]);
    $postWithBob = Post::factory()->create(['author_id' => $userBob->id]);

    // Create posts without authors (null relationship)
    $postWithoutAuthor1 = Post::factory()->create(['author_id' => null]);
    $postWithoutAuthor2 = Post::factory()->create(['author_id' => null]);

    $allPosts = collect([$postWithAlice, $postWithBob, $postWithoutAuthor1, $postWithoutAuthor2]);

    // Just verify sorting doesn't crash with nullable relationships
    livewire(PostsTable::class)
        ->sortTable('author.name')
        ->assertCanSeeTableRecords($allPosts)
        ->sortTable('author.name', 'desc')
        ->assertCanSeeTableRecords($allPosts);
});

it('can sort records with nullable `HasOne` relationship', function (): void {
    // Create users with profiles
    $userWithProfile1 = User::factory()->has(
        Profile::factory()->state(['bio' => 'Alpha bio']),
        'profile'
    )->create();

    $userWithProfile2 = User::factory()->has(
        Profile::factory()->state(['bio' => 'Beta bio']),
        'profile'
    )->create();

    // Create users without profiles (null relationship)
    $userWithoutProfile1 = User::factory()->create();
    $userWithoutProfile2 = User::factory()->create();

    $allUsers = collect([$userWithProfile1, $userWithProfile2, $userWithoutProfile1, $userWithoutProfile2]);

    // Just verify sorting doesn't crash with nullable relationships
    livewire(UsersTable::class)
        ->sortTable('profile.bio')
        ->assertCanSeeTableRecords($allUsers)
        ->sortTable('profile.bio', 'desc')
        ->assertCanSeeTableRecords($allUsers);
});

it('can sort records with nullable `MorphOne` relationship', function (): void {
    // Create users with images
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

    // Create users without images (null relationship)
    $userWithoutImage1 = User::factory()->create();
    $userWithoutImage2 = User::factory()->create();

    $allUsers = collect([$userWithImage1, $userWithImage2, $userWithoutImage1, $userWithoutImage2]);

    // Just verify sorting doesn't crash with nullable relationships
    livewire(UsersTable::class)
        ->sortTable('image.url')
        ->assertCanSeeTableRecords($allUsers)
        ->sortTable('image.url', 'desc')
        ->assertCanSeeTableRecords($allUsers);
});

it('can sort records with nullable nested `BelongsTo` -> `HasOne` relationship', function (): void {
    // Post with author that has profile
    $userWithProfile = User::factory()->has(
        Profile::factory()->state(['bio' => 'Alpha bio']),
        'profile'
    )->create();
    $postWithAuthorAndProfile = Post::factory()->create(['author_id' => $userWithProfile->id]);

    // Post with author but no profile
    $userWithoutProfile = User::factory()->create();
    $postWithAuthorNoProfile = Post::factory()->create(['author_id' => $userWithoutProfile->id]);

    // Post with no author
    $postWithoutAuthor = Post::factory()->create(['author_id' => null]);

    $allPosts = collect([$postWithAuthorAndProfile, $postWithAuthorNoProfile, $postWithoutAuthor]);

    // Just verify sorting doesn't crash with nullable nested relationships
    livewire(PostsTable::class)
        ->sortTable('author.profile.bio')
        ->assertCanSeeTableRecords($allPosts)
        ->sortTable('author.profile.bio', 'desc')
        ->assertCanSeeTableRecords($allPosts);
});

it('can sort records with nullable nested `BelongsTo` -> `HasOne` -> `BelongsTo` relationship', function (): void {
    // Post with author->profile->company
    $company = Company::factory()->create(['name' => 'Acme Corp']);
    $userWithProfileAndCompany = User::factory()->has(
        Profile::factory()->for($company, 'company'),
        'profile'
    )->create();
    $postComplete = Post::factory()->create(['author_id' => $userWithProfileAndCompany->id]);

    // Post with author and profile but no company
    $userWithProfileNoCompany = User::factory()->has(
        Profile::factory()->state(['company_id' => null]),
        'profile'
    )->create();
    $postNoCompany = Post::factory()->create(['author_id' => $userWithProfileNoCompany->id]);

    // Post with author but no profile
    $userNoProfile = User::factory()->create();
    $postNoProfile = Post::factory()->create(['author_id' => $userNoProfile->id]);

    // Post with no author
    $postNoAuthor = Post::factory()->create(['author_id' => null]);

    $allPosts = collect([$postComplete, $postNoCompany, $postNoProfile, $postNoAuthor]);

    // Just verify sorting doesn't crash with nullable nested relationships
    livewire(PostsTable::class)
        ->sortTable('author.profile.company.name')
        ->assertCanSeeTableRecords($allPosts)
        ->sortTable('author.profile.company.name', 'desc')
        ->assertCanSeeTableRecords($allPosts);
});

it('can sort records with nullable nested `BelongsTo` -> `MorphOne` relationship', function (): void {
    // Post with author that has image
    $userWithImage = User::factory()->create();
    Image::factory()->create([
        'url' => 'alpha.jpg',
        'imageable_type' => User::class,
        'imageable_id' => $userWithImage->id,
    ]);
    $postWithAuthorAndImage = Post::factory()->create(['author_id' => $userWithImage->id]);

    // Post with author but no image
    $userWithoutImage = User::factory()->create();
    $postWithAuthorNoImage = Post::factory()->create(['author_id' => $userWithoutImage->id]);

    // Post with no author
    $postWithoutAuthor = Post::factory()->create(['author_id' => null]);

    $allPosts = collect([$postWithAuthorAndImage, $postWithAuthorNoImage, $postWithoutAuthor]);

    // Just verify sorting doesn't crash with nullable nested relationships
    livewire(PostsTable::class)
        ->sortTable('author.image.url')
        ->assertCanSeeTableRecords($allPosts)
        ->sortTable('author.image.url', 'desc')
        ->assertCanSeeTableRecords($allPosts);
});

it('can search records with nullable `BelongsTo` relationship', function (): void {
    $userAlice = User::factory()->create(['name' => 'Alice Unique']);

    $matchingPost = Post::factory()->create(['author_id' => $userAlice->id]);
    $postWithoutAuthor = Post::factory()->create(['author_id' => null]);
    $nonMatchingPost = Post::factory()->create();

    livewire(PostsTable::class)
        ->searchTable('Alice Unique')
        ->assertCanSeeTableRecords([$matchingPost])
        ->assertCanNotSeeTableRecords([$postWithoutAuthor, $nonMatchingPost]);
});

it('can search records with nullable `HasOne` relationship', function (): void {
    $userWithProfile = User::factory()->has(
        Profile::factory()->state(['bio' => 'Unique bio search']),
        'profile'
    )->create();

    $userWithoutProfile = User::factory()->create();
    $otherUsers = User::factory()->count(2)->create();

    livewire(UsersTable::class)
        ->searchTable('Unique bio search')
        ->assertCanSeeTableRecords([$userWithProfile])
        ->assertCanNotSeeTableRecords([$userWithoutProfile, ...$otherUsers]);
});

it('can sort records with `BelongsToThrough` relationship', function (): void {
    Post::factory()->count(5)->state(fn (): array => [
        'author_id' => User::factory()->state([
            'team_id' => Team::factory(),
        ]),
    ])->create();

    $sortedAsc = Post::query()
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

    $sortedDesc = Post::query()
        ->orderByDesc(
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
        ->sortTable('team.name')
        ->assertCanSeeTableRecords($sortedAsc, inOrder: true)
        ->sortTable('team.name', 'desc')
        ->assertCanSeeTableRecords($sortedDesc, inOrder: true);
});

it('can sort records with nullable `BelongsToThrough` relationship', function (): void {
    // Post with author that has team
    $teamAlpha = Team::factory()->create(['name' => 'Alpha Team']);
    $userWithTeam = User::factory()->create(['team_id' => $teamAlpha->id]);
    $postWithTeam = Post::factory()->create(['author_id' => $userWithTeam->id]);

    // Post with author but no team
    $userWithoutTeam = User::factory()->create(['team_id' => null]);
    $postWithAuthorNoTeam = Post::factory()->create(['author_id' => $userWithoutTeam->id]);

    // Post with no author
    $postWithoutAuthor = Post::factory()->create(['author_id' => null]);

    $allPosts = collect([$postWithTeam, $postWithAuthorNoTeam, $postWithoutAuthor]);

    // Just verify sorting doesn't crash with nullable relationships
    livewire(PostsTable::class)
        ->sortTable('team.name')
        ->assertCanSeeTableRecords($allPosts)
        ->sortTable('team.name', 'desc')
        ->assertCanSeeTableRecords($allPosts);
});

it('can search records with `BelongsToThrough` relationship', function (): void {
    $searchTeam = 'Unique Team Name';

    $team = Team::factory()->create(['name' => $searchTeam]);
    $matchingUser = User::factory()->create(['team_id' => $team->id]);
    $matchingPost = Post::factory()->for($matchingUser, 'author')->create();

    $nonMatchingPosts = Post::factory()->count(3)->create();

    livewire(PostsTable::class)
        ->searchTable($searchTeam)
        ->assertCanSeeTableRecords([$matchingPost])
        ->assertCanNotSeeTableRecords($nonMatchingPosts);
});

it('can sort records with nested `BelongsToThrough` -> `BelongsTo` relationship', function (): void {
    Post::factory()->count(5)->state(fn (): array => [
        'author_id' => User::factory()->state([
            'team_id' => Team::factory()->state([
                'company_id' => Company::factory(),
            ]),
        ]),
    ])->create();

    $sortedAsc = Post::query()
        ->orderBy(
            Company::query()
                ->select('companies.name')
                ->whereColumn('companies.id', 'teams.company_id')
                ->join('teams', 'teams.company_id', '=', 'companies.id')
                ->join('users', 'users.team_id', '=', 'teams.id')
                ->whereColumn('users.id', 'posts.author_id')
                ->limit(1)
        )
        ->orderBy('posts.id')
        ->get();

    $sortedDesc = Post::query()
        ->orderByDesc(
            Company::query()
                ->select('companies.name')
                ->whereColumn('companies.id', 'teams.company_id')
                ->join('teams', 'teams.company_id', '=', 'companies.id')
                ->join('users', 'users.team_id', '=', 'teams.id')
                ->whereColumn('users.id', 'posts.author_id')
                ->limit(1)
        )
        ->orderBy('posts.id')
        ->get();

    livewire(PostsTable::class)
        ->sortTable('team.company.name')
        ->assertCanSeeTableRecords($sortedAsc, inOrder: true)
        ->sortTable('team.company.name', 'desc')
        ->assertCanSeeTableRecords($sortedDesc, inOrder: true);
});

it('can sort records with nullable nested `BelongsToThrough` -> `BelongsTo` relationship', function (): void {
    // Post with team and company
    $company = Company::factory()->create(['name' => 'Acme Corp']);
    $teamWithCompany = Team::factory()->create(['company_id' => $company->id]);
    $userWithTeamAndCompany = User::factory()->create(['team_id' => $teamWithCompany->id]);
    $postComplete = Post::factory()->create(['author_id' => $userWithTeamAndCompany->id]);

    // Post with team but no company
    $teamWithoutCompany = Team::factory()->create(['company_id' => null]);
    $userWithTeamNoCompany = User::factory()->create(['team_id' => $teamWithoutCompany->id]);
    $postNoCompany = Post::factory()->create(['author_id' => $userWithTeamNoCompany->id]);

    // Post with author but no team
    $userWithoutTeam = User::factory()->create(['team_id' => null]);
    $postNoTeam = Post::factory()->create(['author_id' => $userWithoutTeam->id]);

    // Post with no author
    $postNoAuthor = Post::factory()->create(['author_id' => null]);

    $allPosts = collect([$postComplete, $postNoCompany, $postNoTeam, $postNoAuthor]);

    // Just verify sorting doesn't crash with nullable nested relationships
    livewire(PostsTable::class)
        ->sortTable('team.company.name')
        ->assertCanSeeTableRecords($allPosts)
        ->sortTable('team.company.name', 'desc')
        ->assertCanSeeTableRecords($allPosts);
});

it('can search records with nested `BelongsToThrough` -> `BelongsTo` relationship', function (): void {
    $searchCompany = 'Unique Company Name';

    $company = Company::factory()->create(['name' => $searchCompany]);
    $team = Team::factory()->create(['company_id' => $company->id]);
    $matchingUser = User::factory()->create(['team_id' => $team->id]);
    $matchingPost = Post::factory()->for($matchingUser, 'author')->create();

    $nonMatchingPosts = Post::factory()->count(3)->create();

    livewire(PostsTable::class)
        ->searchTable($searchCompany)
        ->assertCanSeeTableRecords([$matchingPost])
        ->assertCanNotSeeTableRecords($nonMatchingPosts);
});

it('can sort records with `BelongsTo` -> `BelongsToThrough` relationship', function (): void {
    Post::factory()->count(5)->state(fn (): array => [
        'author_id' => User::factory()->state([
            'team_id' => Team::factory()->state([
                'company_id' => Company::factory(),
            ]),
        ]),
    ])->create();

    $sortedAsc = Post::query()
        ->orderBy(
            Company::query()
                ->select('companies.name')
                ->whereColumn('companies.id', 'teams.company_id')
                ->join('teams', 'teams.company_id', '=', 'companies.id')
                ->join('users', 'users.team_id', '=', 'teams.id')
                ->whereColumn('users.id', 'posts.author_id')
                ->limit(1)
        )
        ->orderBy('posts.id')
        ->get();

    $sortedDesc = Post::query()
        ->orderByDesc(
            Company::query()
                ->select('companies.name')
                ->whereColumn('companies.id', 'teams.company_id')
                ->join('teams', 'teams.company_id', '=', 'companies.id')
                ->join('users', 'users.team_id', '=', 'teams.id')
                ->whereColumn('users.id', 'posts.author_id')
                ->limit(1)
        )
        ->orderBy('posts.id')
        ->get();

    livewire(PostsTable::class)
        ->sortTable('author.company.name')
        ->assertCanSeeTableRecords($sortedAsc, inOrder: true)
        ->sortTable('author.company.name', 'desc')
        ->assertCanSeeTableRecords($sortedDesc, inOrder: true);
});

it('can sort records with nullable `BelongsTo` -> `BelongsToThrough` relationship', function (): void {
    // Post with author, team, and company
    $company = Company::factory()->create(['name' => 'Acme Corp']);
    $teamWithCompany = Team::factory()->create(['company_id' => $company->id]);
    $userComplete = User::factory()->create(['team_id' => $teamWithCompany->id]);
    $postComplete = Post::factory()->create(['author_id' => $userComplete->id]);

    // Post with author and team but no company
    $teamWithoutCompany = Team::factory()->create(['company_id' => null]);
    $userNoCompany = User::factory()->create(['team_id' => $teamWithoutCompany->id]);
    $postNoCompany = Post::factory()->create(['author_id' => $userNoCompany->id]);

    // Post with author but no team
    $userNoTeam = User::factory()->create(['team_id' => null]);
    $postNoTeam = Post::factory()->create(['author_id' => $userNoTeam->id]);

    // Post with no author
    $postNoAuthor = Post::factory()->create(['author_id' => null]);

    $allPosts = collect([$postComplete, $postNoCompany, $postNoTeam, $postNoAuthor]);

    // Just verify sorting doesn't crash with nullable relationships
    livewire(PostsTable::class)
        ->sortTable('author.company.name')
        ->assertCanSeeTableRecords($allPosts)
        ->sortTable('author.company.name', 'desc')
        ->assertCanSeeTableRecords($allPosts);
});

it('can search records with `BelongsTo` -> `BelongsToThrough` relationship', function (): void {
    $searchCompany = 'Specific Company Name';

    $company = Company::factory()->create(['name' => $searchCompany]);
    $team = Team::factory()->create(['company_id' => $company->id]);
    $matchingUser = User::factory()->create(['team_id' => $team->id]);
    $matchingPost = Post::factory()->for($matchingUser, 'author')->create();

    $nonMatchingPosts = Post::factory()->count(3)->create();

    livewire(PostsTable::class)
        ->searchTable($searchCompany)
        ->assertCanSeeTableRecords([$matchingPost])
        ->assertCanNotSeeTableRecords($nonMatchingPosts);
});

it('can sort records with `HasOneThrough` relationship', function (): void {
    $themes = ['alpha', 'beta', 'gamma', 'delta', 'epsilon'];

    foreach ($themes as $theme) {
        User::factory()->has(
            Profile::factory()->has(
                Setting::factory()->state(['theme' => $theme]),
                'setting'
            ),
            'profile'
        )->create();
    }

    $sortedAsc = User::query()
        ->orderBy(
            Setting::query()
                ->select('theme')
                ->join('profiles', 'profiles.id', '=', 'settings.profile_id')
                ->whereColumn('profiles.user_id', 'users.id')
                ->limit(1)
        )
        ->orderBy('users.id')
        ->get();

    $sortedDesc = User::query()
        ->orderByDesc(
            Setting::query()
                ->select('theme')
                ->join('profiles', 'profiles.id', '=', 'settings.profile_id')
                ->whereColumn('profiles.user_id', 'users.id')
                ->limit(1)
        )
        ->orderBy('users.id')
        ->get();

    livewire(UsersTable::class)
        ->sortTable('setting.theme')
        ->assertCanSeeTableRecords($sortedAsc, inOrder: true)
        ->sortTable('setting.theme', 'desc')
        ->assertCanSeeTableRecords($sortedDesc, inOrder: true);
});

it('can sort records with nullable `HasOneThrough` relationship', function (): void {
    // User with profile and setting
    $userWithSetting = User::factory()->has(
        Profile::factory()->has(
            Setting::factory()->state(['theme' => 'dark']),
            'setting'
        ),
        'profile'
    )->create();

    // User with profile but no setting
    $userWithProfileNoSetting = User::factory()->has(
        Profile::factory(),
        'profile'
    )->create();

    // User without profile (no setting possible)
    $userWithoutProfile = User::factory()->create();

    $allUsers = collect([$userWithSetting, $userWithProfileNoSetting, $userWithoutProfile]);

    // Just verify sorting doesn't crash with nullable relationships
    livewire(UsersTable::class)
        ->sortTable('setting.theme')
        ->assertCanSeeTableRecords($allUsers)
        ->sortTable('setting.theme', 'desc')
        ->assertCanSeeTableRecords($allUsers);
});

it('can search records with `HasOneThrough` relationship', function (): void {
    $searchTheme = 'unique-theme-for-testing';

    $matchingUser = User::factory()->has(
        Profile::factory()->has(
            Setting::factory()->state(['theme' => $searchTheme]),
            'setting'
        ),
        'profile'
    )->create();

    $nonMatchingUsers = User::factory()->count(3)->create();

    livewire(UsersTable::class)
        ->searchTable($searchTheme)
        ->assertCanSeeTableRecords([$matchingUser])
        ->assertCanNotSeeTableRecords($nonMatchingUsers);
});

it('can sort records with `HasOneThrough` relationship using different column', function (): void {
    $languages = ['de', 'en', 'es', 'fr', 'it'];

    foreach ($languages as $language) {
        User::factory()->has(
            Profile::factory()->has(
                Setting::factory()->state(['language' => $language]),
                'setting'
            ),
            'profile'
        )->create();
    }

    $sortedAsc = User::query()
        ->orderBy(
            Setting::query()
                ->select('language')
                ->join('profiles', 'profiles.id', '=', 'settings.profile_id')
                ->whereColumn('profiles.user_id', 'users.id')
                ->limit(1)
        )
        ->orderBy('users.id')
        ->get();

    $sortedDesc = User::query()
        ->orderByDesc(
            Setting::query()
                ->select('language')
                ->join('profiles', 'profiles.id', '=', 'settings.profile_id')
                ->whereColumn('profiles.user_id', 'users.id')
                ->limit(1)
        )
        ->orderBy('users.id')
        ->get();

    livewire(UsersTable::class)
        ->sortTable('setting.language')
        ->assertCanSeeTableRecords($sortedAsc, inOrder: true)
        ->sortTable('setting.language', 'desc')
        ->assertCanSeeTableRecords($sortedDesc, inOrder: true);
});

it('can search records with `HasOneThrough` relationship using different column', function (): void {
    $searchLanguage = 'unique-language';

    $matchingUser = User::factory()->has(
        Profile::factory()->has(
            Setting::factory()->state(['language' => $searchLanguage]),
            'setting'
        ),
        'profile'
    )->create();

    $nonMatchingUsers = User::factory()->count(3)->create();

    livewire(UsersTable::class)
        ->searchTable($searchLanguage)
        ->assertCanSeeTableRecords([$matchingUser])
        ->assertCanNotSeeTableRecords($nonMatchingUsers);
});

it('can sort records with `BelongsTo` -> `HasOneThrough` relationship', function (): void {
    $themes = ['alpha', 'beta', 'gamma', 'delta', 'epsilon'];

    foreach ($themes as $theme) {
        $user = User::factory()->has(
            Profile::factory()->has(
                Setting::factory()->state(['theme' => $theme]),
                'setting'
            ),
            'profile'
        )->create();

        Post::factory()->create(['author_id' => $user->id]);
    }

    $sortedAsc = Post::query()
        ->orderBy(
            Setting::query()
                ->select('theme')
                ->join('profiles', 'profiles.id', '=', 'settings.profile_id')
                ->join('users', 'users.id', '=', 'profiles.user_id')
                ->whereColumn('users.id', 'posts.author_id')
                ->limit(1)
        )
        ->orderBy('posts.id')
        ->get();

    $sortedDesc = Post::query()
        ->orderByDesc(
            Setting::query()
                ->select('theme')
                ->join('profiles', 'profiles.id', '=', 'settings.profile_id')
                ->join('users', 'users.id', '=', 'profiles.user_id')
                ->whereColumn('users.id', 'posts.author_id')
                ->limit(1)
        )
        ->orderBy('posts.id')
        ->get();

    livewire(PostsTable::class)
        ->sortTable('author.setting.theme')
        ->assertCanSeeTableRecords($sortedAsc, inOrder: true)
        ->sortTable('author.setting.theme', 'desc')
        ->assertCanSeeTableRecords($sortedDesc, inOrder: true);
});

it('can sort records with nullable `BelongsTo` -> `HasOneThrough` relationship', function (): void {
    // Post with author that has setting
    $userWithSetting = User::factory()->has(
        Profile::factory()->has(
            Setting::factory()->state(['theme' => 'dark']),
            'setting'
        ),
        'profile'
    )->create();
    $postWithSetting = Post::factory()->create(['author_id' => $userWithSetting->id]);

    // Post with author with profile but no setting
    $userWithProfileNoSetting = User::factory()->has(
        Profile::factory(),
        'profile'
    )->create();
    $postNoSetting = Post::factory()->create(['author_id' => $userWithProfileNoSetting->id]);

    // Post with author but no profile
    $userNoProfile = User::factory()->create();
    $postNoProfile = Post::factory()->create(['author_id' => $userNoProfile->id]);

    // Post with no author
    $postNoAuthor = Post::factory()->create(['author_id' => null]);

    $allPosts = collect([$postWithSetting, $postNoSetting, $postNoProfile, $postNoAuthor]);

    // Just verify sorting doesn't crash with nullable nested relationships
    livewire(PostsTable::class)
        ->sortTable('author.setting.theme')
        ->assertCanSeeTableRecords($allPosts)
        ->sortTable('author.setting.theme', 'desc')
        ->assertCanSeeTableRecords($allPosts);
});

it('can search records with `BelongsTo` -> `HasOneThrough` relationship', function (): void {
    $searchTheme = 'unique-post-author-theme';

    $matchingUser = User::factory()->has(
        Profile::factory()->has(
            Setting::factory()->state(['theme' => $searchTheme]),
            'setting'
        ),
        'profile'
    )->create();

    $matchingPost = Post::factory()->for($matchingUser, 'author')->create();
    $nonMatchingPosts = Post::factory()->count(3)->create();

    livewire(PostsTable::class)
        ->searchTable($searchTheme)
        ->assertCanSeeTableRecords([$matchingPost])
        ->assertCanNotSeeTableRecords($nonMatchingPosts);
});

it('can search records using table-level searchable columns', function (): void {
    $posts = Post::factory()->count(10)->create();

    $title = $posts->first()->title;

    livewire(PostsTableWithTableSearchableColumns::class)
        ->searchTable($title)
        ->assertCanSeeTableRecords($posts->where('title', $title))
        ->assertCanNotSeeTableRecords($posts->where('title', '!=', $title));
});

it('can search records using table-level searchable columns with relationship', function (): void {
    $posts = Post::factory()->count(10)->create();

    $author = $posts->first()->author->name;

    livewire(PostsTableWithTableSearchableColumns::class)
        ->searchTable($author)
        ->assertCanSeeTableRecords($posts->where('author.name', $author))
        ->assertCanNotSeeTableRecords($posts->where('author.name', '!=', $author));
});

it('can search records using table-level searchable columns case insensitively', function (): void {
    $matchingPost = Post::factory()->create([
        'title' => 'Test Post Title',
    ]);

    $nonMatchingPosts = Post::factory()->count(3)->create();

    livewire(PostsTableWithTableSearchableColumns::class)
        ->searchTable('test post title')
        ->assertCanSeeTableRecords([$matchingPost])
        ->assertCanNotSeeTableRecords($nonMatchingPosts);
});

it('can search records using table-level searchable columns with relationship case insensitively', function (): void {
    $matchingAuthor = User::factory()->create(['name' => 'John Smith']);

    $matchingPosts = Post::factory()
        ->for($matchingAuthor, 'author')
        ->count(3)
        ->create();

    $nonMatchingPosts = Post::factory()->count(3)->create();

    livewire(PostsTableWithTableSearchableColumns::class)
        ->searchTable('john smith')
        ->assertCanSeeTableRecords($matchingPosts)
        ->assertCanNotSeeTableRecords($nonMatchingPosts);
});
