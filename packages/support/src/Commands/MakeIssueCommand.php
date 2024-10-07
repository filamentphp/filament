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
        $url = 'https://github.com/filamentphp/filament/issues/new?' . http_build_query([
            'template' => 'bug_report.yml',
            'package-version' => InstalledVersions::getPrettyVersion('filament/support'),
            'laravel-version' => InstalledVersions::getPrettyVersion('laravel/framework'),
            'livewire-version' => InstalledVersions::getPrettyVersion('livewire/livewire'),
            'php-version' => PHP_VERSION,
        ]);

        $result = $this->openUrlInBrowser($url);

        if ($result !== 0) {
            $this->components->error('An error occurred while trying to open the issue page in your browser.');
            $this->output->writeln('  <comment>Please open the following URL in your browser:</>');
            $this->output->writeln('  <href="' . $url . '">' . $url . '</>');
        }
    }

    public function openUrlInBrowser(string $url): int
    {
        $result = -1;

        if (PHP_OS_FAMILY === 'Darwin') {
            exec('open "' . $url . '"', result_code: $result);
        }
        if (PHP_OS_FAMILY === 'Linux') {
            exec('xdg-open "' . $url . '"', result_code: $result);
        }
        if (PHP_OS_FAMILY === 'Windows') {
            exec('start "" "' . $url . '"', result_code: $result);
        }

        return $result;
    }
}
