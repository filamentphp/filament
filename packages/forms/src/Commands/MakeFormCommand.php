<?php

namespace Filament\Forms\Commands;

use Filament\Forms\Commands\Concerns\CanGenerateForms;
use Filament\Support\Commands\Concerns\CanIndentStrings;
use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Filament\Support\Commands\Concerns\CanReadModelSchemas;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class MakeFormCommand extends Command
{
    use CanGenerateForms;
    use CanIndentStrings;
    use CanManipulateFiles;
    use CanReadModelSchemas;

    protected $description = 'Create a new Livewire component containing a Filament form';

    protected $signature = 'make:livewire-form {name?} {model?} {--E|edit} {--G|generate} {--F|force}';

    public function handle(): int
    {
        $component = (string) str($this->argument('name') ?? text(
            label: 'What is the form name?',
            placeholder: 'Products/CreateProduct',
            required: true,
        ))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');
        $componentClass = (string) str($component)->afterLast('\\');
        $componentNamespace = str($component)->contains('\\') ?
            (string) str($component)->beforeLast('\\') :
            '';

        $view = str($component)
            ->replace('\\', '/')
            ->prepend('Livewire/')
            ->explode('/')
            ->map(fn ($segment) => Str::lower(Str::kebab($segment)))
            ->implode('.');

        $model = (string) str($this->argument('model') ??
                text(
                    label: 'What is the model name?',
                    placeholder: 'Product',
                    required: $this->option('edit')
                ))->replace('/', '\\');
        $modelClass = (string) str($model)->afterLast('\\');

        if ($this->option('edit')) {
            $isEditForm = true;
        } elseif (filled($model)) {
            $isEditForm = select(
                label: 'Which namespace would you like to create this in?',
                options: [
                    'Create',
                    'Edit',
                ]
            ) === 'Edit';
        } else {
            $isEditForm = false;
        }

        $path = (string) str($component)
            ->prepend('/')
            ->prepend(app_path('Livewire/'))
            ->replace('\\', '/')
            ->replace('//', '/')
            ->append('.php');

        $viewPath = resource_path(
            (string) str($view)
                ->replace('.', '/')
                ->prepend('views/')
                ->append('.blade.php'),
        );

        if (! $this->option('force') && $this->checkForCollision([$path, $viewPath])) {
            return static::INVALID;
        }

        $this->copyStubToApp(filled($model) ? ($isEditForm ? 'EditForm' : 'CreateForm') : 'Form', $path, [
            'class' => $componentClass,
            'model' => $model,
            'modelClass' => $modelClass,
            'namespace' => 'App\\Livewire' . ($componentNamespace !== '' ? "\\{$componentNamespace}" : ''),
            'schema' => $this->indentString((filled($model) && $this->option('generate')) ? $this->getResourceFormSchema(
                'App\\Models\\' . $model,
            ) : '//', 4),
            'view' => $view,
        ]);

        $this->copyStubToApp('FormView', $viewPath, [
            'submitAction' => filled($model) ? ($isEditForm ? 'save' : 'create') : 'submit',
        ]);

        $this->components->info("Filament form [{$path}] created successfully.");

        return static::SUCCESS;
    }
}
