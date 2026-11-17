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
    Route::get('/pending-js-renderer.js', static fn () => response(file_get_contents(__DIR__ . '/../../resources/js/pending-js-renderer.js'), headers: ['Content-Type' => 'text/javascript']));

    $directory = dirname(__DIR__, 3) . '/build/js-renderer-starters';
    foreach (File::allFiles($directory) as $file) {
        $id = substr(str_replace('\\', '/', $file->getRelativePathname()), 0, -3);
        FilamentAsset::register([
            Js::make($id, $file->getPathname())->loadedOnRequest(),
        ], 'tests/js-widgets');
    }
    Artisan::call('filament:assets');
    $this->actingAs(User::factory()->create());
});

it('updates generated renderers from dashboard filters without replacing their host', function (string $framework): void {
    $browser = visit('/js-widget-browser-test?framework=' . $framework);

    $browser->assertSeeIn('.fi-wi-js', 'All time: 19');
    $browser->assertScript('window.rendererReadiness.length === 1 && window.rendererReadiness.every(Boolean)', true);
    $browser->script('window.widgetHost = document.querySelector(".fi-wi-js [x-ref=host]")');
    $browser->fill('Period', 'September')
        ->assertSeeIn('.fi-wi-js', 'September: 19')
        ->assertScript('window.widgetHost === document.querySelector(".fi-wi-js [x-ref=host]")', true)
        ->click('Toggle widget')
        ->assertNotPresent('.fi-wi-js')
        ->click('Toggle widget')
        ->assertSeeIn('.fi-wi-js', 'September: 19')
        ->assertNoSmoke();

    if ($framework === 'js') {
        $browser->assertNoAccessibilityIssues();
        $browser = $browser->inDarkMode();
        $browser->assertNoAccessibilityIssues();
    }
})->with(['js', 'react', 'vue', 'svelte', 'js-ts', 'react-ts', 'vue-ts', 'svelte-ts']);

it('keeps custom widget placeholders until asynchronous mounting completes', function (): void {
    $page = visit('/js-widget-browser-test?framework=pending');
    $page->assertScript('typeof window.releaseRendererImport', 'function')
        ->assertVisible('[data-testid=custom-loading-placeholder]');
    $page->script('window.releaseRendererImport()');
    $page->assertScript('typeof window.releaseRendererMount', 'function')
        ->assertVisible('[data-testid=custom-loading-placeholder]')
        ->assertPresent('[inert][aria-hidden=true]')
        ->assertNoAccessibilityIssues();
    $page->script('window.releaseRendererMount()');
    $page->assertSeeIn('.fi-wi-js', 'All time: 19')
        ->assertMissing('[data-testid=custom-loading-placeholder]')
        ->assertNotPresent('.fi-wi-js [inert]')
        ->assertNoSmoke();
});

it('calls the widget through `$wire` and disposes the renderer on removal', function (): void {
    visit('/js-widget-browser-test?framework=inline')
        ->assertSeeIn('.fi-wi-js', 'All time: 19')
        ->click('Refresh total')
        ->assertSeeIn('.fi-wi-js', 'All time: 0')
        ->fill('Period', 'September')
        ->assertSeeIn('.fi-wi-js', 'September: 0')
        ->assertScript('window.widgetMounts', 1)
        ->click('Toggle widget')
        ->assertNotPresent('.fi-wi-js')
        ->assertScript('window.widgetDisposals', 1)
        ->click('Toggle widget')
        ->assertSeeIn('.fi-wi-js', 'September: 19')
        ->assertScript('window.widgetMounts', 2)
        ->assertNoSmoke();
});

it('shows an accessible error when mounting fails', function (): void {
    $browser = visit('/js-widget-browser-test?framework=broken');
    $browser->assertVisible('[role=alert]')
        ->assertNoAccessibilityIssues();

    $browser = $browser->inDarkMode();
    $browser->assertVisible('[role=alert]')
        ->assertNoAccessibilityIssues();
});
