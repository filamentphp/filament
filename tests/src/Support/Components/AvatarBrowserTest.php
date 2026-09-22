<?php

use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

use function Pest\Laravel\actingAs;

uses(TestCase::class)->group('serial');

beforeEach(function (): void {
    foreach (File::allFiles(dirname(__DIR__, 4) . '/build/js-avatar-renderers') as $file) {
        $id = substr(str_replace('\\', '/', $file->getRelativePathname()), 0, -3);
        FilamentAsset::register([
            Js::make($id, $file->getPathname())->loadedOnRequest(),
        ], 'tests/avatars');
    }

    Artisan::call('filament:assets');
    actingAs(User::factory()->create());
});

it('renders `Avatar` like Blade and updates props in all frameworks', function (): void {
    foreach (['light', 'dark'] as $theme) {
        $page = visit('/avatar-browser-test');

        if ($theme === 'dark') {
            $page = $page->inDarkMode();
        }

        $page->assertScript('document.documentElement.classList.contains("dark")', $theme === 'dark');

        foreach (['react', 'vue', 'svelte'] as $framework) {
            $page->assertScript("document.querySelectorAll('[data-avatar-row={$framework}] [data-loaded]').length", 4);
        }

        $page->assertScript("document.querySelectorAll('[data-avatar-row=react] [data-image-ref]').length", 4);

        $page->assertScript(<<<'JS'
        (() => {
            const attributes = (image) => Object.fromEntries(
                [...image.attributes].map(({ name, value }) => [name,
                    name === 'class' ? value.split(/\s+/).filter(Boolean).sort().join(' ') : value,
                ]).sort(([first], [second]) => first.localeCompare(second)),
            )
            const images = (framework) => [...document.querySelectorAll(`[data-avatar-row=${framework}] img`)].map(attributes)
            return ['react', 'vue', 'svelte'].every((framework) => JSON.stringify(images(framework)) === JSON.stringify(images('blade')))
        })()
        JS, true)
            ->assertNoAccessibilityIssues();

        $page->script("window.avatarImages = [...document.querySelectorAll('[data-avatar-row] img')]");

        foreach (['react', 'vue', 'svelte'] as $framework) {
            $page->click("[data-testid=update-{$framework}]")
                ->assertScript("[...document.querySelectorAll('[data-avatar-row={$framework}] img')].every(image => image.alt === 'Updated portrait' && !image.classList.contains('fi-circular') && image.classList.contains('avatar-custom-size') && image.classList.contains('avatar-custom-theme'))", true);
        }

        $page->assertScript("[...document.querySelectorAll('[data-avatar-row] img')].every((image, index) => image === window.avatarImages[index])", true)
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        foreach (['react', 'vue', 'svelte'] as $framework) {
            $page->click("[data-testid=reset-{$framework}]")
                ->assertScript("[...document.querySelectorAll('[data-avatar-row={$framework}] img')].every(image => image.alt === '' && image.className === 'fi-avatar fi-circular fi-size-md' && !image.hasAttribute('title') && !image.hasAttribute('loading') && !image.hasAttribute('data-profile'))", true);
        }

        $page->assertScript("[...document.querySelectorAll('[data-avatar-row] img')].every((image, index) => image === window.avatarImages[index])", true)
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();
    }
});
