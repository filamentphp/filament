<?php

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\AssetManager;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Font;
use Filament\Support\Assets\Js;
use Filament\Support\Assets\Theme;
use Filament\Tests\TestCase;

uses(TestCase::class);

beforeEach(function (): void {
    $this->manager = new AssetManager;
});

describe('app version', function (): void {
    it('returns `null` for `getAppVersion()` by default', function (): void {
        expect($this->manager->getAppVersion())->toBeNull();
    });

    it('can set `appVersion()`', function (): void {
        $this->manager->appVersion('1.0.0');

        expect($this->manager->getAppVersion())->toBe('1.0.0');
    });

    it('can clear `appVersion()` with `null`', function (): void {
        $this->manager->appVersion('1.0.0');
        $this->manager->appVersion(null);

        expect($this->manager->getAppVersion())->toBeNull();
    });
});

describe('registering assets', function (): void {
    it('can register an `AlpineComponent`', function (): void {
        $this->manager->register([
            AlpineComponent::make('date-picker', '/path/to/date-picker.js'),
        ], 'filament/forms');

        $components = $this->manager->getAlpineComponents();

        expect($components)->toHaveCount(1);
        expect($components[0]->getId())->toBe('date-picker');
        expect($components[0]->getPackage())->toBe('filament/forms');
    });

    it('can register a `Css` asset', function (): void {
        $this->manager->register([
            Css::make('custom-styles', '/path/to/styles.css'),
        ], 'my-package');

        $styles = $this->manager->getStyles();

        expect($styles)->toHaveCount(1);
        expect($styles[0]->getId())->toBe('custom-styles');
    });

    it('can register a `Js` asset', function (): void {
        $this->manager->register([
            Js::make('custom-script', '/path/to/script.js'),
        ], 'my-package');

        $scripts = $this->manager->getScripts();

        expect($scripts)->toHaveCount(1);
        expect($scripts[0]->getId())->toBe('custom-script');
    });

    it('can register a `Font` asset', function (): void {
        $this->manager->register([
            Font::make('inter', '/path/to/inter.css'),
        ], 'my-package');

        $fonts = $this->manager->getFonts();

        expect($fonts)->toHaveCount(1);
        expect($fonts[0]->getId())->toBe('inter');
    });

    it('can register a `Theme` asset', function (): void {
        $this->manager->register([
            Theme::make('admin', '/path/to/admin.css'),
        ]);

        $themes = $this->manager->getThemes();

        expect($themes)->toHaveKey('admin');
        expect($themes['admin']->getId())->toBe('admin');
    });

    it('can register multiple asset types at once', function (): void {
        $this->manager->register([
            Css::make('styles', '/path/styles.css'),
            Js::make('script', '/path/script.js'),
            AlpineComponent::make('component', '/path/component.js'),
        ], 'my-package');

        expect($this->manager->getStyles())->toHaveCount(1);
        expect($this->manager->getScripts())->toHaveCount(1);
        expect($this->manager->getAlpineComponents())->toHaveCount(1);
    });
});

describe('filtering by package', function (): void {
    it('can filter Alpine components by package', function (): void {
        $this->manager->register([
            AlpineComponent::make('comp-a', '/a.js'),
        ], 'package-a');
        $this->manager->register([
            AlpineComponent::make('comp-b', '/b.js'),
        ], 'package-b');

        $filtered = $this->manager->getAlpineComponents(['package-a']);

        expect($filtered)->toHaveCount(1);
        expect($filtered[0]->getId())->toBe('comp-a');
    });

    it('can filter styles by package', function (): void {
        $this->manager->register([
            Css::make('style-a', '/a.css'),
        ], 'package-a');
        $this->manager->register([
            Css::make('style-b', '/b.css'),
        ], 'package-b');

        $filtered = $this->manager->getStyles(['package-b']);

        expect($filtered)->toHaveCount(1);
        expect($filtered[0]->getId())->toBe('style-b');
    });

    it('can filter scripts by package', function (): void {
        $this->manager->register([
            Js::make('script-a', '/a.js'),
        ], 'package-a');
        $this->manager->register([
            Js::make('script-b', '/b.js'),
        ], 'package-b');

        $filtered = $this->manager->getScripts(['package-a']);

        expect($filtered)->toHaveCount(1);
        expect($filtered[0]->getId())->toBe('script-a');
    });

    it('returns all assets when no package filter given', function (): void {
        $this->manager->register([
            Js::make('script-a', '/a.js'),
        ], 'package-a');
        $this->manager->register([
            Js::make('script-b', '/b.js'),
        ], 'package-b');

        expect($this->manager->getScripts())->toHaveCount(2);
    });
});

describe('getting asset sources', function (): void {
    it('can get an Alpine component src by ID', function (): void {
        $this->manager->register([
            AlpineComponent::make('date-picker', '/path/to/date-picker.js'),
        ], 'my-package');

        $src = $this->manager->getAlpineComponentSrc('date-picker', 'my-package');

        expect($src)->toBeString();
        expect($src)->toContain('date-picker');
    });

    it('throws `LogicException` for non-existent Alpine component', function (): void {
        expect(fn () => $this->manager->getAlpineComponentSrc('nonexistent', 'my-package'))
            ->toThrow(LogicException::class);
    });

    it('can get a script src by ID', function (): void {
        $this->manager->register([
            Js::make('custom', '/path/to/custom.js'),
        ], 'my-package');

        $src = $this->manager->getScriptSrc('custom', 'my-package');

        expect($src)->toBeString();
        expect($src)->toContain('custom');
    });

    it('throws `LogicException` for non-existent script', function (): void {
        expect(fn () => $this->manager->getScriptSrc('nonexistent', 'my-package'))
            ->toThrow(LogicException::class);
    });

    it('can get a style href by ID', function (): void {
        $this->manager->register([
            Css::make('custom', '/path/to/custom.css'),
        ], 'my-package');

        $href = $this->manager->getStyleHref('custom', 'my-package');

        expect($href)->toBeString();
        expect($href)->toContain('custom');
    });

    it('throws `LogicException` for non-existent style', function (): void {
        expect(fn () => $this->manager->getStyleHref('nonexistent', 'my-package'))
            ->toThrow(LogicException::class);
    });
});

describe('CSS variables', function (): void {
    it('returns empty array for `getCssVariables()` by default', function (): void {
        expect($this->manager->getCssVariables())->toBe([]);
    });

    it('can register CSS variables', function (): void {
        $this->manager->registerCssVariables(['primary' => '#ff0000'], 'my-package');

        expect($this->manager->getCssVariables())->toBe(['primary' => '#ff0000']);
    });

    it('can filter CSS variables by package', function (): void {
        $this->manager->registerCssVariables(['a' => '1'], 'package-a');
        $this->manager->registerCssVariables(['b' => '2'], 'package-b');

        $filtered = $this->manager->getCssVariables(['package-a']);

        expect($filtered)->toBe(['a' => '1']);
    });

    it('merges CSS variables within the same package', function (): void {
        $this->manager->registerCssVariables(['a' => '1'], 'pkg');
        $this->manager->registerCssVariables(['b' => '2'], 'pkg');

        expect($this->manager->getCssVariables(['pkg']))->toBe(['a' => '1', 'b' => '2']);
    });
});

describe('script data', function (): void {
    it('returns empty array for `getScriptData()` by default', function (): void {
        expect($this->manager->getScriptData())->toBe([]);
    });

    it('can register script data', function (): void {
        $this->manager->registerScriptData(['locale' => 'en'], 'my-package');

        expect($this->manager->getScriptData())->toBe(['locale' => 'en']);
    });

    it('can filter script data by package', function (): void {
        $this->manager->registerScriptData(['a' => '1'], 'package-a');
        $this->manager->registerScriptData(['b' => '2'], 'package-b');

        $filtered = $this->manager->getScriptData(['package-a']);

        expect($filtered)->toBe(['a' => '1']);
    });

    it('merges script data within the same package', function (): void {
        $this->manager->registerScriptData(['a' => '1'], 'pkg');
        $this->manager->registerScriptData(['b' => '2'], 'pkg');

        expect($this->manager->getScriptData(['pkg']))->toBe(['a' => '1', 'b' => '2']);
    });
});

describe('themes', function (): void {
    it('returns empty array for `getThemes()` by default', function (): void {
        expect($this->manager->getThemes())->toBe([]);
    });

    it('can get a theme by ID', function (): void {
        $this->manager->register([
            Theme::make('admin', '/path/admin.css'),
        ]);

        $theme = $this->manager->getTheme('admin');

        expect($theme)->toBeInstanceOf(Theme::class);
        expect($theme->getId())->toBe('admin');
    });

    it('returns `null` for non-existent theme', function (): void {
        expect($this->manager->getTheme('nonexistent'))->toBeNull();
    });

    it('overwrites themes with the same ID', function (): void {
        $this->manager->register([Theme::make('admin', '/first.css')]);
        $this->manager->register([Theme::make('admin', '/second.css')]);

        $themes = $this->manager->getThemes();

        expect($themes)->toHaveCount(1);
        expect($themes['admin']->getPath())->toBe('/second.css');
    });
});
