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
            ->with($destinationPath, $contents);
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
