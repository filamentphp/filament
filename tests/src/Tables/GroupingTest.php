<?php

use Filament\Tables\Grouping\Group;
use Filament\Tests\Fixtures\Livewire\GroupedCustomDataTable;
use Filament\Tests\Fixtures\Livewire\PostsTable;
use Filament\Tests\Fixtures\Livewire\PostsTableWithoutSummarizers;
use Filament\Tests\Fixtures\Livewire\TicketMessagesTable;
use Filament\Tests\Fixtures\Livewire\UsersTable;
use Filament\Tests\Fixtures\Models\Company;
use Filament\Tests\Fixtures\Models\Image;
use Filament\Tests\Fixtures\Models\Language;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Profile;
use Filament\Tests\Fixtures\Models\Setting;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\Fixtures\Models\TicketMessage;
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
                ->getGrouping()->toBeInstanceOf(Group::class)
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

it('can group records by `HasOneThrough` relationship', function (): void {
    // Create users with profiles that have settings with unique themes
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
                ->join('profiles', 'profiles.id', '=', 'settings.profile_id')
                ->whereColumn('profiles.user_id', 'users.id')
                ->limit(1)
        )
        ->orderBy('users.id')
        ->get();

    livewire(UsersTable::class)
        ->set('tableGrouping', 'setting.theme')
        ->assertCanSeeTableRecords($sortedUsers, inOrder: true);
});

it('can group records with nullable `HasOneThrough` relationship', function (): void {
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

    // Just verify grouping doesn't crash with nullable relationships
    livewire(UsersTable::class)
        ->set('tableGrouping', 'setting.theme')
        ->assertCanSeeTableRecords($allUsers);
});

it('can group records by `BelongsTo` -> `HasOneThrough` relationship', function (): void {
    // Create posts with authors that have settings via HasOneThrough
    $themes = ['alpha-theme', 'beta-theme', 'gamma-theme', 'delta-theme', 'epsilon-theme'];
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

    $sortedPosts = Post::query()
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

    livewire(PostsTable::class)
        ->set('tableGrouping', 'author.setting.theme')
        ->assertCanSeeTableRecords($sortedPosts, inOrder: true);
});

it('can group records with nullable `BelongsTo` -> `HasOneThrough` relationship', function (): void {
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

    // Just verify grouping doesn't crash with nullable nested relationships
    livewire(PostsTable::class)
        ->set('tableGrouping', 'author.setting.theme')
        ->assertCanSeeTableRecords($allPosts);
});

it('can handle array records in `getKey()`', function (): void {
    $livewire = livewire(PostsTable::class)->instance();
    $group = Group::make('status')->table($livewire->getTable());

    $arrayRecord = ['__key' => '1', 'name' => 'John', 'status' => 'active'];

    expect($group->getKey($arrayRecord))->toBe('active');
});

it('can handle array records in `getStringKey()`', function (): void {
    $livewire = livewire(PostsTable::class)->instance();
    $group = Group::make('status')->table($livewire->getTable());

    $arrayRecord = ['__key' => '1', 'name' => 'John', 'status' => 'active'];

    expect($group->getStringKey($arrayRecord))->toBe('active');
});

it('can handle array records in `getTitle()`', function (): void {
    $livewire = livewire(PostsTable::class)->instance();
    $group = Group::make('status')->table($livewire->getTable());

    $arrayRecord = ['__key' => '1', 'name' => 'John', 'status' => 'active'];

    expect($group->getTitle($arrayRecord))->toBe('active');
});

it('can handle array records in `getDescription()`', function (): void {
    $livewire = livewire(PostsTable::class)->instance();
    $group = Group::make('status')
        ->getDescriptionFromRecordUsing(fn (array $record): string => 'User: ' . $record['name'])
        ->table($livewire->getTable());

    $arrayRecord = ['__key' => '1', 'name' => 'John', 'status' => 'active'];

    expect($group->getDescription($arrayRecord, 'Active'))->toBe('User: John');
});

it('can use custom `getKeyFromRecordUsing()` with array records', function (): void {
    $livewire = livewire(PostsTable::class)->instance();
    $group = Group::make('status')
        ->getKeyFromRecordUsing(fn (array $record): string => strtoupper($record['status']))
        ->table($livewire->getTable());

    $arrayRecord = ['__key' => '1', 'name' => 'John', 'status' => 'active'];

    expect($group->getKey($arrayRecord))->toBe('ACTIVE')
        ->and($group->getStringKey($arrayRecord))->toBe('ACTIVE');
});

it('can use custom `getTitleFromRecordUsing()` with array records', function (): void {
    $livewire = livewire(PostsTable::class)->instance();
    $group = Group::make('status')
        ->getTitleFromRecordUsing(fn (array $record): string => 'Status: ' . ucfirst($record['status']))
        ->table($livewire->getTable());

    $arrayRecord = ['__key' => '1', 'name' => 'John', 'status' => 'active'];

    expect($group->getTitle($arrayRecord))->toBe('Status: Active');
});

it('can get grouped selectable record keys for array tables', function (): void {
    livewire(GroupedCustomDataTable::class)
        ->set('tableGrouping', 'status')
        ->tap(function (Testable $testable): void {
            /** @var GroupedCustomDataTable $livewire */
            $livewire = $testable->instance();

            $activeKeys = $livewire->getGroupedSelectableTableRecordKeys('active');
            $inactiveKeys = $livewire->getGroupedSelectableTableRecordKeys('inactive');

            expect($activeKeys)
                ->toHaveCount(3)
                ->each->toBeString()
                ->and($inactiveKeys)
                ->toHaveCount(2)
                ->each->toBeString();
        });
});

it('returns an empty array for a non-existent group in array tables', function (): void {
    livewire(GroupedCustomDataTable::class)
        ->set('tableGrouping', 'status')
        ->tap(function (Testable $testable): void {
            /** @var GroupedCustomDataTable $livewire */
            $livewire = $testable->instance();

            $keys = $livewire->getGroupedSelectableTableRecordKeys('nonexistent');

            expect($keys)->toBeEmpty();
        });
});

it('can set `collapsible()` and get with `isCollapsible()`', function (): void {
    expect(Group::make('status')->collapsible()->isCollapsible())->toBeTrue();
});

it('defaults `isCollapsible()` to `false`', function (): void {
    expect(Group::make('status')->isCollapsible())->toBeFalse();
});

it('can set `column()` and get with `getColumn()`', function (): void {
    expect(Group::make('status')->column('custom_column')->getColumn())->toBe('custom_column');
});

it('returns `getId()` from `getColumn()` when no custom column is set', function (): void {
    expect(Group::make('status')->getColumn())->toBe('status');
});

it('can set `date()` and get with `isDate()`', function (): void {
    expect(Group::make('created_at')->date()->isDate())->toBeTrue();
});

it('defaults `isDate()` to `false`', function (): void {
    expect(Group::make('created_at')->isDate())->toBeFalse();
});

it('can set `label()` and get with `getLabel()`', function (): void {
    expect(Group::make('status')->label('Status Group')->getLabel())->toBe('Status Group');
});

it('generates a default label from `getId()` when no label is set', function (): void {
    expect(Group::make('author_name')->getLabel())->toBe('Author name');
});

it('can set `titlePrefixedWithLabel()` to `false` and get with `isTitlePrefixedWithLabel()`', function (): void {
    expect(Group::make('status')->titlePrefixedWithLabel(false)->isTitlePrefixedWithLabel())->toBeFalse();
});

it('defaults `isTitlePrefixedWithLabel()` to `true`', function (): void {
    expect(Group::make('status')->isTitlePrefixedWithLabel())->toBeTrue();
});

it('can set `id()` and get with `getId()`', function (): void {
    $group = Group::make('status')
        ->id('custom-id');

    expect($group->getId())->toBe('custom-id');
});

it('can set `label()` with a `Closure`', function (): void {
    $group = Group::make('status')
        ->label(static fn (): string => 'Dynamic Label');

    expect($group->getLabel())->toBe('Dynamic Label');
});

it('returns fluent `$this` from callback setters', function (): void {
    $group = Group::make('status');

    expect($group->getDescriptionFromRecordUsing(static fn () => 'desc'))->toBe($group);
    expect($group->getDescriptionUsing(static fn () => 'desc'))->toBe($group);
    expect($group->getTitleFromRecordUsing(static fn () => 'title'))->toBe($group);
    expect($group->getKeyFromRecordUsing(static fn () => 'key'))->toBe($group);
    expect($group->groupQueryUsing(static fn ($query) => $query))->toBe($group);
    expect($group->orderQueryUsing(static fn ($query) => $query))->toBe($group);
    expect($group->scopeQueryUsing(static fn ($query) => $query))->toBe($group);
    expect($group->scopeQueryByKeyUsing(static fn ($query) => $query))->toBe($group);
});

it('can group records by `BelongsTo` relationship that uses `withTrashed()` when the related model is soft-deleted', function (): void {
    $ticketAlpha = Ticket::factory()->create();
    $ticketBeta = Ticket::factory()->create();
    $ticketGamma = Ticket::factory()->create();

    TicketMessage::factory()->create(['ticket_id' => $ticketBeta->id]);
    TicketMessage::factory()->create(['ticket_id' => $ticketAlpha->id]);
    TicketMessage::factory()->create(['ticket_id' => $ticketGamma->id]);
    TicketMessage::factory()->create(['ticket_id' => $ticketAlpha->id]);
    TicketMessage::factory()->create(['ticket_id' => $ticketBeta->id]);

    $ticketGamma->delete();

    $sortedMessages = TicketMessage::query()
        ->orderBy(
            Ticket::query()
                ->select('id')
                ->withTrashed()
                ->whereColumn('tickets.id', 'ticket_messages.ticket_id')
                ->limit(1)
        )
        ->orderBy('ticket_messages.id')
        ->get();

    livewire(TicketMessagesTable::class)
        ->set('tableGrouping', 'ticket.id')
        ->assertCanSeeTableRecords($sortedMessages, inOrder: true);
});

it('can group records by `HasOne` relationship', function (): void {
    $bios = ['Alpha bio', 'Beta bio', 'Gamma bio', 'Delta bio', 'Epsilon bio'];
    foreach ($bios as $bio) {
        User::factory()->has(
            Profile::factory()->state(['bio' => $bio]),
            'profile'
        )->create();
    }

    $sortedUsers = User::query()
        ->orderBy(
            Profile::query()
                ->select('bio')
                ->whereColumn('profiles.user_id', 'users.id')
                ->limit(1)
        )
        ->orderBy('users.id')
        ->get();

    livewire(UsersTable::class)
        ->set('tableGrouping', 'profile.bio')
        ->assertCanSeeTableRecords($sortedUsers, inOrder: true);
});

it('can group records by `BelongsToThrough` relationship', function (): void {
    $companyNames = ['Alpha Co', 'Beta Co', 'Gamma Co', 'Delta Co', 'Epsilon Co'];
    foreach ($companyNames as $companyName) {
        $company = Company::factory()->create(['name' => $companyName]);
        $team = Team::factory()->create(['company_id' => $company->id]);
        User::factory()->create(['team_id' => $team->id]);
    }

    $sortedUsers = User::query()
        ->orderBy(
            Company::query()
                ->select('companies.name')
                ->join('teams', 'teams.company_id', '=', 'companies.id')
                ->whereColumn('teams.id', 'users.team_id')
                ->limit(1)
        )
        ->orderBy('users.id')
        ->get();

    livewire(UsersTable::class)
        ->set('tableGrouping', 'company.name')
        ->assertCanSeeTableRecords($sortedUsers, inOrder: true);
});

it('can group records by `BelongsTo` -> `BelongsToThrough` relationship', function (): void {
    $companyNames = ['Acme Corp', 'Beta Inc', 'Gamma LLC', 'Delta Co', 'Epsilon Ltd'];
    foreach ($companyNames as $companyName) {
        $company = Company::factory()->create(['name' => $companyName]);
        $team = Team::factory()->create(['company_id' => $company->id]);
        $user = User::factory()->create(['team_id' => $team->id]);
        Post::factory()->create(['author_id' => $user->id]);
    }

    $sortedPosts = Post::query()
        ->orderBy(
            Company::query()
                ->select('companies.name')
                ->join('teams', 'teams.company_id', '=', 'companies.id')
                ->join('users', 'users.team_id', '=', 'teams.id')
                ->whereColumn('users.id', 'posts.author_id')
                ->limit(1)
        )
        ->orderBy('posts.id')
        ->get();

    livewire(PostsTableWithoutSummarizers::class)
        ->set('tableGrouping', 'author.company.name')
        ->assertCanSeeTableRecords($sortedPosts, inOrder: true);
});

it('can group records with nullable `BelongsTo` -> `BelongsToThrough` relationship', function (): void {
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

    livewire(PostsTableWithoutSummarizers::class)
        ->set('tableGrouping', 'author.company.name')
        ->assertCanSeeTableRecords($allPosts);
});

it('can group records by `HasOneThrough` -> `BelongsTo` relationship', function (): void {
    $languageNames = ['Alpha Lang', 'Beta Lang', 'Gamma Lang', 'Delta Lang', 'Epsilon Lang'];
    foreach ($languageNames as $languageName) {
        $language = Language::factory()->create(['name' => $languageName]);
        User::factory()->has(
            Profile::factory()->has(
                Setting::factory()->state(['language_id' => $language->id]),
                'setting'
            ),
            'profile'
        )->create();
    }

    $sortedUsers = User::query()
        ->orderBy(
            Language::query()
                ->select('languages.name')
                ->join('settings', 'settings.language_id', '=', 'languages.id')
                ->join('profiles', 'profiles.id', '=', 'settings.profile_id')
                ->whereColumn('profiles.user_id', 'users.id')
                ->limit(1)
        )
        ->orderBy('users.id')
        ->get();

    livewire(UsersTable::class)
        ->set('tableGrouping', 'setting.language.name')
        ->assertCanSeeTableRecords($sortedUsers, inOrder: true);
});

it('can group records with nullable `HasOneThrough` -> `BelongsTo` relationship', function (): void {
    // User with full chain
    $language = Language::factory()->create(['name' => 'English']);
    $userWithLanguage = User::factory()->has(
        Profile::factory()->has(
            Setting::factory()->state(['language_id' => $language->id]),
            'setting'
        ),
        'profile'
    )->create();

    // User with profile and setting but no language
    $userWithSettingNoLanguage = User::factory()->has(
        Profile::factory()->has(
            Setting::factory()->state(['language_id' => null]),
            'setting'
        ),
        'profile'
    )->create();

    // User with profile but no setting
    $userWithProfileNoSetting = User::factory()->has(
        Profile::factory(),
        'profile'
    )->create();

    // User without profile
    $userWithoutProfile = User::factory()->create();

    $allUsers = collect([$userWithLanguage, $userWithSettingNoLanguage, $userWithProfileNoSetting, $userWithoutProfile]);

    livewire(UsersTable::class)
        ->set('tableGrouping', 'setting.language.name')
        ->assertCanSeeTableRecords($allUsers);
});

it('can group records by `BelongsTo` -> `HasOneThrough` -> `BelongsTo` relationship', function (): void {
    $languageNames = ['Alpha Lang', 'Beta Lang', 'Gamma Lang', 'Delta Lang', 'Epsilon Lang'];
    foreach ($languageNames as $languageName) {
        $language = Language::factory()->create(['name' => $languageName]);
        $user = User::factory()->has(
            Profile::factory()->has(
                Setting::factory()->state(['language_id' => $language->id]),
                'setting'
            ),
            'profile'
        )->create();
        Post::factory()->create(['author_id' => $user->id]);
    }

    $sortedPosts = Post::query()
        ->orderBy(
            Language::query()
                ->select('languages.name')
                ->join('settings', 'settings.language_id', '=', 'languages.id')
                ->join('profiles', 'profiles.id', '=', 'settings.profile_id')
                ->join('users', 'users.id', '=', 'profiles.user_id')
                ->whereColumn('users.id', 'posts.author_id')
                ->limit(1)
        )
        ->orderBy('posts.id')
        ->get();

    livewire(PostsTable::class)
        ->set('tableGrouping', 'author.setting.language.name')
        ->assertCanSeeTableRecords($sortedPosts, inOrder: true);
});

it('can group records by `HasOne` relationship that uses a custom `where()` constraint', function (): void {
    // User 1: has both published and unpublished posts; should group by published
    $user1 = User::factory()->create();
    Post::factory()->create(['author_id' => $user1->id, 'is_published' => false, 'title' => 'Z Unpublished']);
    Post::factory()->create(['author_id' => $user1->id, 'is_published' => true, 'title' => 'A Published']);

    // User 2: only has unpublished posts; should group as null
    $user2 = User::factory()->create();
    Post::factory()->create(['author_id' => $user2->id, 'is_published' => false, 'title' => 'B Unpublished']);

    // User 3: has a published post
    $user3 = User::factory()->create();
    Post::factory()->create(['author_id' => $user3->id, 'is_published' => true, 'title' => 'M Published']);

    $sortedUsers = User::query()
        ->orderBy(
            Post::query()
                ->select('posts.title')
                ->whereColumn('posts.author_id', 'users.id')
                ->where('posts.is_published', true)
                ->limit(1)
        )
        ->orderBy('users.id')
        ->get();

    livewire(UsersTable::class)
        ->set('tableGrouping', 'publishedPost.title')
        ->assertCanSeeTableRecords($sortedUsers, inOrder: true);
});

it('can group records by `HasOneThrough` -> `BelongsTo` relationship that uses `withTrashed()` when the related model is soft-deleted', function (): void {
    $alphaLanguage = Language::factory()->create(['name' => 'Alpha Lang']);
    $betaLanguage = Language::factory()->create(['name' => 'Beta Lang']);
    $gammaLanguage = Language::factory()->create(['name' => 'Gamma Lang']);

    foreach ([$alphaLanguage, $betaLanguage, $gammaLanguage] as $language) {
        User::factory()->has(
            Profile::factory()->has(
                Setting::factory()->state(['language_id' => $language->id]),
                'setting'
            ),
            'profile'
        )->create();
    }

    // Soft-delete the Gamma language
    $gammaLanguage->delete();

    $sortedUsers = User::query()
        ->orderBy(
            Language::query()
                ->withTrashed()
                ->select('languages.name')
                ->join('settings', 'settings.language_id', '=', 'languages.id')
                ->join('profiles', 'profiles.id', '=', 'settings.profile_id')
                ->whereColumn('profiles.user_id', 'users.id')
                ->limit(1)
        )
        ->orderBy('users.id')
        ->get();

    livewire(UsersTable::class)
        ->set('tableGrouping', 'setting.languageWithTrashed.name')
        ->assertCanSeeTableRecords($sortedUsers, inOrder: true);
});
