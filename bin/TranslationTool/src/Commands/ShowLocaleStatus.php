<?php

namespace Filament\TranslationTool\Commands;

use Filament\TranslationTool\Actions\FindOutdatedTranslations;
use Filament\TranslationTool\DataObjects\Locale;
use Filament\TranslationTool\DataObjects\Results\FileResult;
use Filament\TranslationTool\DataObjects\Results\Result;
use Filament\TranslationTool\DataObjects\Translator;
use Illuminate\Support\Collection;

use function Laravel\Prompts\table;

final class ShowLocaleStatus
{
    public function __invoke(): void
    {
        $checker = new FindOutdatedTranslations(locales: Locale::getAvailableLocales());
        $results = $checker->getResults();

        $this->format($results);
    }

    public function format(Collection $results): void
    {
        Translator::loadFromFile();

        $data = collect($results)
            ->groupBy('locale')
            ->map(function (Collection $fileResults, string $localeCode) {
                $totalTranslations = $fileResults->sum('totalTranslations');
                $missingTranslations = $fileResults->flatMap(fn (FileResult $result) => $result->missingTranslations)->unique()->values()->all();
                $removedTranslations = $fileResults->flatMap(fn (FileResult $result) => $result->removedTranslations)->unique()->values()->all();

                $locale = new Locale($localeCode);
                $result = new Result(
                    totalTranslations: $totalTranslations,
                    missingTranslations: $missingTranslations,
                    removedTranslations: $removedTranslations
                );

                return [
                    'language' => $locale->displayName(),
                    'locale' => $locale->code,
                    'nr_of_outdated' => count($result->missingTranslations) + count($result->removedTranslations),
                    'coverage_value' => $result->coverage(),
                    'coverage' => $result->coverage() . '%',
                    'translators' => Translator::getTranslatorsForLocale($locale)
                        ->map(
                            fn (Translator $translator) => $translator->discordId
                            ? $translator->getDiscordLink()
                            : $translator->discordHandle
                        )
                        ->implode(', '),
                ];
            })
            ->sortBy('coverage_value')
            ->map(
                fn (array $row) => collect($row)
                    ->only(['language', 'locale', 'nr_of_outdated', 'coverage', 'translators'])
                    ->map(fn (string $value, string $key) => $key === 'translators' ? $value : match (true) {
                        $row['coverage_value'] > 99 => "<fg=green>$value</>",
                        $row['coverage_value'] > 90 => "<fg=yellow>$value</>",
                        default => "<fg=red>$value</>"
                    })
            )
            ->toArray();

        table(
            ['Language', 'Locale', 'No. Outdated', 'Coverage', 'Translators'],
            $data
        );
    }
}
