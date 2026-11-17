<?php

use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
    $this->actingAs(User::factory()->create());

    Route::get('/async-component.js', static fn () => response(
        file_get_contents(__DIR__ . '/../../resources/js/async-component.js'),
        headers: ['Content-Type' => 'text/javascript'],
    ));

    Route::get('/async-component/{component}.js', static function (string $component) {
        abort_unless(in_array($component, ['color-picker', 'date-time-picker', 'file-upload', 'key-value', 'tags-input']), 404);

        return response(
            'await new Promise(resolve => { window.releaseAsyncDownload = resolve });' .
                file_get_contents(__DIR__ . '/../../../packages/forms/dist/components/' . $component . '.js'),
            headers: ['Content-Type' => 'text/javascript'],
        );
    });
});

it('keeps the loading placeholder until the control finishes initializing', function (bool $hasCustomLoadingPlaceholder): void {
    $page = visit('/async-component-browser-test?custom=' . (int) $hasCustomLoadingPlaceholder);

    $page->assertScript('typeof window.releaseAsyncDownload', 'function')
        ->assertVisible('[data-testid="async-region"] [role="status"]')
        ->assertAttribute('[data-testid="async-region"] [role="status"]', 'aria-busy', 'true')
        ->assertNoAccessibilityIssues();

    if ($hasCustomLoadingPlaceholder) {
        $page->assertVisible('[data-testid="custom-loading-placeholder"]');
    }

    $page->script('window.releaseAsyncDownload()');
    $page->assertScript('typeof window.releaseAsyncInitialization', 'function')
        ->assertVisible('[data-testid="async-region"] [role="status"]');

    $page->script('window.releaseAsyncInitialization()');
    $page->assertValue('input[x-model="value"]', 'Ready to edit')
        ->assertMissing('[data-testid="async-region"] [role="status"]')
        ->fill('Test value', 'Edited value')
        ->assertValue('input[x-model="value"]', 'Edited value')
        ->assertNoJavaScriptErrors()
        ->assertNoAccessibilityIssues();

    $page->inDarkMode()->assertNoAccessibilityIssues();
})->with([false, true]);

it('keeps enhanced fields inaccessible until their JavaScript has initialized', function (string $control, string $readySelector): void {
    $page = visit('/async-component-browser-test?control=' . $control);

    $page->assertScript('typeof window.releaseAsyncDownload', 'function')
        ->assertVisible('[data-testid="async-region"] [role="status"][aria-busy="true"]')
        ->assertPresent('[data-testid="async-region"] [inert][aria-hidden="true"]');

    $page->script('window.releaseAsyncDownload()');

    $page->assertMissing('[data-testid="async-region"] [aria-busy="true"]')
        ->assertNotPresent('[data-testid="async-region"] [inert]')
        ->assertPresent($readySelector)
        ->assertEnabled($readySelector);

    // FilePond visually hides its native file input after initialization.
    if ($control !== 'file-upload') {
        $page->assertVisible($readySelector);
    }

    $page->assertNoJavaScriptErrors()
        ->assertNoAccessibilityIssues();

    if ($control === 'tags-input') {
        $page->assertScript('document.activeElement.matches(' . json_encode($readySelector) . ')', true);
    }
})->with([
    ['color-picker', 'input[role="combobox"]'],
    ['date-time-picker', 'input[role="combobox"]'],
    ['file-upload', 'input[type="file"]'],
    ['key-value', 'input[aria-label="Property name"]'],
    ['tags-input', 'input[type="text"]'],
]);

it('can remove a control while async work is pending', function (bool $isInitializing): void {
    $page = visit('/async-component-browser-test');
    $page->assertScript('typeof window.releaseAsyncDownload', 'function');

    if ($isInitializing) {
        $page->script('window.releaseAsyncDownload()');
        $page->assertScript('typeof window.releaseAsyncInitialization', 'function');
    }

    $page->click('Remove control')
        ->assertNotPresent('[data-testid="async-region"]');

    $page->script($isInitializing ? 'window.releaseAsyncInitialization()' : 'window.releaseAsyncDownload()');
    $page->script("window.dispatchEvent(new Event('async-control-probe'))");
    $page->assertNotPresent('[data-testid="async-region"]')
        ->assertScript('document.querySelector(\'[data-testid="lifecycle-events"]\').textContent', $isInitializing ? 'started,destroyed' : '')
        ->assertNoJavaScriptErrors();
})->with([false, true]);
