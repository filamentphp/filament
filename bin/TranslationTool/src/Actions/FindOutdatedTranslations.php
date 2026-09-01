<?php

namespace Filament\TranslationTool\Actions;

use Filament\TranslationTool\DataObjects\Locale;
use Filament\TranslationTool\DataObjects\Package;
use Filament\TranslationTool\DataObjects\Results\FileResult;
use Illuminate\Support\Collection;

class FindOutdatedTranslations
{
    public function __construct(
        public Collection $locales,
        public ?Collection $packages = null,
        public ?Locale $targetLocale = null,
    ) {
        $this->packages ??= Package::all();
        $this->targetLocale ??= new Locale('en');
    }

    public function getResults(): Collection
    {
        $results = collect();

        foreach ($this->locales as $locale) {
            foreach ($this->packages as $package) {
                $results = $results->merge($this->getTranslationStatus($package, $locale));
            }
        }

        return $results;
    }

    protected function getTranslationStatus(Package $package, Locale $locale): array
    {
        $originDir = $package->getLangFolder($this->targetLocale);

        if (! is_dir($originDir)) {
            return [];
        }

        $results = [];

        foreach ($package->getTranslationFiles() as $file) {
            $originKeys = $file->getTranslations($this->targetLocale);

            $totalKeys = count($originKeys);

            if (! file_exists($file->getFilePath($locale->code))) {
                $results[] = new FileResult(
                    package: $package,
                    file: $file,
                    locale: $locale,
                    totalTranslations: $totalKeys,
                    missingTranslations: $originKeys,
                    removedTranslations: []
                );

                continue;
            }

            $localeKeys = $file->getTranslations($locale->code);

            $missingKeys = array_diff_key($originKeys, $localeKeys);
            $removedKeys = array_diff_key($localeKeys, $originKeys);

            $results[] = new FileResult(
                package: $package,
                file: $file,
                locale: $locale,
                totalTranslations: $totalKeys,
                missingTranslations: $missingKeys,
                removedTranslations: $removedKeys
            );
        }

        return $results;
    }
}
