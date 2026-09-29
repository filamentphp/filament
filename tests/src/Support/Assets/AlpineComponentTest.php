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

        expect($version)->toBe(AlpineComponent::make('my-component')->package('filament/support')->getVersion());
    });

    it('returns package version when a valid package is set', function (): void {
        $asset = AlpineComponent::make('my-component')
            ->package('filament/support');

        $version = $asset->getVersion();

        // A release reads as the release; a dev checkout (this monorepo) as its commit.
        $installed = InstalledVersions::getVersion('filament/support');
        $expected = (str_starts_with($installed, 'dev-') || str_ends_with($installed, '-dev'))
            ? substr(InstalledVersions::getReference('filament/support'), 0, 12)
            : $installed;

        expect($version)->toBe($expected);
    });

    it('uses the installed commit reference for a dev version, so a new commit busts the cache', function (): void {
        $original = InstalledVersions::getAllRawData()[0];

        InstalledVersions::reload([
            'root' => $original['root'],
            'versions' => [
                ...$original['versions'],
                'vendor/dev-plugin' => [
                    'pretty_version' => '5.x-dev',
                    'version' => '5.9999999.9999999.9999999-dev',
                    'reference' => 'abcdef1234567890abcdef1234567890abcdef12',
                    'type' => 'library',
                    'install_path' => __DIR__,
                    'aliases' => [],
                    'dev_requirement' => false,
                ],
                'vendor/released-plugin' => [
                    'pretty_version' => 'v2.3.1',
                    'version' => '2.3.1.0',
                    'reference' => 'fedcba0987654321fedcba0987654321fedcba09',
                    'type' => 'library',
                    'install_path' => __DIR__,
                    'aliases' => [],
                    'dev_requirement' => false,
                ],
            ],
        ]);

        try {
            expect(AlpineComponent::make('my-component')->package('vendor/dev-plugin')->getVersion())->toBe('abcdef123456');
            expect(AlpineComponent::make('my-component')->package('vendor/released-plugin')->getVersion())->toBe('2.3.1.0');
        } finally {
            InstalledVersions::reload($original);
        }
    });

    it('falls back to the file\'s modification time for a package Composer does not know', function (): void {
        $asset = AlpineComponent::make('my-component', __FILE__)
            ->package('nonexistent/package');

        expect($asset->getVersion())->toBe((string) filemtime(__FILE__));
    });

    it('falls back to `filament/support` version for unknown packages without a file', function (): void {
        $asset = AlpineComponent::make('my-component')
            ->package('nonexistent/package');

        $version = $asset->getVersion();

        expect($version)->toBe(AlpineComponent::make('my-component')->getVersion());
    });
});
