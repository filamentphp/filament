<?php

namespace Filament\Support\Commands;

use Composer\InstalledVersions;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:filament-issue')]
class MakeIssueCommand extends Command
{
    protected $signature = 'make:filament-issue';

    protected $description = 'Generates a link to the Filament issue page and pre-fills the version numbers.';

    public function handle(): void
    {
        $this->openUrlInBrowser('https://github.com/filamentphp/filament/issues/new?' . http_build_query([
            'template' => 'bug_report.yml',
            'package-version' => InstalledVersions::getPrettyVersion('filament/support'),
            'laravel-version' => InstalledVersions::getPrettyVersion('laravel/framework'),
            'livewire-version' => InstalledVersions::getPrettyVersion('livewire/livewire'),
            'php-version' => PHP_VERSION,
        ]));
    }

    public function openUrlInBrowser(string $url): void
    {
        if (PHP_OS_FAMILY === 'Darwin') {
            exec('open "' . $url . '"');
        }
        if (PHP_OS_FAMILY === 'Linux') {
            exec('xdg-open "' . $url . '"');
        }
        if (PHP_OS_FAMILY === 'Windows') {
            exec('start "" "' . $url . '"');
        }
    }
}
