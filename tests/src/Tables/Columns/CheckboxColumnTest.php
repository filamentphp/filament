<?php

namespace Filament\Tests\Tables\Columns;

use DOMDocument;
use DOMXPath;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Tables\TestCase;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\HtmlString;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render', function (): void {
    Post::factory()->count(5)->create();

    livewire(TestTableWithCheckboxColumn::class)
        ->assertSuccessful()
        ->assertCanRenderTableColumn('is_published');
});

it('can display checked state', function (): void {
    Post::factory()->create(['is_published' => true]);

    livewire(TestTableWithCheckboxColumn::class)
        ->assertSuccessful();
});

it('can display unchecked state', function (): void {
    Post::factory()->create(['is_published' => false]);

    livewire(TestTableWithCheckboxColumn::class)
        ->assertSuccessful();
});

it('injects boolean relationship state into `tooltip()`', function (mixed $state, string $expectedState): void {
    $author = User::factory()->create(['json' => ['is_active' => $state]]);
    $post = Post::factory()->create(['author_id' => $author->getKey()]);

    livewire(TestTableWithRelationshipCheckboxColumn::class)
        ->assertSuccessful()
        ->assertSee("bool-{$expectedState}-{$author->getKey()}-{$post->getKey()}", escape: false);
})->with([
    'null' => [null, 'false'],
    'false' => [false, 'false'],
    'true' => [true, 'true'],
    'zero' => [0, 'false'],
    'one' => [1, 'true'],
]);

it('renders one reactive tooltip with a configured fallback', function (string | Htmlable | null $tooltip, string $expectedFallback): void {
    $post = Post::factory()->create();
    $column = livewire(TestTableWithCheckboxColumn::class)->instance()->getTable()->getColumn('is_published');
    $html = $column->record($post)->tooltip($tooltip)->toEmbeddedHtml();

    $document = new DOMDocument;
    $document->loadHTML($html);
    $xpath = new DOMXPath($document);
    $expression = $xpath->query('//input[@type="checkbox"]')->item(0)->getAttribute('x-tooltip');

    expect(substr_count($html, 'x-tooltip='))->toBe(1)
        ->and($expression)->toContain('error === undefined ? ' . $expectedFallback)
        ->toContain('content: error')
        ->toContain('allowHTML: false');
})->with([
    'no tooltip' => [null, 'false'],
    'plain text' => ['Publish this post', '{'],
    'HTML' => [new HtmlString('<strong>Publish this post</strong>'), '{'],
]);

it('prioritizes checkbox errors and restores the configured tooltip after a successful save', function (): void {
    Artisan::call('filament:assets');
    $author = User::factory()->create(['name' => 'Alex Morgan', 'json' => ['is_active' => null]]);
    Post::factory()->create(['author_id' => $author->getKey()]);
    $this->actingAs(User::factory()->create());

    foreach ([false, true] as $isDarkMode) {
        $author->refresh()->update(['json' => ['is_active' => null]]);
        $page = visit('/columns-browser-test');

        if ($isDarkMode) {
            $page = $page->inDarkMode();
        }

        $page
            ->assertNotChecked('[data-testid="author-active-checkbox"]')
            ->hover('[data-testid="author-active-checkbox"]')
            ->assertVisible('[role="tooltip"] strong')
            ->assertScript('document.querySelector(\'[role="tooltip"]\').textContent', 'Update activity for Alex Morgan')
            ->check('[data-testid="author-active-checkbox"]')
            ->assertScript('Alpine.$data(document.querySelector(\'[data-testid="author-active-checkbox"]\')).error', 'Approval <em>required</em>.')
            ->hover('[data-testid="enum-label-column"]')
            ->hover('[data-testid="author-active-checkbox"]')
            ->assertVisible('[role="tooltip"]')
            ->assertScript('document.querySelector(\'[role="tooltip"]\').textContent', 'Approval <em>required</em>.')
            ->assertMissing('[role="tooltip"] em')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        expect($author->fresh()->json['is_active'])->toBeNull();

        $page
            ->uncheck('[data-testid="author-active-checkbox"]')
            ->check('[data-testid="author-active-checkbox"]')
            ->assertScript('Alpine.$data(document.querySelector(\'[data-testid="author-active-checkbox"]\')).error === undefined')
            ->hover('[data-testid="enum-label-column"]')
            ->hover('[data-testid="author-active-checkbox"]')
            ->assertVisible('[role="tooltip"] strong')
            ->assertScript('document.querySelector(\'[role="tooltip"]\').textContent', 'Update activity for Alex Morgan')
            ->assertChecked('[data-testid="author-active-checkbox"]')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        expect($author->fresh()->json['is_active'])->toBeTrue();
    }
});

class TestTableWithCheckboxColumn extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
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
                Tables\Columns\CheckboxColumn::make('is_published'),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class TestTableWithRelationshipCheckboxColumn extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->columns([
                Tables\Columns\CheckboxColumn::make('author.json.is_active')
                    ->tooltip(static fn (mixed $state, User $relatedRecord, Post $record): string => get_debug_type($state) . '-' . ($state ? 'true' : 'false') . "-{$relatedRecord->getKey()}-{$record->getKey()}"),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
