<?php

namespace Filament\TranslationTool\Commands;

use Filament\TranslationTool\DataObjects\Locale;
use Filament\TranslationTool\DataObjects\Package;
use Laravel\Prompts;

final class ListTranslations
{
    public function __invoke(): void
    {
        $localeCode = Prompts\search(
            label: 'Select a locale to proofread',
            options: fn ($search) => Locale::getAvailableLocales()
                ->mapWithKeys(fn (Locale $locale) => [$locale->code => $locale->displayName()])
                ->filter(fn (string $displayName) => ! $search || str_contains($displayName, $search))
                ->toArray(),
            placeholder: 'Search for a locale',
            required: true,
        );

        $output = new Prompts\Output\ConsoleOutput;

        foreach (Package::all() as $package) {
            $files = $package->getTranslationFiles();

            if ($files->isEmpty()) {
                continue;
            }

            foreach ($package->getTranslationFiles() as $file) {
                $output->writeln('');

                if (! $file->exists($localeCode)) {
                    Prompts\error(
                        "[{$package->name}] {$file->name} ⋅ Missing ⋅ " . createLink($file->getFileUrl('en'), '↗ Open EN file')
                    );

                    continue;
                }

                Prompts\info(
                    "[{$package->name}] {$file->name} ⋅ " . createLink($file->getFileUrl($localeCode), '↗ Open file')
                );

                $originTranslations = $file->getTranslations('en');
                $localeTranslations = $file->getTranslations($localeCode);

                $index = 0;

                foreach ($originTranslations as $key => $originTranslation) {
                    $translation = ($localeTranslations[$key] ?? '<bg=red;fg=white> Missing </>');
                    $content = '  ' . $originTranslation . '  ➡  ' . $translation;

                    $output->writeln(
                        $index++ % 2 === 0
                            ? '<fg=cyan>' . $content . '</>'
                            : $content
                    );
                }
            }
        }
    }
}
