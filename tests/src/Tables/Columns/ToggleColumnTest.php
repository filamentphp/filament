<?php

namespace Filament\Tests\Tables\Columns;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Tables\TestCase;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\HtmlString;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render', function (): void {
    Post::factory()->count(5)->create();

    livewire(TestTableWithToggleColumn::class)
        ->assertSuccessful()
        ->assertCanRenderTableColumn('is_published');
});

it('can display true state', function (): void {
    Post::factory()->create(['is_published' => true]);

    livewire(TestTableWithToggleColumn::class)
        ->assertSuccessful();
});

it('can display false state', function (): void {
    Post::factory()->create(['is_published' => false]);

    livewire(TestTableWithToggleColumn::class)
        ->assertSuccessful();
});

it('injects boolean relationship state into `tooltip()`', function (mixed $state, string $expectedState): void {
    $author = User::factory()->create(['json' => ['is_active' => $state]]);
    $post = Post::factory()->create(['author_id' => $author->getKey()]);

    livewire(TestTableWithRelationshipToggleColumn::class)
        ->assertSuccessful()
        ->assertSee("bool-{$expectedState}-{$author->getKey()}-{$post->getKey()}", escape: false);
})->with([
    'null' => [null, 'false'],
    'false' => [false, 'false'],
    'true' => [true, 'true'],
    'zero' => [0, 'false'],
    'one' => [1, 'true'],
]);

it('renders one error-aware toggle tooltip with or without a configured hint', function (bool $hasTooltip): void {
    $post = Post::factory()->create();
    $column = livewire(TestTableWithToggleColumn::class)->instance()->getTable()->getColumn('is_published');
    $html = $column->record($post)->tooltip($hasTooltip ? new HtmlString('<strong>Publish</strong>') : null)->toEmbeddedHtml();

    expect(substr_count($html, 'x-tooltip='))->toBe(1)
        ->and($html)->toContain('error === undefined ? ' . ($hasTooltip ? '{' : 'false'))
        ->toContain('content: error')
        ->toContain('allowHTML: false');
})->with([false, true]);

it('prioritizes toggle errors and restores the configured tooltip after a successful save', function (): void {
    Artisan::call('filament:assets');
    $author = User::factory()->create(['name' => 'Alex Morgan', 'json' => ['is_subscribed' => null]]);
    Post::factory()->create(['author_id' => $author->getKey()]);
    $this->actingAs(User::factory()->create());

    foreach ([false, true] as $isDarkMode) {
        $author->refresh()->update(['json' => ['is_subscribed' => null]]);
        $page = visit('/columns-browser-test');

        if ($isDarkMode) {
            $page = $page->inDarkMode();
        }

        $page->assertAttribute('[data-testid="author-subscribed-toggle"] [role="switch"]', 'aria-checked', 'false')
            ->assertScript('typeof Alpine.$data(document.querySelector(\'[data-testid="author-subscribed-toggle"]\')).getServerState === "function"');
        $page->script('document.querySelector(\'[data-testid="author-subscribed-toggle"] [role="switch"]\').focus()');

        $page
            ->assertVisible('[role="tooltip"] strong')
            ->assertScript('document.querySelector(\'[role="tooltip"]\').textContent', 'Update subscription for Alex Morgan')
            ->assertScript('document.activeElement.hasAttribute("aria-describedby")')
            ->click('[data-testid="author-subscribed-toggle"] [role="switch"]')
            ->assertScript('Alpine.$data(document.querySelector(\'[data-testid="author-subscribed-toggle"]\')).error', 'Approval <em>required</em>.')
            ->hover('[data-testid="enum-label-column"]')
            ->hover('[data-testid="author-subscribed-toggle"] [role="switch"]')
            ->assertScript('Array.from(document.querySelectorAll(\'[role="tooltip"]\')).filter(element => getComputedStyle(element).visibility === "visible").map(element => element.textContent)', ['Approval <em>required</em>.'])
            ->assertMissing('[role="tooltip"] em')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        expect($author->fresh()->json['is_subscribed'])->toBeNull();

        if ($page->script('Alpine.$data(document.querySelector(\'[data-testid="author-subscribed-toggle"]\')).state')) {
            $page->click('[data-testid="author-subscribed-toggle"] [role="switch"]')
                ->assertAttribute('[data-testid="author-subscribed-toggle"] [role="switch"]', 'aria-checked', 'false');
        }

        $page
            ->click('[data-testid="author-subscribed-toggle"] [role="switch"]')
            ->assertScript('Alpine.$data(document.querySelector(\'[data-testid="author-subscribed-toggle"]\')).error === undefined')
            ->hover('[data-testid="enum-label-column"]')
            ->hover('[data-testid="author-subscribed-toggle"] [role="switch"]')
            ->assertVisible('[role="tooltip"] strong')
            ->assertScript('Array.from(document.querySelectorAll(\'[role="tooltip"]\')).filter(element => getComputedStyle(element).visibility === "visible").map(element => element.textContent)', ['Update subscription for Alex Morgan'])
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        expect($author->fresh()->json['is_subscribed'])->toBeTrue();
    }
});

class TestTableWithToggleColumn extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\ToggleColumn::make('is_published'),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class TestTableWithRelationshipToggleColumn extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->columns([
                Tables\Columns\ToggleColumn::make('author.json.is_active')
                    ->tooltip(static fn (mixed $state, User $relatedRecord, Post $record): string => get_debug_type($state) . '-' . ($state ? 'true' : 'false') . "-{$relatedRecord->getKey()}-{$record->getKey()}"),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
