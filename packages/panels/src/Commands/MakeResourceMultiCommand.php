<?php

namespace Filament\Commands;

use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\multiselect;

#[AsCommand(name: 'make:filament-resource-multi')]
class MakeResourceMultiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:filament-resource-multi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Filament resource class and default page classes for multiple models';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $models = $this->getModels()->map(function ($model) {
            return array_reverse(explode('\\', $model))[0];
        });
        $this->info($models->count() . ' Found. Please Select Which Model You Want To Generate Resource For');
        $selectedModels = multiselect(
            label: 'Select Models',
            options: $models->toArray(),
            required: 'Please Select At least 1 Model or CTRL+C to Exit',
            hint: 'Press Space to Select, Enter to Confirm'
        );
        $options = [
            '--soft-deletes' => 'Enable Soft Deletes (--soft-deletes)',
            '--view' => 'Generate Views (--view)',
            '--generate' => 'Generate All (--generate)',
            '--simple' => 'Generate Simple (--simple)',
            '--model' => 'Model Name (--model)',
            '--migration' => 'Generate Migration (--migration)',
            '--factory' => 'Generate Factory (--factory)',
            '--force' => 'Force (--force)',
        ];
        $selectedOptions = multiselect(
            label: 'Select Options',
            options: $options,
            required: 'Please Select At least 1 Option or CTRL+C to Exit',
            hint: 'Press Space to Select, Enter to Confirm'
        );

        foreach ($selectedModels as $model) {
            $this->call('make:filament-resource', [
                'name' => $model,
                '--soft-deletes' => in_array('--soft-deletes', $selectedOptions),
                '--view' => in_array('--view', $selectedOptions),
                '--generate' => in_array('--generate', $selectedOptions),
                '--simple' => in_array('--simple', $selectedOptions),
                '--model' => in_array('--model', $selectedOptions),
                '--migration' => in_array('--migration', $selectedOptions),
                '--factory' => in_array('--factory', $selectedOptions),
                '--force' => in_array('--force', $selectedOptions),
            ]);
        }
    }

    protected function getModels(): Collection
    {
        $models = collect(File::allFiles(app_path()))
            ->map(function ($item) {
                $path = $item->getRelativePathName();
                $class = sprintf(
                    '\%s%s',
                    Container::getInstance()->getNamespace(),
                    strtr(substr($path, 0, strrpos($path, '.')), '/', '\\')
                );

                return $class;
            })
            ->filter(function ($class) {
                $valid = false;

                if (class_exists($class)) {
                    $reflection = new \ReflectionClass($class);
                    $valid = $reflection->isSubclassOf(Model::class) &&
                        ! $reflection->isAbstract();
                }

                return $valid;
            });

        return $models->values();
    }
}
