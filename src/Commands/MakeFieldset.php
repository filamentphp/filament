<?php

namespace Filament\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeFieldset extends Command
{
    protected $signature = 'filament:fieldset {name} {--package}';
    protected $description = 'Make a new Filament fieldset.';

    public function handle()
    {
        $fieldsetName = Str::studly($this->argument('name')).'Fieldset';

        $stub = File::get(__DIR__.'/../../resources/stubs/fieldset.stub');
        $stub = str_replace('DummyFieldset', $fieldsetName, $stub);
        $stub = str_replace('Dummy', $this->argument('name'), $stub);
        
        if (! $this->option('package')) {
            $stub = str_replace('namespace Filament', 'namespace App', $stub);
        }

        $fieldsetPath = $this->option('package') ? __DIR__.'/../Http/Fieldsets/' : app_path('Http/Filament/Fieldsets/');
        $path = $fieldsetPath.$fieldsetName.'.php';

        if (!File::exists($path) || $this->confirm($fieldsetName.' already exists. Overwrite it?')) {
            File::put($path, $stub);
            $this->info($fieldsetName.' was created in '. $fieldsetPath);
        }
    }
}
