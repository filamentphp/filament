<?php

namespace Filament\TranslationTool\DataObjects;

use Illuminate\Support\Arr;

final class TranslationFile
{
    public function __construct(
        public Package $package,
        public string $name,
    ) {}

    public function exists(Locale | string $locale): bool
    {
        return file_exists($this->getFilePath($locale));
    }

    public function getFilePath(Locale | string $locale): string
    {
        return $this->package->getLangFolder($locale) . DIRECTORY_SEPARATOR . $this->name;
    }

    public function getFileUrl(Locale | string $locale): string
    {
        return 'file://' . $this->getFilePath($locale);
    }

    public function getTranslations(Locale | string $locale): array
    {
        if (! file_exists($this->getFilePath($locale))) {
            return [];
        }

        return Arr::dot(require $this->getFilePath($locale));
    }
}
