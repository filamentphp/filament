<?php

namespace Filament\Support\Commands;

use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Filament\Support\Facades\Asset;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class AssetsCommand extends Command
{
    use CanManipulateFiles;

    protected $description = 'Set up Filament assets.';

    protected $signature = 'filament:assets';

    public function handle(): int
    {
        foreach (Asset::getAlpineComponents() as $asset) {
            if ($asset->isRemote()) {
                continue;
            }

            $this->copyAsset($asset->getPath(), $asset->getPublicPath());
        }

        foreach (Asset::getScripts() as $asset) {
            if ($asset->isRemote()) {
                continue;
            }

            $this->copyAsset($asset->getPath(), $asset->getPublicPath());
        }

        foreach (Asset::getStyles() as $asset) {
            if ($asset->isRemote()) {
                continue;
            }

            $this->copyAsset($asset->getPath(), $asset->getPublicPath());
        }

        $this->components->info('Successfully published assets!');

        return static::SUCCESS;
    }

    protected function copyAsset(string $from, string $to): void
    {
        $filesystem = app(Filesystem::class);

        $filesystem->ensureDirectoryExists(
            (string) str($to)
                ->beforeLast(DIRECTORY_SEPARATOR),
        );

        $filesystem->copy($from, $to);
    }
}
