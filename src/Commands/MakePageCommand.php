<?php

namespace Filament\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakePageCommand extends Command
{
    use Concerns\CanManipulateFiles;

    protected $description = 'Creates a Filament page class and view.';

    protected $signature = 'make:filament-page {name} {--R|resource=}';

    public function handle()
    {
        $page = (string) Str::of($this->argument('name'))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');
        $pageClass = (string) Str::of($page)->afterLast('\\');
        $pageNamespace = Str::of($page)->contains('\\') ?
            (string) Str::of($page)->beforeLast('\\') :
            '';

        $resource = null;
        $resourceClass = null;

        if ($this->option('resource') !== null) {
            $resource = (string) Str::of($this->option('resource'))
                ->trim('/')
                ->trim('\\')
                ->trim(' ')
                ->replace('/', '\\');

            $resourceClass = (string) Str::of($resource)
                ->afterLast('\\');
        }

        $view = Str::of($page)
            ->prepend($resource === null ? 'filament\\pages\\' : "filament\\resources\\{$resource}\\pages\\")
            ->explode('\\')
            ->map(fn ($segment) => Str::kebab($segment))
            ->implode('.');

        $path = app_path(
            (string) Str::of($page)
                ->prepend($resource === null ? 'Filament\\Pages\\' : "Filament\\Resources\\{$resource}\\Pages\\")
                ->replace('\\', '/')
                ->append('.php'),
        );
        $viewPath = resource_path(
            (string) Str::of($view)
                ->replace('.', '/')
                ->prepend('views/')
                ->append('.blade.php'),
        );

        if ($this->checkForCollision([
            $path,
            $viewPath,
        ])) return;

        if ($resource === null) {
            $this->copyStubToApp('Page', $path, [
                'class' => $pageClass,
                'namespace' => 'App\\Filament\\Pages' . ($pageNamespace !== '' ? "\\{$pageNamespace}" : ''),
                'view' => $view,
            ]);
        } else {
            $this->copyStubToApp('ResourcePage', $path, [
                'baseResourcePage' => 'Filament\\Resources\\Page',
                'baseResourcePageClass' => 'Page',
                'namespace' => "App\\Filament\\Resources\\{$resource}\\Pages" . ($pageNamespace !== '' ? "\\{$pageNamespace}" : ''),
                'resource' => $resource,
                'resourceClass' => $resourceClass,
                'resourcePageClass' => $pageClass,
                'view' => $view,
            ]);
        }

        $this->copyStubToApp('PageView', $viewPath);

        $this->info("Successfully created {$page}!");
    }
}
