<?php

namespace Filament\TranslationTool\Commands;

use Laravel\Prompts;

final class ShowHelp
{
    /**
     * @param  array<string, string>  $commandLabels
     */
    public function __invoke(array $commandLabels): void
    {
        $rows = [];

        foreach ($commandLabels as $commandName => $label) {
            $rows[] = ["--{$commandName}", $label];
        }

        $rows[] = ['--help', 'Show available options'];
        $rows[] = ['--locale=<code>', 'Skip the locale prompt of `--list-outdated` and `--list-translations`'];

        Prompts\table(['Option', 'Description'], $rows);
    }
}
