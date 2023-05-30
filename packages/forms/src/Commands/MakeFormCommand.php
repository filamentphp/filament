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

    protected $description = 'Create a new Livewire component containing a Filament form';

    protected $signature = 'make:livewire-form {name?} {model?} {--E|edit} {--G|generate} {--F|force}';

    public function handle(): int
    {
        $component = (string) Str::of($this->argument('name') ?? $this->askRequired('Name (e.g. `Products/CreateProduct`)', 'name'))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');
        $componentClass = (string) Str::of($component)->afterLast('\\');
        $componentNamespace = Str::of($component)->contains('\\') ?
            (string) Str::of($component)->beforeLast('\\') :
            '';

        $view = Str::of($component)
            ->replace('\\', '/')
            ->prepend('Livewire/')
            ->explode('/')
            ->map(fn ($segment) => Str::lower(Str::kebab($segment)))
            ->implode('.');

        $model = (string) Str::of($this->argument('model') ?? $this->ask('(Optional) Model (e.g. `Product`)'))
            ->replace('/', '\\');
        $modelClass = (string) Str::of($model)->afterLast('\\');

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

        $path = (string) Str::of($component)
            ->prepend('/')
            ->prepend(app_path('Http/Livewire/'))
            ->replace('\\', '/')
            ->replace('//', '/')
            ->append('.php');

        $viewPath = resource_path(
            (string) Str::of($view)
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
            ) : '//', 3),
            'view' => $view,
        ]);

        $this->copyStubToApp('FormView', $viewPath, [
            'submitAction' => filled($model) ? ($isEditForm ? 'save' : 'create') : 'submit',
        ]);

        $this->info("Successfully created {$component}!");

        return static::SUCCESS;
    }
}
