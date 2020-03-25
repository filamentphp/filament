<?php

namespace Filament\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

use Filament;

class MakeForm extends Command
{
    protected $signature = 'filament:form {name} {--model=Model}';
    protected $description = 'Make a new Filament Livewire form component.';

    public function handle()
    {
        $stub = File::get(__DIR__ . '/../../resources/stubs/component.stub');
        $stub = str_replace('DummyComponent', $this->argument('name'), $stub);
        $stub = str_replace('Contracts\Dummy', 'Contracts\\' . $this->option('model'), $stub);
        $stub = str_replace('DummyClass', lcFirst($this->option('model')).'Class', $stub);
        $stub = str_replace('DummyContract', $this->option('model').'Contract', $stub);
        $stub = str_replace('DummyRoute', Str::slug(Str::plural($this->option('model'))), $stub);

        $path = __DIR__ . '/../Http/Livewire/' . $this->argument('name') . '.php';

        if (!File::exists($path) || $this->confirm($this->argument('name') . ' already exists. Overwrite it?')) {
            File::put($path, $stub);
            $this->info($this->argument('name') . ' was made!');
        }
    }
}
