<?php

use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Pages\PaginationBrowserTest;
use Filament\Tests\TestCase;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Artisan;
use Livewire\Component;
use Livewire\WithPagination;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
});

it('can render the same paginator multiple times with distinct `wire:key` prefixes', function (): void {
    Post::factory()->count(3)->create();

    $html = livewire(PaginationTestComponent::class)
        ->assertSuccessful()
        ->html();

    preg_match('/<nav[^>]*data-testid="top-pagination".*?<\/nav>/s', $html, $topPaginationMatches);
    preg_match('/<nav[^>]*data-testid="bottom-pagination".*?<\/nav>/s', $html, $bottomPaginationMatches);

    expect($topPaginationMatches)->toHaveCount(1)
        ->and($bottomPaginationMatches)->toHaveCount(1);

    preg_match_all('/wire:key="([^"]+)"/', $topPaginationMatches[0], $topWireKeyMatches);
    preg_match_all('/wire:key="([^"]+)"/', $bottomPaginationMatches[0], $bottomWireKeyMatches);

    $topWireKeys = array_values(array_unique($topWireKeyMatches[1]));
    $bottomWireKeys = array_values(array_unique($bottomWireKeyMatches[1]));

    expect($topWireKeys)->not->toBeEmpty()
        ->and($bottomWireKeys)->not->toBeEmpty()
        ->and(array_intersect($topWireKeys, $bottomWireKeys))->toBeEmpty();

    foreach ($topWireKeys as $wireKey) {
        expect($wireKey)->toContain('.top-pagination.');
    }

    foreach ($bottomWireKeys as $wireKey) {
        expect($wireKey)->toContain('.pagination.');
    }
});

it('can navigate when the same paginator is rendered multiple times', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit(PaginationBrowserTest::getUrl(isAbsolute: false))
            ->assertSeeIn('[data-testid="top-pagination"] [aria-current="page"]', '1')
            ->assertSeeIn('[data-testid="bottom-pagination"] [aria-current="page"]', '1')
            ->click('[data-testid="top-pagination"] [rel="next"]:visible')
            ->assertSeeIn('[data-testid="top-pagination"] [aria-current="page"]', '2')
            ->assertSeeIn('[data-testid="bottom-pagination"] [aria-current="page"]', '2')
            ->assertNoAccessibilityIssues();

        visit(PaginationBrowserTest::getUrl(isAbsolute: false))
            ->inDarkMode()
            ->assertNoAccessibilityIssues();
    });
});

class PaginationTestComponent extends Component
{
    use WithPagination;

    public function render(): View
    {
        return view('livewire.pagination-test', [
            'paginator' => Post::query()
                ->orderBy('id')
                ->paginate(perPage: 1),
        ]);
    }
}
