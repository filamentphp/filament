<?php

use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

uses(TestCase::class);

it('renders HTML with `x-filament-html` without initializing it, across Livewire updates', function (bool $isCspSafe): void {
    retry(10, function () use ($isCspSafe): void {
        Artisan::call('filament:assets');

        config(['livewire.csp_safe' => $isCspSafe]);

        // A page without a panel around it, so the only Alpine expressions on it are the directive's own.
        Route::get('/filament-html-directive', fn () => view('fixtures.filament-html-directive'))->middleware('web');

        visit('/filament-html-directive')
            ->assertPresent('[data-testid="filament-html-markup"]')
            // Alpine directives inside the inserted HTML must stay inert.
            ->assertSeeIn('[data-testid="filament-html-nested-directive"]', 'Not initialized')
            // A re-render sends the element back empty, so the HTML must survive the morph.
            ->click('[data-testid="filament-html-refresh"]')
            ->wait(1)
            ->assertPresent('[data-testid="filament-html-markup"]')
            ->click('[data-testid="filament-html-replace"]')
            ->wait(1)
            ->assertPresent('[data-testid="filament-html-replaced"]')
            ->assertMissing('[data-testid="filament-html-markup"]')
            ->assertNoSmoke()
            // Only the default build can evaluate an arrow function, which proves the page ran the build this case is about.
            ->assertScript(<<<'JS'
                (() => {
                    let isCspBuild = false

                    window.Alpine.setErrorHandler(() => (isCspBuild = true))
                    window.Alpine.evaluate(document.body, '(() => true)()')

                    return isCspBuild
                })()
                JS, $isCspSafe)
            ->assertNoAccessibilityIssues();

        visit('/filament-html-directive')
            ->inDarkMode()
            ->assertNoAccessibilityIssues();
    });
})->with([
    'default Alpine build' => [false],
    'CSP Alpine build' => [true],
]);
