<?php

namespace Filament\Support\Commands;

use Composer\InstalledVersions;
use Filament\Support\Commands\Concerns\CanOpenUrlInBrowser;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:filament-issue', aliases: [
    'filament:issue',
    'filament:make-issue',
])]
class MakeIssueCommand extends Command
{
    use CanOpenUrlInBrowser;

    protected $description = 'Generates a link to the Filament issue page and pre-fills the version numbers.';

    protected $name = 'make:filament-issue';

    /**
     * @var array<string>
     */
    protected $aliases = [
        'filament:issue',
        'filament:make-issue',
    ];

    public function handle(): void
    {
        $url = 'https://github.com/filamentphp/filament/issues/new?' . http_build_query([
            'template' => 'bug_report.yml',
            'package-version' => InstalledVersions::getPrettyVersion('filament/support'),
            'laravel-version' => InstalledVersions::getPrettyVersion('laravel/framework'),
            'livewire-version' => InstalledVersions::getPrettyVersion('livewire/livewire'),
            'php-version' => PHP_VERSION,
        ]);

        $this->openUrlInBrowser($url);
    }
}
