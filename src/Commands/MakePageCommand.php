<?php

namespace Filament\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakePageCommand extends Command
{
    use Concerns\CanManipulateFiles;

    protected $description = 'Creates a Filament page class and view.';

    protected $signature = 'make:filament-page {name}';

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

        $view = Str::of($page)
            ->prepend('filament\\pages.')
            ->explode('\\')
            ->map(fn ($segment) => Str::kebab($segment))
            ->implode('.');

        $path = app_path(
            (string) Str::of($page)
                ->prepend('Filament\\Pages\\')
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

        $this->copyStubToApp('Page', $path, [
            'class' => $pageClass,
            'namespace' => 'App\Filament\Pages' . ($pageNamespace !== '' ? "\\{$pageNamespace}" : ''),
            'view' => $view,
        ]);

        $this->copyStubToApp('PageView', $viewPath);

        $this->info("Successfully created {$page}!");
    }
}
