<?php

use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Testing\TestAction;
use Filament\Tests\Fixtures\Livewire\PostsTableWithCustomLayout;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Tables\TestCase;
use Illuminate\Support\Facades\Artisan;

use function Filament\Tests\livewire;
use function Pest\Laravel\assertSoftDeleted;

uses(TestCase::class);

it('renders the parts inside the custom layout without the frame', function (): void {
    Post::factory()->count(3)->create();

    $html = livewire(PostsTableWithCustomLayout::class)->html();

    expect($html)
        ->toContain('class="fi-sc-section')
        ->toContain('class="fi-ta-filters"')
        ->toContain('class="fi-ta-search-field"')
        ->toContain('class="fi-ta-table"')
        ->not->toContain('fi-ta-ctn');
});

it('can list records', function (): void {
    $posts = Post::factory()->count(10)->create();

    livewire(PostsTableWithCustomLayout::class)
        ->assertCanSeeTableRecords($posts);
});

it('can search records', function (): void {
    $posts = Post::factory()->count(10)->create();

    $title = $posts->first()->title;

    livewire(PostsTableWithCustomLayout::class)
        ->searchTable($title)
        ->assertCanSeeTableRecords($posts->where('title', $title))
        ->assertCanNotSeeTableRecords($posts->where('title', '!=', $title));
});

it('can sort records', function (): void {
    $posts = Post::factory()->count(10)->create();

    livewire(PostsTableWithCustomLayout::class)
        ->sortTable('title')
        ->assertCanSeeTableRecords($posts->sortBy('title'), inOrder: true)
        ->sortTable('title', 'desc')
        ->assertCanSeeTableRecords($posts->sortByDesc('title'), inOrder: true);
});

it('can filter records', function (): void {
    $posts = Post::factory()->count(10)->create();

    livewire(PostsTableWithCustomLayout::class)
        ->assertCanSeeTableRecords($posts)
        ->filterTable('is_published')
        ->assertCanSeeTableRecords($posts->where('is_published', true))
        ->assertCanNotSeeTableRecords($posts->where('is_published', false));
});

it('can select records and call a bulk action', function (): void {
    $posts = Post::factory()->count(10)->create();

    livewire(PostsTableWithCustomLayout::class)
        ->selectTableRecords($posts)
        ->callAction(TestAction::make(DeleteBulkAction::class)->table()->bulk());

    foreach ($posts as $post) {
        assertSoftDeleted($post);
    }
});

it('can paginate records', function (): void {
    $posts = Post::factory()->count(15)->create();

    livewire(PostsTableWithCustomLayout::class)
        ->assertCanSeeTableRecords($posts->take(10))
        ->assertCanNotSeeTableRecords($posts->skip(10))
        ->call('gotoPage', 2)
        ->assertCanSeeTableRecords($posts->skip(10))
        ->assertCanNotSeeTableRecords($posts->take(10));
});

it('searches, filters and selects records in the browser', function (): void {
    retry(10, function (): void {
        Artisan::call('filament:assets');

        $this->actingAs(User::factory()->create());

        $posts = Post::factory()->count(3)->sequence(
            ['title' => 'Alpha post', 'is_published' => true],
            ['title' => 'Bravo post', 'is_published' => false],
            ['title' => 'Charlie post', 'is_published' => true],
        )->create();

        visit('/custom-table-layout-browser-test')
            ->assertSee('Alpha post')
            ->assertSee('Bravo post')
            ->fill('.fi-ta-header-toolbar .fi-ta-search-field input', 'Alpha')
            ->wait(1)
            ->assertSee('Alpha post')
            ->assertDontSee('Bravo post')
            ->fill('.fi-ta-header-toolbar .fi-ta-search-field input', '')
            ->wait(1)
            ->assertSee('Bravo post')
            ->click('.fi-ta-filters input[type="checkbox"]')
            ->click('.fi-ta-filters-actions-ctn button')
            ->wait(1)
            ->assertSee('Alpha post')
            ->assertDontSee('Bravo post')
            ->assertSee('Is published')
            ->click('.fi-ta-record-checkbox >> nth=0')
            ->wait(1)
            ->assertSee('1 record selected')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        visit('/custom-table-layout-browser-test')
            ->inDarkMode()
            ->assertNoAccessibilityIssues();
    });
});
