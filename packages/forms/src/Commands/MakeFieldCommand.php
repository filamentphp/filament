<?php

namespace Filament\Forms\Commands;

use Filament\Forms\Commands\FileGenerators\FieldClassGenerator;
use Filament\Support\Commands\Concerns\CanAskForComponentLocation;
use Filament\Support\Commands\Concerns\CanAskForViewLocation;
use Filament\Support\Commands\Concerns\CanConfigureVite;
use Filament\Support\Commands\Concerns\CanManageJavaScriptPackages;
use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Filament\Support\Commands\Exceptions\FailureCommandOutput;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use function Laravel\Prompts\text;

#[AsCommand(name: 'make:filament-form-field', aliases: [
    'filament:field',
    'filament:form-field',
    'forms:field',
    'forms:make-field',
    'make:filament-field',
    'make:form-field',
])]
class MakeFieldCommand extends Command
{
    use CanAskForComponentLocation;
    use CanAskForViewLocation;
    use CanConfigureVite;
    use CanManageJavaScriptPackages;
    use CanManipulateFiles;

    protected $description = 'Create a new form field class and view or JavaScript renderer';

    protected $name = 'make:filament-form-field';

    /**
     * @var array<string>
     */
    protected $aliases = [
        'filament:field',
        'filament:form-field',
        'forms:field',
        'forms:make-field',
        'make:filament-field',
        'make:form-field',
    ];

    protected string $fqnEnd;

    protected string $fqn;

    protected string $path;

    protected string $view;

    protected string $viewPath;

    protected ?string $framework = null;

    protected bool $isTypeScript = false;

    protected string $renderer;

    /** @var array<string, string> */
    protected array $rendererFiles = [];

    protected Filesystem $filesystem;

    /**
     * @return array<InputArgument>
     */
    protected function getArguments(): array
    {
        return [
            new InputArgument(
                name: 'name',
                mode: InputArgument::OPTIONAL,
                description: 'The name of the field to generate, optionally prefixed with directories',
            ),
        ];
    }

    /**
     * @return array<InputOption>
     */
    protected function getOptions(): array
    {
        return [
            new InputOption('js', mode: InputOption::VALUE_NONE, description: 'Generate a framework-free JavaScript field'),
            new InputOption('react', mode: InputOption::VALUE_NONE, description: 'Generate a React field'),
            new InputOption('vue', mode: InputOption::VALUE_NONE, description: 'Generate a Vue field'),
            new InputOption('svelte', mode: InputOption::VALUE_NONE, description: 'Generate a Svelte 5 field'),
            new InputOption('typescript', mode: InputOption::VALUE_NONE, description: 'Generate a typed JavaScript field'),
            new InputOption('ts', mode: InputOption::VALUE_NONE, description: 'Alias for --typescript'),
            new InputOption('pm', mode: InputOption::VALUE_REQUIRED, description: 'The package manager to use (npm, yarn)'),
            new InputOption('skip-install', mode: InputOption::VALUE_NONE, description: 'Do not install JavaScript dependencies'),
            new InputOption('skip-build', mode: InputOption::VALUE_NONE, description: 'Do not offer to compile assets'),
            new InputOption(
                name: 'force',
                shortcut: 'F',
                mode: InputOption::VALUE_NONE,
                description: 'Overwrite the contents of the files if they already exist',
            ),
        ];
    }

    public function handle(Filesystem $filesystem): int
    {
        $this->filesystem = $filesystem;

        try {
            $frameworks = array_values(array_filter(['js', 'react', 'vue', 'svelte'], fn (string $framework): bool => (bool) $this->option($framework)));

            if (count($frameworks) > 1) {
                $this->components->error('Only one of --js, --react, --vue, or --svelte may be specified.');

                return static::FAILURE;
            }

            $this->framework = $framework = $frameworks[0] ?? null;
            $this->isTypeScript = $this->option('typescript') || $this->option('ts');
            $this->rendererFiles = [];

            if ($this->isTypeScript && ! $framework) {
                $this->components->error('Use --typescript or --ts with --js, --react, --vue, or --svelte.');

                return static::FAILURE;
            }

            $this->configureFqnEnd();

            $this->configureLocation();

            if (! $this->option('force') && $this->checkForCollision([
                $this->path,
                ...($framework ? array_keys($this->rendererFiles) : (filled($this->view) ? [$this->viewPath] : [])),
            ])) {
                throw new FailureCommandOutput;
            }

            if ($framework) {
                $this->configurePackageManager();
                $this->installJavaScriptDependencies($this->getJavaScriptRendererDependencies(
                    $framework,
                    $this->isTypeScript,
                    $this->getViteVersion(),
                ));
            }

            $this->createField();

            if ($this->framework) {
                $this->createRenderer();
            } else {
                $this->createView();
            }
        } catch (FailureCommandOutput) {
            return static::FAILURE;
        }

        $this->components->info("Filament form field [{$this->fqn}] created successfully.");

        if ($this->framework) {
            $pendingActions = [];

            if ($this->isTypeScript && ! $this->configureTypeScript()) {
                $pendingActions[] = 'Configure the @filament/forms/js-field type alias in tsconfig.json: https://filamentphp.com/docs/4.x/forms/custom-fields#typing-renderers';
            }

            if (! $this->registerViteInput($this->renderer)) {
                $pendingActions[] = "Add [{$this->renderer}] to the Laravel plugin's input array in your Vite config.";
            }

            if (! $this->configureRendererViteConfig()) {
                $pendingActions[] = "Configure Vite to compile {$this->framework} and preserve the renderer's default export: https://filamentphp.com/docs/4.x/advanced/assets#building-lazy-loaded-es-modules";
            }

            if (filled($pendingActions)) {
                $this->components->warn('Action is required to complete the field setup:');
                $this->components->bulletList($pendingActions);
            }

            if (! glob(base_path('vite.config.*s'))) {
                return static::SUCCESS;
            }

            return $this->buildJavaScriptAssets('field') ? static::SUCCESS : static::FAILURE;
        }

        return static::SUCCESS;
    }

    protected function configureFqnEnd(): void
    {
        $this->fqnEnd = (string) str($this->argument('name') ?? text(
            label: 'What is the field name?',
            placeholder: 'RangeSlider',
            required: true,
        ))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->studly()
            ->replace('/', '\\');
    }

    protected function configureLocation(): void
    {
        [
            $namespace,
            $path,
            $viewNamespace,
        ] = $this->askForComponentLocation(
            path: 'Forms/Components',
            question: 'Where would you like to create the field?',
        );

        $this->fqn = "{$namespace}\\{$this->fqnEnd}";
        $this->path = (string) str("{$path}\\{$this->fqnEnd}.php")
            ->replace('\\', '/')
            ->replace('//', '/');

        if ($this->framework) {
            $this->configureRendererLocation();

            return;
        }

        [
            $this->view,
            $this->viewPath,
        ] = $this->askForViewLocation(
            str($this->fqn)
                ->afterLast('\\Forms\\Components\\')
                ->prepend('Filament\\Forms\\Components\\')
                ->replace('\\', '/')
                ->explode('/')
                ->map(Str::kebab(...))
                ->implode('.'),
            defaultNamespace: $viewNamespace,
        );
    }

    protected function createField(): void
    {
        $this->writeFile($this->path, app(FieldClassGenerator::class, [
            'fqn' => $this->fqn,
            'view' => $this->framework ? '' : $this->view,
            ...($this->framework ? ['renderer' => $this->renderer] : []),
        ]));
    }

    protected function configureRendererLocation(): void
    {
        $directory = 'js/filament/forms/components';
        $name = str($this->fqnEnd)->replace('\\', '/');

        if ($name->contains('/')) {
            $directory .= '/' . $name->beforeLast('/')->explode('/')->map(Str::kebab(...))->implode('/');
        }

        $basename = $name->afterLast('/')->toString();
        $extension = match ($this->framework) {
            'react' => $this->isTypeScript ? 'tsx' : 'jsx',
            'svelte' => $this->isTypeScript ? 'svelte.ts' : 'svelte.js',
            default => $this->isTypeScript ? 'ts' : 'js',
        };

        $entry = "{$directory}/" . Str::kebab($basename) . ".{$extension}";
        $this->renderer = 'resources/' . $entry;
        $stubPrefix = ucfirst($this->framework) . ($this->isTypeScript ? 'TypeScript' : '');
        $this->rendererFiles = [resource_path($entry) => $stubPrefix . 'FieldRenderer'];

        if (in_array($this->framework, ['vue', 'svelte'])) {
            $this->rendererFiles[resource_path("{$directory}/{$basename}.{$this->framework}")] = $stubPrefix . 'FieldComponent';
        }
    }

    protected function createRenderer(): void
    {
        foreach ($this->rendererFiles as $path => $stub) {
            $this->copyStubToApp($stub, $path, [
                'componentName' => class_basename($this->fqn),
            ]);
        }
    }

    protected function configureRendererViteConfig(): bool
    {
        return $this->configureJavaScriptRendererVite($this->framework);
    }

    protected function configureTypeScript(): bool
    {
        return $this->configureJavaScriptRendererTypeScript(
            '@filament/forms/js-field',
            'vendor/filament/forms/resources/js/types/js-field.d.ts',
        );
    }

    protected function createView(): void
    {
        if (blank($this->view)) {
            return;
        }

        $this->copyStubToApp('FieldView', $this->viewPath);
    }
}
