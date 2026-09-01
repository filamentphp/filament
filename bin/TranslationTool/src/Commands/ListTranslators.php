<?php

namespace Filament\TranslationTool\Commands;

use Filament\TranslationTool\DataObjects\Locale;
use Filament\TranslationTool\DataObjects\Translator;
use Laravel\Prompts;

final class ListTranslators
{
    public function __invoke(): void
    {
        Translator::loadFromFile();

        $data = Locale::getAvailableLocales()
            ->map(fn (Locale $locale) => [
                'name' => $locale->displayName(),
                'code' => $locale->code,
                'nr_of_translators' => count($translators = Translator::getTranslatorsForLocale($locale)),
                'translators' => $translators
                    ->map(fn (Translator $translator) => $translator->getPreferredHandle())
                    ->implode(', '),
            ])
            ->sortBy(['nr_of_translators', 'name']);

        Prompts\table(['Language', 'Locale', 'Nr.', 'Translators'], $data->toArray());

        Prompts\info('No. of translators: ' . Translator::count());
        Prompts\info('No. of locales: ' . count(Locale::getAvailableLocales()));
        Prompts\info('No. of maintained locales: ' . $data->filter(fn ($row) => $row['nr_of_translators'] > 0)->count());
        Prompts\warning('No. of locales with missing translator: ' . $data->filter(fn ($row) => $row['nr_of_translators'] === 0)->count());
    }
}
