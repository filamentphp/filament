<?php

namespace Filament\TranslationTool\Commands;

use Filament\TranslationTool\Actions\FindOutdatedTranslations;
use Filament\TranslationTool\DataObjects\Locale;
use Filament\TranslationTool\DataObjects\Results\FileResult;
use Illuminate\Support\Collection;
use Laravel\Prompts;

final class ListOutdatedTranslationKeys
{
    public function __invoke(): void
    {
        $localeCode = Prompts\search(
            label: 'Select a locale to check',
            options: fn ($search) => Locale::getAvailableLocales()
                ->mapWithKeys(fn (Locale $locale) => [$locale->code => $locale->displayName()])
                ->filter(fn (string $displayName) => ! $search || stripos($displayName, $search) !== false)
                ->toArray(),
            placeholder: 'Search for a locale',
            required: true,
        );

        $locale = new Locale($localeCode);

        $checker = new FindOutdatedTranslations(locales: collect([$locale]));
        $results = $checker->getResults();

        $this->format($results, $locale);
    }

    public function format(Collection $results, Locale $locale): void
    {
        $data = collect($results)
            ->groupBy(fn (FileResult $result) => $result->package->name)
            ->map(function (Collection $fileResults) use ($locale) {
                return $fileResults
                    ->groupBy(fn (FileResult $result) => $result->file->getFilePath($locale))
                    ->map(function (Collection $fileGroup, string $file) use ($locale) {
                        /**
                         * @var FileResult $result
                         */
                        $result = $fileGroup->first();

                        $file = $result->file;

                        $missingTranslations = $fileGroup->flatMap(fn (FileResult $result) => $result->missingTranslations)->unique();
                        $removedTranslations = $fileGroup->flatMap(fn (FileResult $result) => $result->removedTranslations)->unique();

                        $missingTranslations = $missingTranslations->map(fn (array $value, string $key) => [
                            'key' => createLink($file->getFileUrl('en', $value['line'] ?? null), $key),
                            'line' => isset($value['line']) ? createLink($file->getFileUrl($locale, $value['line'] ?? null), $value['line']) : null,
                            'status' => 'Missing',
                        ]);

                        $removedTranslations = $removedTranslations->map(fn (array $value, string $key) => [
                            'key' => createLink($file->getFileUrl($locale, $value['line'] ?? null), $key),
                            'line' => isset($value['line']) ? createLink($file->getFileUrl('en', $value['line'] ?? null), $value['line']) : null,
                            'status' => 'Removed',
                        ]);

                        $file = $result->file;
                        $locale = $result->locale;

                        return [
                            'file_exists' => $file->exists($locale),
                            'header' => "[{$result->package->name}] {$file->name} ⋅ " .
                                (
                                    ($file->exists($locale)
                                        ? createLink($file->getFileUrl($locale), '↗ Open file') . ' | '
                                        : \null)

                                        . createLink($file->getFileUrl('en'), '↗ Open EN file')
                                ),
                            'rows' => $missingTranslations->merge($removedTranslations)->toArray(),
                        ];
                    });
            })
            ->flatten(1)
            ->filter(fn ($table) => count($table['rows']) > 0);

        if (count($data) === 0) {
            Prompts\info('🎉🎉🎉 All translations are up to date 🎉🎉🎉');

            return;
        }

        foreach ($data as $table) {

            if ($table['file_exists']) {
                Prompts\info($table['header']);

                Prompts\table(
                    ['Key', 'Line', 'Status'],
                    $table['rows'],
                );
            } else {
                Prompts\error($table['header']);
            }
        }

        Prompts\info('Total outdated translations: ' . count($data));
    }
}
