<?php

use Composer\InstalledVersions;
use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('can be constructed with `make()`', function (): void {
    $asset = AlpineComponent::make('my-component');

    expect($asset)->toBeInstanceOf(AlpineComponent::class);
    expect($asset)->toBeInstanceOf(Asset::class);
});

it('returns the ID from `getId()`', function (): void {
    $asset = AlpineComponent::make('my-component');

    expect($asset->getId())->toBe('my-component');
});

it('returns the path from `getPath()`', function (): void {
    $asset = AlpineComponent::make('my-component', '/path/to/component.js');

    expect($asset->getPath())->toBe('/path/to/component.js');
});

it('returns `null` for `getPath()` when no path given', function (): void {
    $asset = AlpineComponent::make('my-component');

    expect($asset->getPath())->toBeNull();
});

describe('package', function (): void {
    it('returns `null` for `getPackage()` by default', function (): void {
        $asset = AlpineComponent::make('my-component');

        expect($asset->getPackage())->toBeNull();
    });

    it('can set `package()`', function (): void {
        $asset = AlpineComponent::make('my-component')
            ->package('filament/forms');

        expect($asset->getPackage())->toBe('filament/forms');
    });

    it('can clear `package()` with `null`', function (): void {
        $asset = AlpineComponent::make('my-component')
            ->package('filament/forms')
            ->package(null);

        expect($asset->getPackage())->toBeNull();
    });
});

describe('loaded on request', function (): void {
    it('defaults `isLoadedOnRequest()` to `false`', function (): void {
        $asset = AlpineComponent::make('my-component');

        expect($asset->isLoadedOnRequest())->toBeFalse();
    });

    it('can set `loadedOnRequest()`', function (): void {
        $asset = AlpineComponent::make('my-component')
            ->loadedOnRequest();

        expect($asset->isLoadedOnRequest())->toBeTrue();
    });

    it('can set `loadedOnRequest()` to `false`', function (): void {
        $asset = AlpineComponent::make('my-component')
            ->loadedOnRequest()
            ->loadedOnRequest(false);

        expect($asset->isLoadedOnRequest())->toBeFalse();
    });
});

describe('remote detection', function (): void {
    it('returns `false` for `isRemote()` with local path', function (): void {
        $asset = AlpineComponent::make('my-component', '/local/path.js');

        expect($asset->isRemote())->toBeFalse();
    });

    it('returns `true` for `isRemote()` with https URL', function (): void {
        $asset = AlpineComponent::make('my-component', 'https://cdn.example.com/component.js');

        expect($asset->isRemote())->toBeTrue();
    });

    it('returns `true` for `isRemote()` with http URL', function (): void {
        $asset = AlpineComponent::make('my-component', 'http://cdn.example.com/component.js');

        expect($asset->isRemote())->toBeTrue();
    });

    it('returns `true` for `isRemote()` with protocol-relative URL', function (): void {
        $asset = AlpineComponent::make('my-component', '//cdn.example.com/component.js');

        expect($asset->isRemote())->toBeTrue();
    });
});

describe('public path', function (): void {
    it('returns a path containing the component ID', function (): void {
        $asset = AlpineComponent::make('date-picker')
            ->package('filament/forms');

        $path = $asset->getRelativePublicPath();

        expect($path)->toContain('date-picker.js');
        expect($path)->toContain('components');
        expect($path)->toContain('filament/forms');
    });

    it('returns a `getPublicPath()` that starts with the public path', function (): void {
        $asset = AlpineComponent::make('date-picker')
            ->package('filament/forms');

        $publicPath = $asset->getPublicPath();

        expect($publicPath)->toContain('date-picker.js');
    });

    it('returns a `getSrc()` URL with a version query string', function (): void {
        $asset = AlpineComponent::make('date-picker')
            ->package('filament/forms');

        $src = $asset->getSrc();

        expect($src)->toContain('date-picker.js');
        expect($src)->toContain('?v=');
    });
});

describe('version', function (): void {
    it('returns a version string from `getVersion()`', function (): void {
        $asset = AlpineComponent::make('my-component');

        $version = $asset->getVersion();

        expect($version)->toBeString();
        expect($version)->not->toBeEmpty();
    });

    it('returns `filament/support` version when no package is set', function (): void {
        $asset = AlpineComponent::make('my-component');

        $version = $asset->getVersion();

        expect($version)->toBe(InstalledVersions::getVersion('filament/support'));
    });

    it('returns package version when a valid package is set', function (): void {
        $asset = AlpineComponent::make('my-component')
            ->package('filament/support');

        $version = $asset->getVersion();

        expect($version)->toBe(InstalledVersions::getVersion('filament/support'));
    });

    it('falls back to `filament/support` version for unknown packages', function (): void {
        $asset = AlpineComponent::make('my-component')
            ->package('nonexistent/package');

        $version = $asset->getVersion();

        expect($version)->toBe(InstalledVersions::getVersion('filament/support'));
    });
});
