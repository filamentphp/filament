<?php

namespace Filament\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;

class MakeSettingsPageCommand extends Command
{
    use Concerns\CanManipulateFiles;
    use Concerns\CanValidateInput;

    protected $description = 'Creates a Filament settings page class.';

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
        $path = app_path(
            (string) Str::of($page)
                ->prepend('Filament\\Pages\\')
                ->replace('\\', '/')
                ->append('.php'),
        );

        $settingsPage = (string) Str::of($this->argument('settingsClass') ?? $this->askRequired('Settings class (e.g. `FooterSettings`)', 'settings class'))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');
        $settingsClass = (string) Str::of($settingsPage)->afterLast('\\');
        $settingsNamespace = Str::of($settingsPage)->contains('\\')
            ? (string) Str::of($settingsPage)->beforeLast('\\')
            : '';
        $settingsPath = app_path(
            (string) Str::of($settingsPage)
                ->prepend('Filament\\Settings\\')
                ->replace('\\','/')
                ->append('.php'),
        );

        if ($this->checkForCollision([$path, $settingsPath])) {
            return static::INVALID;
        }

        $this->publishSettingsMigration();

        Artisan::call('make:settings-migration',['name' => 'Create'.$settingsClass]);

        $this->copyStubToApp('Settings', $settingsPath, [
            'class' => $settingsClass,
            'namespace' => 'App\\Filament\\Settings' . ($settingsNamespace !== '' ? "\\{$settingsNamespace}" : '')
        ]);

        $this->info("Successfully created setting's class: App/".Str::after($settingsPath,'/app/'));

        $this->info("Successfully created setting's migration: database/".Str::after(database_path('settings/Create'.$settingsClass),'/database/'));

        $this->copyStubToApp('SettingsPage', $path, [
            'class' => $pageClass,
            'namespace' => 'App\\Filament\\Pages' . ($pageNamespace !== '' ? "\\{$pageNamespace}" : ''),
            'settingsClass' => $settingsClass,
            'settingsNamespace' => 'App\\Filament\\Settings' . ($settingsNamespace !== '' ? "\\{$settingsNamespace}" :'')
        ]);

        $this->info("Successfully created setting's page: App/".Str::after($path,'/app/')."!");

        return static::SUCCESS;
    }

    protected function copyStubToApp(string $stub, string $targetPath, array $replacements = []): void
    {
        $filesystem = new Filesystem();

        if (! $this->fileExists($stubPath = base_path("stubs/filament/{$stub}.stub"))) {
            $stubPath = __DIR__ . "/../../stubs/{$stub}.stub";
        }

        $stub = Str::of($filesystem->get($stubPath));

        foreach ($replacements as $key => $replacement) {
            $stub = $stub->replace("{{ {$key} }}", $replacement);
        }

        $stub = (string) $stub;

        $this->writeFile($targetPath, $stub);
    }

    protected function publishSettingsMigration(): void
    {
        $filesystem = new Filesystem();

        if (! Str::contains(collect($filesystem->glob(database_path('migrations/*.*')))->implode(''),'create_settings_table.php'))
        {
            Artisan::call('vendor:publish',[
                '--provider' => 'Spatie\LaravelSettings\LaravelSettingsServiceProvider',
                '--tag' => 'migrations'
            ]);
        }
    }
}
