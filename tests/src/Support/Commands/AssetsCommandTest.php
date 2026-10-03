<?php

use Filament\Support\Commands\AssetsCommand;
use Filament\Tests\TestCase;
use Illuminate\Filesystem\Filesystem;
use Mockery\MockInterface;

uses(TestCase::class);

it('uses `Filesystem::replace()` to publish assets atomically', function (): void {
    $sourcePath = base_path('source/asset.js');
    $destinationPath = public_path('asset.js');
    $contents = 'window.asset = true';

    $filesystem = Mockery::mock(Filesystem::class, function (MockInterface $mock) use ($contents, $destinationPath, $sourcePath): void {
        $mock
            ->shouldReceive('ensureDirectoryExists')
            ->once()
            ->with(dirname($destinationPath));
        $mock
            ->shouldReceive('get')
            ->once()
            ->with($sourcePath)
            ->andReturn($contents);
        $mock
            ->shouldReceive('replace')
            ->once()
            ->with($destinationPath, $contents, 0666 & ~umask());
    });

    app()->instance(Filesystem::class, $filesystem);

    $command = new class extends AssetsCommand
    {
        public function copyAssetForTesting(string $from, string $to): void
        {
            $this->copyAsset($from, $to);
        }
    };

    $command->copyAssetForTesting($sourcePath, $destinationPath);
});

it('publishes assets without the executable bit', function (): void {
    $directory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'filament-assets-' . bin2hex(random_bytes(8));
    $sourcePath = $directory . DIRECTORY_SEPARATOR . 'source.js';
    $destinationPath = $directory . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'asset.js';

    $filesystem = app(Filesystem::class);
    $filesystem->ensureDirectoryExists($directory);
    $filesystem->put($sourcePath, 'window.asset = true');

    $originalUmask = umask(0022);

    try {
        $command = new class extends AssetsCommand
        {
            public function copyAssetForTesting(string $from, string $to): void
            {
                $this->copyAsset($from, $to);
            }
        };

        $command->copyAssetForTesting($sourcePath, $destinationPath);

        clearstatcache();

        expect(fileperms($destinationPath) & 0777)->toBe(0644);
    } finally {
        umask($originalUmask);

        $filesystem->deleteDirectory($directory);
    }
});
