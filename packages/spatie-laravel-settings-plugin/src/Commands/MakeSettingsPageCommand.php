<?php

namespace Filament\Commands;

use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Illuminate\Console\Command;
use function Laravel\Prompts\text;

class MakeSettingsPageCommand extends Command
{
    use CanManipulateFiles;

    protected $description = 'Create a new Filament settings page class';

    protected $signature = 'make:filament-settings-page {name?} {settingsClass?}';

    public function handle(): int
    {
        $page = (string) str($this->argument('name') ?? text(
            label: 'What is the page name?',
            placeholder: 'ManageFooter',
            required: true,
        ))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');
        $pageClass = (string) str($page)->afterLast('\\');
        $pageNamespace = str($page)->contains('\\') ?
            (string) str($page)->beforeLast('\\') :
            '';

        $settingsClass = (string) str($this->argument('settingsClass') ?? text(
            label: 'What is the settings name?',
            placeholder: 'FooterSettings',
            required: true,
        ))
            ->trim('/')
            ->trim('\\')
            ->trim(' ');

        $path = app_path(
            (string) str($page)
                ->prepend('Filament\\Pages\\')
                ->replace('\\', '/')
                ->append('.php'),
        );

        if ($this->checkForCollision([$path])) {
            return static::INVALID;
        }

        $this->copyStubToApp('SettingsPage', $path, [
            'class' => $pageClass,
            'namespace' => 'App\\Filament\\Pages' . ($pageNamespace !== '' ? "\\{$pageNamespace}" : ''),
            'settingsClass' => $settingsClass,
        ]);

        $this->components->info("Successfully created {$page}!");

        return static::SUCCESS;
    }
}
