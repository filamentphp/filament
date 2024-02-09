<?php

namespace Filament\Commands;

use Illuminate\Console\Command;

class MakeFilamentVersionPrefilledGithubIssue extends Command
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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filament = \Composer\InstalledVersions::getPrettyVersion('filament/filament');
        $laravel = \Composer\InstalledVersions::getPrettyVersion('laravel/framework');
        $livewire = \Composer\InstalledVersions::getPrettyVersion('livewire/livewire');
        $php = \PHP_VERSION;

        $issueTemplate = 'bug_report.yml';
        $baseGithubUrl = 'https://github.com/filamentphp/filament/issues/new';

        $queryParams = [
            'template' => $issueTemplate,
            'package-version' => $filament,
            'laravel-version' => $laravel,
            'livewire-version' => $livewire,
            'php-version' => $php,
        ];

        $queryString = http_build_query($queryParams);

        $fullUrl = $baseGithubUrl . '?' . $queryString;

        $this->info('Please fill out the issue template at ' . $fullUrl);
    }
}
