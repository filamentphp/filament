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

    protected $signature = 'make:filament-settings-page
                            {name? : provide Settings Page name. i.e, `ManageSite`}
                            {--S|settings= : Provide your Settings class name for the settings page. i.e, `SiteSettings`}
                            {--N|namespace : Use this flag to Override the namesapace, provide your settings class name with the namespace of your choosing because by default the this command presumes that you have a Settings i.e, `SiteSettings` in your App\Settings directory.}';

    public function handle(): int
    {
        $page = (string) Str::of($this->argument('name') ?? $this->askRequired('Page name (e.g. `ManageSite`)', 'name'))
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

        $settingsPage = (string) Str::of($this->option('settings') ?? $this->askRequired('Settings class (e.g. `SiteSettings`)', 'settings'))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');
        $settingsNamespace = Str::of($settingsPage)->contains('\\') ?
        (string) Str::of($settingsPage)->beforeLast('\\') :
        '';
        $settingsClass = (string) Str::of($settingsPage)->afterLast('\\');

        if ($this->checkForCollision([$path])) {
            return static::INVALID;
        }

        $this->copyStubToApp('SettingsPage', $path, [
            'class' => $pageClass,
            'namespace' => 'App\\Filament\\Pages' . ($pageNamespace !== '' ? "\\{$pageNamespace}" : ''),
            'settingsClass' => $settingsClass,
            'settingsNamespace' => $this->option('namespace') ? $settingsNamespace : 'App\\Settings'
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
}
