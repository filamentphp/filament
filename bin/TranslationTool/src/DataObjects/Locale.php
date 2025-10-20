<?php

namespace Filament\TranslationTool\DataObjects;

use Illuminate\Support\Collection;

final class Locale
{
    public function __construct(
        public string $code,
    ) {}

    /**
     * @return Collection<Locale>
     */
    public static function getAvailableLocales(): Collection
    {
        $locales = collect();

        foreach (scandir(PACKAGES_DIR) as $package) {
            if (! is_dir(PACKAGES_DIR . $package) || in_array($package, ['.', '..', '.DS_Store'])) {
                continue;
            }

            $langDir = PACKAGES_DIR . $package . '/resources/lang';

            if (is_dir($langDir)) {
                $locales = $locales->merge(array_diff(scandir($langDir), ['.', '..', '.DS_Store']));
            }
        }

        return $locales->unique()->mapInto(Locale::class);
    }

    public function displayName(): string
    {
        return locale_get_display_name($this->code, 'en') . ' (' . $this->code . ')';
    }

    public function __toString(): string
    {
        return $this->code;
    }
}
