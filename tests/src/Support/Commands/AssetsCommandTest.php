<?php

use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Tests\TestCase;
use Illuminate\Filesystem\Filesystem;

uses(TestCase::class);

it('publishes and replaces asset contents without the executable bit', function (): void {
    $package = 'filament-tests-' . bin2hex(random_bytes(8));
    $directory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $package;
    $sourcePath = $directory . DIRECTORY_SEPARATOR . 'source.js';
    $asset = Js::make('example', $sourcePath)->package($package);
    $destinationPath = $asset->getPublicPath();

    $filesystem = app(Filesystem::class);
    $filesystem->ensureDirectoryExists($directory);
    $originalUmask = umask(0022);

    try {
        FilamentAsset::register([$asset], $package);
        $filesystem->put($sourcePath, 'window.asset = "original"');

        $this->artisan('filament:assets')->assertSuccessful();

        clearstatcache();

        expect($filesystem->get($destinationPath))->toBe('window.asset = "original"')
            ->and(fileperms($destinationPath) & 0777)->toBe(0644);

        $filesystem->put($sourcePath, 'window.asset = "updated"');

        $this->artisan('filament:assets')->assertSuccessful();

        clearstatcache();

        expect($filesystem->get($destinationPath))->toBe('window.asset = "updated"')
            ->and(fileperms($destinationPath) & 0777)->toBe(0644);
    } finally {
        umask($originalUmask);

        $filesystem->deleteDirectory($directory);
        $filesystem->deleteDirectory(dirname($destinationPath));
    }
});
