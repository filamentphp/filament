<?php

namespace Filament\Commands;

use Composer\InstalledVersions;
use Illuminate\Console\Command;

class MakeIssueCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:filament-issue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates a link to the Filament issue page and pre-fills the version numbers.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $filament = InstalledVersions::getPrettyVersion('filament/filament');
        $laravel = InstalledVersions::getPrettyVersion('laravel/framework');
        $livewire = InstalledVersions::getPrettyVersion('livewire/livewire');
        $php = PHP_VERSION;

        $issueTemplate = 'bug_report.yml';
        $baseGithubUrl = 'https://github.com/filamentphp/filament/issues/new?';

        $queryParams = [
            'template' => $issueTemplate,
            'package-version' => $filament,
            'laravel-version' => $laravel,
            'livewire-version' => $livewire,
            'php-version' => $php,
        ];

        $fullUrl = "{$baseGithubUrl}" . http_build_query($queryParams);

        $this->openGithubIssueInBrowser($fullUrl);
    }

    public function openGithubIssueInBrowser(string $url): void
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
