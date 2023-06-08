<?php

namespace Filament\Commands;

use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Filament\Support\Commands\Concerns\CanValidateInput;
use Illuminate\Console\Command;

class MakeSettingsPageCommand extends Command
{
    use CanManipulateFiles;
    use CanValidateInput;

    protected $description = 'Create a new Filament settings page class';

    protected $signature = 'make:filament-settings-page {name?} {settingsClass?}';

    public function handle(): int
    {
        $page = (string) str($this->argument('name') ?? $this->askRequired('Page name (e.g. `ManageFooter`)', 'name'))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');
        $pageClass = (string) str($page)->afterLast('\\');
        $pageNamespace = str($page)->contains('\\') ?
            (string) str($page)->beforeLast('\\') :
            '';

        $settingsClass = (string) str($this->argument('settingsClass') ?? $this->askRequired('Settings class (e.g. `FooterSettings`)', 'settings class'))
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
