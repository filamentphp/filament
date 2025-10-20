<?php

namespace Filament\TranslationTool\DataObjects;

use Illuminate\Support\Collection;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

final class Package
{
    public function __construct(
        public string $name,
    ) {}

    public function getLangFolder($locale): string
    {
        return PACKAGES_DIR . $this->name . '/resources/lang/' . $locale;
    }

    /**
     * @return Collection<TranslationFile>
     */
    public function getTranslationFiles(): Collection
    {
        $originDir = $this->getLangFolder('en');

        if (! is_dir($originDir)) {
            return collect();
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($originDir, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        return collect(iterator_to_array($iterator, false))
            ->map(function (SplFileInfo $file) use ($originDir) {
                $filename = str_replace($originDir . DIRECTORY_SEPARATOR, '', $file->getPathname());

                return new TranslationFile($this, $filename);
            });
    }

    /**
     * @return Collection<Package>
     */
    public static function all(): Collection
    {
        return collect(scandir(PACKAGES_DIR))
            ->filter(fn (string $package) => is_dir(PACKAGES_DIR . $package) && ! in_array($package, ['.', '..', '.DS_Store']))
            ->map(fn ($package) => new Package($package));
    }
}
