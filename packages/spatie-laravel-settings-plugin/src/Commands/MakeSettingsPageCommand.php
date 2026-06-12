<?php

namespace Filament\Commands;

use Filament\Commands\FileGenerators\SettingsPageClassGenerator;
use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Filament\Support\Commands\Concerns\HasCluster;
use Filament\Support\Commands\Concerns\HasClusterPagesLocation;
use Filament\Support\Commands\Concerns\HasPanel;
use Filament\Support\Commands\Exceptions\FailureCommandOutput;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Spatie\LaravelSettings\Settings;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use function Filament\Support\discover_app_classes;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\search;
use function Laravel\Prompts\suggest;
use function Laravel\Prompts\text;

class MakeSettingsPageCommand extends Command
{
    use CanManipulateFiles;
    use HasCluster;
    use HasClusterPagesLocation;
    use HasPanel;

    protected $description = 'Create a new Filament settings page class';

    protected $name = 'make:filament-settings-page';

    /**
     * @var class-string
     */
    protected string $fqn;

    protected string $fqnEnd;

    /**
     * @var class-string
     */
    protected string $settingsFqn;

    protected bool $isGenerated;

    protected string $pagesNamespace;

    protected string $pagesDirectory;

    /**
     * @var array<string>
     */
    protected $aliases = [
        'filament:settings',
        'filament:settings-page',
        'make:filament-settings',
    ];

    /**
     * @return array<InputArgument>
     */
    protected function getArguments(): array
    {
        return [
            new InputArgument(
                name: 'name',
                mode: InputArgument::OPTIONAL,
                description: 'The name of the page to generate, optionally prefixed with directories',
            ),
            new InputArgument(
                name: 'settings',
                mode: InputArgument::OPTIONAL,
                description: 'The name of the settings class within the settings directory or fully-qualified',
            ),
        ];
    }

    /**
     * @return array<InputOption>
     */
    protected function getOptions(): array
    {
        return [
            new InputOption(
                name: 'cluster',
                shortcut: 'C',
                mode: InputOption::VALUE_OPTIONAL,
                description: 'The cluster to create the page in',
            ),
            new InputOption(
                name: 'generate',
                shortcut: 'G',
                mode: InputOption::VALUE_NONE,
                description: 'Generate the form schema based on the properties of the settings class',
            ),
            new InputOption(
                name: 'panel',
                shortcut: null,
                mode: InputOption::VALUE_REQUIRED,
                description: 'The panel to create the resource in',
            ),
            new InputOption(
                name: 'force',
                shortcut: 'F',
                mode: InputOption::VALUE_NONE,
                description: 'Overwrite the contents of the files if they already exist',
            ),
        ];
    }

    public function handle(): int
    {
        try {
            $this->configureFqnEnd();
            $this->configureSettingsFqn();
            $this->configureIsGenerated();
            $this->configurePanel(question: 'Which panel would you like to create this page in?');
            $this->configureCluster();
            $this->configurePagesLocation();

            $this->configureFqn();

            $this->createPage();
        } catch (FailureCommandOutput) {
            return static::FAILURE;
        }

        $this->components->info("Filament page [{$this->fqn}] created successfully.");

        if (empty($this->panel->getPageNamespaces())) {
            $this->components->info('Make sure to register the page with [pages()] or discover it with [discoverPages()] in the panel service provider.');
        }

        return static::SUCCESS;
    }

    protected function configureFqnEnd(): void
    {
        $this->fqnEnd = (string) str($this->argument('name') ?? text(
            label: 'What is the page name?',
            placeholder: 'ManageSettings',
            required: true,
        ))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->studly()
            ->replace('/', '\\');
    }

    protected function configureSettingsFqn(): void
    {
        $settingsFqn = (string) str($this->argument('settings'))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');

        if (class_exists(config('settings.setting_class_path', app_path('Settings')) . '\\' . $settingsFqn)) {
            $this->settingsFqn = config('settings.setting_class_path', app_path('Settings')) . '\\' . $settingsFqn;

            return;
        }

        if (class_exists($settingsFqn)) {
            $this->settingsFqn = $settingsFqn;

            return;
        }

        $settingsFqns = discover_app_classes(parentClass: Settings::class);

        $settingsFqns = array_combine(
            $settingsFqns,
            $settingsFqns,
        );

        $this->settingsFqn = suggest(
            label: 'What is the settings class?',
            options: function (?string $search) use ($settingsFqns): array {
                if (blank($search)) {
                    return $settingsFqns;
                }

                $search = str($search)->trim()->replace(['\\', '/'], '');

                return array_filter($settingsFqns, fn (string $modelFqn): bool => str($modelFqn)->replace(['\\', '/'], '')->contains($search, ignoreCase: true));
            },
            placeholder: app()->getNamespace() . 'Settings\\SiteSettings',
            hint: 'Please provide the fully-qualified class name.',
        );
    }

    protected function configureIsGenerated(): void
    {
        $this->isGenerated = $this->option('generate') || confirm(
            label: 'Should the form schema be generated from the properties of the settings class?',
            default: false,
        );
    }

    protected function configureCluster(): void
    {
        $this->configureClusterFqn(
            initialQuestion: 'Would you like to create this page in a cluster?',
            question: 'Which cluster would you like to create this page in?',
        );

        if (blank($this->clusterFqn)) {
            return;
        }

        $this->configureClusterPagesLocation();
    }

    protected function configurePagesLocation(): void
    {
        if (filled($this->clusterFqn)) {
            return;
        }

        $directories = $this->panel->getPageDirectories();
        $namespaces = $this->panel->getPageNamespaces();

        foreach ($directories as $index => $directory) {
            if (str($directory)->startsWith(base_path('vendor'))) {
                unset($directories[$index]);
                unset($namespaces[$index]);
            }
        }

        if (count($namespaces) < 2) {
            $this->pagesNamespace = (Arr::first($namespaces) ?? app()->getNamespace() . 'Filament\\Pages');
            $this->pagesDirectory = (Arr::first($directories) ?? app_path('Filament/Pages/'));

            return;
        }

        $keyedNamespaces = array_combine(
            $namespaces,
            $namespaces,
        );

        $this->pagesNamespace = search(
            label: 'Which namespace would you like to create this page in?',
            options: function (?string $search) use ($keyedNamespaces): array {
                if (blank($search)) {
                    return $keyedNamespaces;
                }

                $search = str($search)->trim()->replace(['\\', '/'], '');

                return array_filter($keyedNamespaces, fn (string $namespace): bool => str($namespace)->replace(['\\', '/'], '')->contains($search, ignoreCase: true));
            },
        );
        $this->pagesDirectory = $directories[array_search($this->pagesNamespace, $namespaces)];
    }

    protected function configureFqn(): void
    {
        $this->fqn = $this->pagesNamespace . '\\' . $this->fqnEnd;
    }

    protected function createPage(): void
    {
        $path = (string) str("{$this->pagesDirectory}\\{$this->fqnEnd}.php")
            ->replace('\\', '/')
            ->replace('//', '/');

        if (! $this->option('force') && $this->checkForCollision($path)) {
            throw new FailureCommandOutput;
        }

        $this->writeFile($path, app(SettingsPageClassGenerator::class, [
            'fqn' => $this->fqn,
            'settingsFqn' => $this->settingsFqn,
            'clusterFqn' => $this->clusterFqn,
            'isGenerated' => $this->isGenerated,
        ]));
    }
}
