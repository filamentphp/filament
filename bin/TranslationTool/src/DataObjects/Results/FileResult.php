<?php

namespace Filament\TranslationTool\DataObjects\Results;

use Filament\TranslationTool\DataObjects\Locale;
use Filament\TranslationTool\DataObjects\Package;
use Filament\TranslationTool\DataObjects\TranslationFile;

final class FileResult extends Result
{
    public function __construct(
        public Package $package,
        public TranslationFile $file,
        public Locale $locale,
        public int $totalTranslations = 0,
        public array $missingTranslations = [],
        public array $removedTranslations = []
    ) {}

    public function getTranslationLine(Locale $locale, string $key): ?int
    {
        return \array_search($key, array_keys($this->missingTranslations)) ?: null;
    }
}
