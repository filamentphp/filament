<?php

namespace Filament\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeForm extends Command
{
    protected $signature = 'filament:form {name} {--model=Model} {--package}';
    protected $description = 'Make a new Filament Livewire form component.';

    public function handle()
    {
        $componentName = Str::studly($this->argument('name'));

        $stub = File::get(__DIR__.'/../../resources/stubs/component.stub');
        $stub = str_replace('DummyComponent', $componentName, $stub);
        $stub = str_replace('Contracts\Dummy', 'Contracts\\'.$this->option('model'), $stub);
        $stub = str_replace('DummyClass', lcFirst($this->option('model')).'Class', $stub);
        $stub = str_replace('DummyContract', $this->option('model').'Contract', $stub);
        $stub = str_replace('DummyRoute', Str::slug(Str::plural($this->option('model'))), $stub);
        $stub = str_replace('DummyView', Str::kebab($componentName), $stub);
        
        if (!$this->option('package')) {
            $stub = str_replace('namespace Filament', 'namespace App', $stub);
        }

        $formPath = $this->option('package') ? __DIR__.'/../Http/Livewire/' : app_path('Http/Livewire/');
        $path = $formPath.$componentName.'.php';

        if (!File::exists($path) || $this->confirm($componentName.' already exists. Overwrite it?')) {
            File::put($path, $stub);
            $this->info($componentName.' was created in '. $formPath);
        }
    }
}
