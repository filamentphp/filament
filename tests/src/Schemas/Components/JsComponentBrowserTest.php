<?php

use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

uses(TestCase::class)->group('serial');

beforeEach(function (): void {
    $directory = dirname(__DIR__, 4) . '/build/js-renderer-starters';
    if (! is_file($directory . '/component-js.js')) {
        throw new LogicException('Run `npm run test:js` before the schema component browser tests.');
    }
    foreach (File::allFiles($directory) as $file) {
        if ($file->getExtension() !== 'js') {
            continue;
        }
        $id = substr(str_replace('\\', '/', $file->getRelativePathname()), 0, -3);
        FilamentAsset::register([Js::make($id, $file->getPathname())->loadedOnRequest()], 'tests/js-components');
    }
    Artisan::call('filament:assets');
    $this->actingAs(User::factory()->create());
});

it('syncs PHP props without replacing hosts and never renders children', function (string $framework, bool $isDarkMode): void {
    $page = visit('/js-component-browser-test?framework=' . $framework);
    if ($isDarkMode) {
        $page = $page->inDarkMode();
    }
    $page->assertSeeIn('[data-report]', 'Quarterly sales')
        ->assertSeeIn('[data-comparison]', 'Previous quarter')
        ->assertDontSee('This child must not render')
        ->assertNoAccessibilityIssues();
    $page->script('window.originalReport = document.querySelector("[data-report] [x-ref=host]"); window.originalParagraph = window.originalReport.firstElementChild');
    $page->fill('[id="form.caption"]', 'Revised café <em>literal</em>')
        ->assertSeeIn('[data-report]', 'Revised café <em>literal</em>')
        ->assertScript('document.querySelector("[data-report] [x-ref=host]") === window.originalReport', true)
        ->assertScript('window.originalReport.firstElementChild === window.originalParagraph', true)
        ->assertNotPresent('[data-report] em')
        ->assertSeeIn('[data-comparison]', 'Previous quarter')
        ->click('Toggle configuration')
        ->assertSeeIn('[data-report]', 'Your component is ready.')
        ->click('Toggle configuration')
        ->assertSeeIn('[data-report]', 'Revised café <em>literal</em>')
        ->click('Toggle mounted')
        ->assertNotPresent('[data-report]')
        ->click('Toggle mounted')
        ->assertSeeIn('[data-report]', 'Revised café <em>literal</em>')
        ->assertScript('document.querySelector("[data-report] [x-ref=host]") === window.originalReport', false)
        ->assertNoAccessibilityIssues()
        ->assertNoSmoke();

    if (($framework === 'react') && (! $isDarkMode)) {
        $page->screenshotElement('main', 'js-schema-component');
    }
})->with(['js', 'react', 'vue', 'svelte', 'js-ts', 'react-ts', 'vue-ts', 'svelte-ts'])->with([false, true]);

it('shows an accessible renderer error without affecting sibling components', function (bool $isDarkMode): void {
    $page = visit('/js-component-browser-test');
    if ($isDarkMode) {
        $page = $page->inDarkMode();
    }
    $page->assertSeeIn('[data-report]', 'Quarterly sales')
        ->click('Toggle failure')
        ->assertSeeIn('[data-report] [role=alert]', 'This component could not be loaded.')
        ->assertScript('document.querySelector("[data-report] [x-ref=host]").childElementCount', 0)
        ->assertSeeIn('[data-comparison]', 'Previous quarter')
        ->assertValue('[id="form.caption"]', 'Quarterly sales')
        ->assertNoAccessibilityIssues()
        ->screenshotElement('main', 'js-schema-component-error-' . ($isDarkMode ? 'dark' : 'light'))
        ->click('Toggle failure')
        ->assertSeeIn('[data-report]', 'Quarterly sales');
})->with([false, true]);
