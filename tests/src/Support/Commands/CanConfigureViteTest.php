<?php

use Filament\Support\Commands\Concerns\CanConfigureVite;
use Filament\Tests\TestCase;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;

uses(TestCase::class)->group('serial');

it('leaves commented Vite configurations unchanged when registering an input', function (string $comment): void {
    $path = base_path('vite.config.js');
    $original = File::exists($path) ? File::get($path) : null;
    $contents = match ($comment) {
        'import' => "// import { defineConfig } from 'vite'\nexport default defineConfig({ plugins: [] })",
        'plugin' => 'export default defineConfig({ plugins: [/* laravel() */] })',
        'input' => "export default defineConfig({ plugins: [laravel({ input: ['resources/js/app.js' /*, 'resources/css/filament/admin/theme.css' */] })] })",
    };

    try {
        File::put($path, $contents);
        $command = app(ViteInputSetupCommand::class);

        expect($command->registerViteInput('resources/css/filament/admin/theme.css'))->toBeFalse()
            ->and(File::get($path))->toBe($contents);
    } finally {
        if ($original === null) {
            File::delete($path);
        } else {
            File::put($path, $original);
        }
    }
})->with(['import', 'plugin', 'input']);

class ViteInputSetupCommand extends Command
{
    use CanConfigureVite {
        registerViteInput as public;
    }

    public function __construct(protected Filesystem $filesystem)
    {
        parent::__construct();
    }
}
