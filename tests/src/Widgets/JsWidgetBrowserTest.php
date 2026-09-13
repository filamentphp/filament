<?php

use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

uses(TestCase::class)->group('serial');

beforeEach(function (): void {
    $directory = dirname(__DIR__, 3) . '/build/js-widget-renderers';
    foreach (File::allFiles($directory) as $file) {
        $id = substr(str_replace('\\', '/', $file->getRelativePathname()), 0, -3);
        FilamentAsset::register([
            Js::make($id, $file->getPathname())->loadedOnRequest(),
        ], 'tests/js-widgets');
    }
    Artisan::call('filament:assets');
    $this->actingAs(User::factory()->create());
});

it('updates generated renderers from dashboard filters without replacing their host', function (string $framework, bool $isDarkMode): void {
    $browser = visit('/js-widget-browser-test?framework=' . $framework);
    if ($isDarkMode) {
        $browser = $browser->inDarkMode();
    }

    $browser->assertSeeIn('.fi-wi-js', 'All time: 19')
        ->assertScript('document.documentElement.classList.contains("dark")', $isDarkMode)
        ->assertNoAccessibilityIssues();
    $browser->script('window.widgetHost = document.querySelector(".fi-wi-js [x-ref=host]")');
    $browser->fill('Period', 'September')
        ->assertSeeIn('.fi-wi-js', 'September: 19')
        ->assertScript('window.widgetHost === document.querySelector(".fi-wi-js [x-ref=host]")', true)
        ->click('Toggle widget')
        ->assertNotPresent('.fi-wi-js')
        ->click('Toggle widget')
        ->assertSeeIn('.fi-wi-js', 'September: 19')
        ->assertNoAccessibilityIssues()
        ->assertNoSmoke();

    if ($framework === 'react') {
        $browser->screenshotElement('.fi-wi-js', 'js-widget-' . ($isDarkMode ? 'dark' : 'light'));
    }
})->with(['js', 'react', 'vue', 'svelte', 'js-ts', 'react-ts', 'vue-ts', 'svelte-ts'])->with([false, true]);

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

it('shows an accessible error when mounting fails', function (bool $isDarkMode): void {
    $browser = visit('/js-widget-browser-test?framework=broken');
    if ($isDarkMode) {
        $browser = $browser->inDarkMode();
    }
    $browser->assertSeeIn('.fi-wi-js [role=alert]', 'This widget could not be loaded.')
        ->assertScript('document.documentElement.classList.contains("dark")', $isDarkMode)
        ->assertNoAccessibilityIssues()
        ->screenshotElement('.fi-wi-js', 'js-widget-error-' . ($isDarkMode ? 'dark' : 'light'));
})->with([false, true]);
