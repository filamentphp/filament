<?php

use Composer\InstalledVersions;
use Filament\Support\Assets\Asset;
use Filament\Support\Facades\FilamentAsset;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('returns the ID from `getId()`', function (): void {
    $asset = TestAsset::make('my-asset');

    expect($asset->getId())->toBe('my-asset');
});

it('returns the path from `getPath()`', function (): void {
    $asset = TestAsset::make('my-asset', '/path/to/asset.js');

    expect($asset->getPath())->toBe('/path/to/asset.js');
});

it('returns `null` for `getPath()` when no path is given', function (): void {
    $asset = TestAsset::make('my-asset');

    expect($asset->getPath())->toBeNull();
});

describe('package', function (): void {
    it('returns `null` for `getPackage()` by default', function (): void {
        $asset = TestAsset::make('my-asset');

        expect($asset->getPackage())->toBeNull();
    });

    it('can set `package()`', function (): void {
        $asset = TestAsset::make('my-asset')
            ->package('filament/support');

        expect($asset->getPackage())->toBe('filament/support');
    });

    it('can clear `package()` with `null`', function (): void {
        $asset = TestAsset::make('my-asset')
            ->package('filament/support')
            ->package(null);

        expect($asset->getPackage())->toBeNull();
    });
});

describe('loaded on request', function (): void {
    it('defaults `isLoadedOnRequest()` to `false`', function (): void {
        $asset = TestAsset::make('my-asset');

        expect($asset->isLoadedOnRequest())->toBeFalse();
    });

    it('can set `loadedOnRequest()`', function (): void {
        $asset = TestAsset::make('my-asset')
            ->loadedOnRequest();

        expect($asset->isLoadedOnRequest())->toBeTrue();
    });

    it('can set `loadedOnRequest()` to `false`', function (): void {
        $asset = TestAsset::make('my-asset')
            ->loadedOnRequest()
            ->loadedOnRequest(false);

        expect($asset->isLoadedOnRequest())->toBeFalse();
    });
});

describe('remote detection', function (): void {
    it('returns `false` for `isRemote()` with a local path', function (): void {
        $asset = TestAsset::make('my-asset', '/local/path.js');

        expect($asset->isRemote())->toBeFalse();
    });

    it('returns `true` for `isRemote()` with an HTTPS URL', function (): void {
        $asset = TestAsset::make('my-asset', 'https://cdn.example.com/asset.js');

        expect($asset->isRemote())->toBeTrue();
    });

    it('returns `true` for `isRemote()` with an HTTP URL', function (): void {
        $asset = TestAsset::make('my-asset', 'http://cdn.example.com/asset.js');

        expect($asset->isRemote())->toBeTrue();
    });

    it('returns `true` for `isRemote()` with a protocol-relative URL', function (): void {
        $asset = TestAsset::make('my-asset', '//cdn.example.com/asset.js');

        expect($asset->isRemote())->toBeTrue();
    });
});

it('returns a version string from `getVersion()`', function (): void {
    $asset = TestAsset::make('my-asset');

    $version = $asset->getVersion();

    expect($version)->toBeString();
    expect($version)->not->toBeEmpty();
});

it('returns `filament/support` version when no package is set', function (): void {
    $asset = TestAsset::make('my-asset');

    $version = $asset->getVersion();

    expect($version)->toBe(TestAsset::make('my-asset')->package('filament/support')->getVersion());
});

it('returns package version when a valid package is set', function (): void {
    $asset = TestAsset::make('my-asset')
        ->package('filament/support');

    $version = $asset->getVersion();

    $installedVersion = InstalledVersions::getVersion('filament/support');
    $installedReference = InstalledVersions::getReference('filament/support');
    $expectedVersion = $installedVersion;

    if (
        (str_starts_with($installedVersion, 'dev-') || str_ends_with($installedVersion, '-dev')) &&
        is_string($installedReference) &&
        ($installedReference !== '')
    ) {
        $expectedVersion = hash('sha256', $installedReference);
    }

    expect($version)->toBe($expectedVersion);
});

it('returns the configured application version from `getVersion()`', function (): void {
    FilamentAsset::appVersion('dev-custom-build');

    try {
        expect(TestAsset::make('my-asset')->package('app')->getVersion())->toBe('dev-custom-build');
    } finally {
        FilamentAsset::appVersion(null);
    }
});

it('uses the full installed reference to version assets from development packages', function (): void {
    $original = InstalledVersions::getAllRawData()[0];

    InstalledVersions::reload([
        'root' => $original['root'],
        'versions' => [
            ...$original['versions'],
            'vendor/dev-main-plugin' => [
                'pretty_version' => 'dev-main',
                'version' => 'dev-main',
                'reference' => '/branches/development/@100',
                'type' => 'library',
                'install_path' => __DIR__,
                'aliases' => [],
                'dev_requirement' => false,
            ],
            'vendor/dev-branch-plugin' => [
                'pretty_version' => '5.x-dev',
                'version' => '5.9999999.9999999.9999999-dev',
                'reference' => '/branches/development/@101',
                'type' => 'library',
                'install_path' => __DIR__,
                'aliases' => [],
                'dev_requirement' => false,
            ],
            'vendor/dev-plugin-without-reference' => [
                'pretty_version' => 'dev-main',
                'version' => 'dev-main',
                'reference' => null,
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
            'vendor/provided-plugin' => [
                'pretty_version' => null,
                'version' => null,
                'reference' => null,
                'type' => 'library',
                'install_path' => __DIR__,
                'aliases' => [],
                'dev_requirement' => false,
            ],
        ],
    ]);

    try {
        $devMainVersion = TestAsset::make('my-asset')->package('vendor/dev-main-plugin')->getVersion();
        $devBranchVersion = TestAsset::make('my-asset')->package('vendor/dev-branch-plugin')->getVersion();

        expect($devMainVersion)
            ->toBe(hash('sha256', '/branches/development/@100'))
            ->not->toBe($devBranchVersion)
            ->and($devBranchVersion)->toBe(hash('sha256', '/branches/development/@101'))
            ->and(TestAsset::make('my-asset')->package('vendor/dev-plugin-without-reference')->getVersion())->toBe('dev-main')
            ->and(TestAsset::make('my-asset')->package('vendor/released-plugin')->getVersion())->toBe('2.3.1.0')
            ->and(TestAsset::make('my-asset')->package('vendor/provided-plugin')->getVersion())->toBe(TestAsset::make('my-asset')->getVersion());

        expect(fn (): string => TestAsset::make('my-asset')->getInstalledVersionForTesting('vendor/provided-plugin'))
            ->toThrow(LogicException::class, 'Unable to determine the installed version of package [vendor/provided-plugin].');
    } finally {
        InstalledVersions::reload($original);
    }
});

it('falls back to `filament/support` version for unknown packages', function (): void {
    $asset = TestAsset::make('my-asset')
        ->package('nonexistent/package');

    $version = $asset->getVersion();

    expect($version)->toBe(TestAsset::make('my-asset')->getVersion());
});

class TestAsset extends Asset
{
    public function getInstalledVersionForTesting(string $package): string
    {
        return $this->getInstalledVersion($package);
    }

    public function getPublicPath(): string
    {
        return '';
    }
}
