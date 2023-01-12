<?php

namespace Filament\Forms\Commands;

use Filament\Forms\Commands\Concerns\CanGenerateForms;
use Filament\Support\Commands\Concerns\CanIndentStrings;
use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Filament\Support\Commands\Concerns\CanReadModelSchemas;
use Filament\Support\Commands\Concerns\CanValidateInput;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeFormCommand extends Command
{
    use CanGenerateForms;
    use CanIndentStrings;
    use CanManipulateFiles;
    use CanReadModelSchemas;
    use CanValidateInput;

    protected $description = 'Creates a Livewire component containing a Filament form.';

    protected $signature = 'make:livewire-form {name?} {model?} {--E|edit} {--G|generate} {--F|force}';

    public function handle(): int
    {
        $component = (string) str($this->argument('name') ?? $this->askRequired('Name (e.g. `Products/CreateProduct`)', 'name'))
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

        $model = (string) str($this->argument('model') ?? (
            $this->option('edit') ?
            $this->askRequired('Model (e.g. `Product`)', 'model') :
            $this->ask('(Optional) Model (e.g. `Product`)')
        ))->replace('/', '\\');
        $modelClass = (string) str($model)->afterLast('\\');

        if ($this->option('edit')) {
            $isEditForm = true;
        } elseif (filled($model)) {
            $isEditForm = $this->choice('Operation', [
                'Create',
                'Edit',
            ]) === 'Edit';
        } else {
            $isEditForm = false;
        }

        $path = (string) str($component)
            ->prepend('/')
            ->prepend(app_path('Http/Livewire/'))
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
            'namespace' => 'App\\Http\\Livewire' . ($componentNamespace !== '' ? "\\{$componentNamespace}" : ''),
            'schema' => $this->indentString((filled($model) && $this->option('generate')) ? $this->getResourceFormSchema(
                'App\\Models\\' . $model,
            ) : '//', 4),
            'view' => $view,
        ]);

        $this->copyStubToApp('FormView', $viewPath, [
            'submitAction' => filled($model) ? ($isEditForm ? 'save' : 'create') : 'submit',
        ]);

        $this->components->info("Successfully created {$component}!");

        return static::SUCCESS;
    }
}
