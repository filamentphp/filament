<?php

namespace Filament\Commands;

use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Filament\Support\Commands\Concerns\CanValidateInput;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeSettingsPageCommand extends Command
{
    use CanManipulateFiles;
    use CanValidateInput;

    protected $description = 'Create a new Filament settings page class';

    protected $signature = 'make:filament-settings-page {name?} {settingsClass?}';

    public function handle(): int
    {
        $page = (string) Str::of($this->argument('name') ?? $this->askRequired('Page name (e.g. `ManageFooter`)', 'name'))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');
        $pageClass = (string) Str::of($page)->afterLast('\\');
        $pageNamespace = Str::of($page)->contains('\\') ?
            (string) Str::of($page)->beforeLast('\\') :
            '';

        $settingsClass = (string) Str::of($this->argument('settingsClass') ?? $this->askRequired('Settings class (e.g. `FooterSettings`)', 'settings class'))
            ->trim('/')
            ->trim('\\')
            ->trim(' ');

        $path = app_path(
            (string) Str::of($page)
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

        $this->info("Successfully created {$page}!");

        return static::SUCCESS;
    }
}
