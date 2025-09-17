#! /usr/bin/env php
<?php

declare(strict_types=1);

use Filament\TranslationTool\Commands;

use function Laravel\Prompts\select;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/TranslationTool/src/helpers.php';

const PACKAGES_DIR = __DIR__ . '/../packages/';

$commandName = select(
    label: 'Choose the command you want to run',
    options: [
        'status' => 'Show translation status',
        'list_outdated' => 'List outdated translation keys',
        'list_translations' => 'List translations side-by-side',
        'list_translators' => 'List translation managers',
    ],
    default: 'status',
);

$command = match ($commandName) {
    'status' => new Commands\ShowLocaleStatus,
    'list_outdated' => new Commands\ListOutdatedTranslationKeys,
    'list_translators' => new Commands\ListTranslators,
    'list_translations' => new Commands\ListTranslations
};

$command();
