#! /usr/bin/env php
<?php

declare(strict_types=1);

use Filament\TranslationTool\Commands;

use function Laravel\Prompts\select;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/TranslationTool/src/helpers.php';

const PACKAGES_DIR = __DIR__ . '/../packages/';

$commandLabels = [
    'status' => 'Show translation status',
    'list-outdated' => 'List outdated translation keys',
    'list-translations' => 'List translations side-by-side',
    'list-translators' => 'List translation managers',
];

$options = getopt('', [...array_keys($commandLabels), 'help', 'locale:']);

$localeCode = $options['locale'] ?? null;
unset($options['locale']);

$commandName = array_key_first($options) ?? select(
    label: 'Choose the command you want to run',
    options: $commandLabels,
    default: 'status',
);

match ($commandName) {
    'status' => (new Commands\ShowLocaleStatus)(),
    'list-outdated' => (new Commands\ListOutdatedTranslationKeys)($localeCode),
    'list-translations' => (new Commands\ListTranslations)($localeCode),
    'list-translators' => (new Commands\ListTranslators)(),
    'help' => (new Commands\ShowHelp)($commandLabels),
};
