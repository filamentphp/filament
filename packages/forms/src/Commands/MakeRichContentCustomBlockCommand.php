<?php

namespace Filament\Forms\Commands;

use Filament\Forms\Commands\FileGenerators\RichContentCustomBlockClassGenerator;
use Filament\Support\Commands\Concerns\CanAskForComponentLocation;
use Filament\Support\Commands\Concerns\CanAskForViewLocation;
use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Filament\Support\Commands\Exceptions\FailureCommandOutput;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use function Laravel\Prompts\text;

#[AsCommand(name: 'make:filament-rich-content-custom-block', aliases: [
    'filament:rich-content-custom-block',
    'filament:rich-editor-custom-block',
    'filament:custom-block',
    'forms:rich-content-custom-block',
    'forms:rich-editor-custom-block',
    'forms:make-custom-block',
    'make:filament-rich-editor-custom-block',
    'make:filament-custom-block',
    'make:rich-content-custom-block',
    'make:rich-editor-custom-block',
    'make:custom-block',
])]
class MakeRichContentCustomBlockCommand extends Command
{
    use CanAskForComponentLocation;
    use CanAskForViewLocation;
    use CanManipulateFiles;

    protected $description = 'Create a new rich editor custom block class and view';

    protected $name = 'make:filament-rich-content-custom-block';

    /**
     * @var array<string>
     */
    protected $aliases = [
        'filament:rich-content-custom-block',
        'filament:rich-editor-custom-block',
        'filament:custom-block',
        'forms:rich-content-custom-block',
        'forms:rich-editor-custom-block',
        'forms:make-custom-block',
        'make:filament-rich-editor-custom-block',
        'make:filament-custom-block',
        'make:rich-content-custom-block',
        'make:rich-editor-custom-block',
        'make:custom-block',
    ];

    protected string $fqnEnd;

    protected string $fqn;

    protected string $path;

    protected string $view;

    protected string $previewView;

    protected string $viewPath;

    protected string $previewViewPath;

    /**
     * @return array<InputArgument>
     */
    protected function getArguments(): array
    {
        return [
            new InputArgument(
                name: 'name',
                mode: InputArgument::OPTIONAL,
                description: 'The name of the custom block to generate, optionally prefixed with directories',
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

            $this->configureLocation();

            $this->createCustomBlock();
            $this->createView();
            $this->createPreviewView();
        } catch (FailureCommandOutput) {
            return static::FAILURE;
        }

        $this->components->info("Filament rich editor custom block [{$this->fqn}] created successfully.");

        return static::SUCCESS;
    }

    protected function configureFqnEnd(): void
    {
        $this->fqnEnd = (string) str($this->argument('name') ?? text(
            label: 'What is the custom block name?',
            placeholder: 'HeroBlock',
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
            path: 'Forms/Components/RichEditor/RichContentCustomBlocks',
            question: 'Where would you like to create the custom block?',
        );

        $this->fqn = "{$namespace}\\{$this->fqnEnd}";
        $this->path = (string) str("{$path}\\{$this->fqnEnd}.php")
            ->replace('\\', '/')
            ->replace('//', '/');

        [
            $view,
            $viewPath,
        ] = $this->askForViewLocation(
            str($this->fqn)
                ->afterLast('\\Forms\\Components\\RichEditor\\RichContentCustomBlocks\\')
                ->prepend('Filament\\Forms\\Components\\RichEditor\\RichContentCustomBlocks\\')
                ->whenEndsWith('Block', fn (Stringable $stringable) => $stringable->beforeLast('Block'))
                ->replace('\\', '/')
                ->explode('/')
                ->map(Str::kebab(...))
                ->implode('.'),
            defaultNamespace: $viewNamespace,
        );
        $this->view = str($view)->append('.index');
        $this->viewPath = str($viewPath)->replaceLast('.blade.php', '/index.blade.php');
        $this->previewView = str($view)->append('.preview');
        $this->previewViewPath = str($viewPath)->replaceLast('.blade.php', '/preview.blade.php');
    }

    protected function createCustomBlock(): void
    {
        if (! $this->option('force') && $this->checkForCollision($this->path)) {
            throw new FailureCommandOutput;
        }

        $this->writeFile($this->path, app(RichContentCustomBlockClassGenerator::class, [
            'fqn' => $this->fqn,
            'view' => $this->view,
            'previewView' => $this->previewView,
        ]));
    }

    protected function createView(): void
    {
        if (blank($this->view)) {
            return;
        }

        if (! $this->option('force') && $this->checkForCollision($this->viewPath)) {
            throw new FailureCommandOutput;
        }

        $this->copyStubToApp('RichContentCustomBlockView', $this->viewPath);
    }

    protected function createPreviewView(): void
    {
        if (blank($this->previewViewPath)) {
            return;
        }

        if (! $this->option('force') && $this->checkForCollision($this->previewViewPath)) {
            throw new FailureCommandOutput;
        }

        $this->copyStubToApp('RichContentCustomBlockPreviewView', $this->previewViewPath);
    }
}
