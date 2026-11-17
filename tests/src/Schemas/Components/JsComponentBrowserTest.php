<?php

use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

uses(TestCase::class)->group('serial');

beforeEach(function (): void {
    Route::get('/pending-js-renderer.js', static fn () => response(file_get_contents(__DIR__ . '/../../../resources/js/pending-js-renderer.js'), headers: ['Content-Type' => 'text/javascript']));

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

it('syncs PHP props without replacing hosts and never renders children', function (string $framework): void {
    $page = visit('/js-component-browser-test?framework=' . $framework);
    $page->assertSeeIn('[data-report]', 'Quarterly sales')
        ->assertScript('window.rendererReadiness.length >= 2 && window.rendererReadiness.every(Boolean)', true)
        ->assertSeeIn('[data-comparison]', 'Previous quarter')
        ->assertDontSee('This child must not render');
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
        ->assertNoSmoke();

    if ($framework === 'react') {
        $page->assertNoAccessibilityIssues()
            ->screenshotElement('main', 'js-schema-component');

        $page->inDarkMode()
            ->assertSeeIn('[data-report]', 'Quarterly sales')
            ->assertNoAccessibilityIssues();
    }
})->with(['js', 'react', 'vue', 'svelte', 'js-ts', 'react-ts', 'vue-ts', 'svelte-ts']);

it('handles pending renderer imports and mounts without exposing unfinished content', function (string $outcome): void {
    $page = visit('/js-component-browser-test?framework=pending');
    $page->assertScript('typeof window.releaseRendererImport', 'function')
        ->assertVisible('[data-testid=custom-loading-placeholder]');

    if ($outcome === 'remove during import') {
        $page->click('Toggle mounted')->assertNotPresent('[data-report]');
    }
    $page->script('window.failRendererImport = ' . ($outcome === 'import failure' ? 'true' : 'false') . '; window.releaseRendererImport()');

    if ($outcome !== 'import failure') {
        $page->assertScript('typeof window.releaseRendererMount', 'function');
        if ($outcome === 'remove during mount') {
            $page->assertScript('document.querySelector("[data-report] [x-ref=host] p")?.textContent', 'Quarterly sales')
                ->assertVisible('[data-testid=custom-loading-placeholder]');
            $page->script('window.removedHosts = [...document.querySelectorAll("[x-ref=host]")]');
            $page->click('Toggle mounted')->assertNotPresent('[data-report]');
        } elseif ($outcome !== 'remove during import') {
            $page->assertPresent('[data-report][inert][aria-hidden=true]')
                ->assertVisible('[data-testid=custom-loading-placeholder]')
                ->assertNoAccessibilityIssues();
            if ($outcome === 'ready') {
                $page->fill('Caption', 'Updated while mounting')
                    ->assertValue('[id="form.caption"]', 'Updated while mounting')
                    ->assertSeeIn('[data-testid=server-caption]', 'Updated while mounting')
                    ->assertVisible('[data-testid=custom-loading-placeholder]');
            }
        }
        $page->script('window.failRendererMount = ' . ($outcome === 'mount failure' ? 'true' : 'false') . '; window.releaseRendererMount()');
    }

    if (str_starts_with($outcome, 'remove')) {
        $page->assertNotPresent('[data-report]');
        if ($outcome === 'remove during mount') {
            $page->assertScript('window.rendererDisposals', 2)
                ->assertScript('window.removedHosts.every(host => !host.childElementCount)', true);
        }
        $page->assertNoSmoke();
    } else {
        $page->assertMissing('[data-testid=custom-loading-placeholder]')
            ->assertNotPresent('[data-report][inert]');
        if ($outcome === 'ready') {
            $page->assertSeeIn('[data-report]', 'Updated while mounting')->assertNoSmoke();
        } else {
            $page->assertVisible('[data-report] [role=alert]');
        }
        $page->assertNoAccessibilityIssues();
    }
})->with(['ready', 'import failure', 'mount failure', 'remove during import', 'remove during mount']);

it('shows an accessible renderer error without affecting sibling components', function (): void {
    $page = visit('/js-component-browser-test');
    $page->assertSeeIn('[data-report]', 'Quarterly sales')
        ->click('Toggle failure')
        ->assertVisible('[data-report] [role=alert]')
        ->assertScript('document.querySelector("[data-report] [x-ref=host]").childElementCount', 0)
        ->assertSeeIn('[data-comparison]', 'Previous quarter')
        ->assertValue('[id="form.caption"]', 'Quarterly sales')
        ->assertNoAccessibilityIssues()
        ->screenshotElement('main', 'js-schema-component-error-light')
        ->click('Toggle failure')
        ->assertSeeIn('[data-report]', 'Quarterly sales');

    $page->inDarkMode()
        ->click('Toggle failure')
        ->assertVisible('[data-report] [role=alert]')
        ->assertNoAccessibilityIssues()
        ->screenshotElement('main', 'js-schema-component-error-dark');
});

it('uses relative state and routes exposed methods separately from `$wire` methods', function (): void {
    $page = visit('/js-component-browser-test?utilities=1');
    $page->click('Use schema utilities')
        ->assertSeeIn('[data-utilities-result]', 'Quarterly sales | data | Updated via utilities | Component: Updated via utilities | Component: Updated via utilities | Page: Updated via utilities')
        ->assertValue('[id="form.caption"]', 'Updated via utilities')
        ->assertSeeIn('[data-report]', 'Updated via utilities')
        ->assertSeeIn('[data-comparison]', 'Previous quarter')
        ->assertNoAccessibilityIssues()
        ->assertNoSmoke();

    $page->inDarkMode()
        ->assertPresent('[data-utilities-result]')
        ->assertNoAccessibilityIssues();
});
