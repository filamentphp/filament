<?php

use Filament\Support\Commands\Concerns\CanConfigureVite;
use Filament\Support\Commands\Concerns\CanManageJavaScriptPackages;
use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Filament\Tests\TestCase;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;

uses(TestCase::class)->group('serial');

it('shares renderer setup without duplicating plugins or replacing another type alias', function (): void {
    $paths = ['vite.config.js', 'tsconfig.json', 'node_modules/vite/package.json'];
    $original = [];
    foreach ($paths as $path) {
        $original[$path] = File::exists(base_path($path)) ? File::get(base_path($path)) : null;
    }

    try {
        File::ensureDirectoryExists(base_path('node_modules/vite'));
        File::put(base_path('node_modules/vite/package.json'), '{"version":"8.3.0"}');
        File::put(base_path('vite.config.js'), "import { defineConfig } from 'vite'\nexport default defineConfig({ plugins: [] })");
        File::put(base_path('tsconfig.json'), json_encode(['compilerOptions' => ['jsx' => 'preserve', 'paths' => ['@app/*' => ['./resources/js/*']]]]));
        $command = app(JavaScriptRendererSetupCommand::class);

        foreach (['vue', 'svelte', 'react', 'js', 'vue', 'svelte'] as $framework) {
            expect($command->configureJavaScriptRendererVite($framework))->toBeTrue();
            expect($command->getJavaScriptRendererDependencies($framework, true, '8.3.0'))->toContain('typescript@^6.0');
        }
        foreach (['first', 'second', 'first'] as $kind) {
            expect($command->configureJavaScriptRendererTypeScript("@example/{$kind}", "vendor/example/{$kind}/renderer.d.ts"))->toBeTrue();
        }

        $vite = File::get(base_path('vite.config.js'));
        expect(substr_count($vite, 'filamentVue()'))->toBe(1)
            ->and(substr_count($vite, 'filamentSvelte()'))->toBe(1)
            ->and(substr_count($vite, 'preserveEntrySignatures'))->toBe(1);
        expect(File::json(base_path('tsconfig.json'))['compilerOptions'])->toBe([
            'jsx' => 'preserve',
            'paths' => [
                '@app/*' => ['./resources/js/*'],
                '@example/first' => ['./vendor/example/first/renderer.d.ts'],
                '@example/second' => ['./vendor/example/second/renderer.d.ts'],
            ],
        ]);
    } finally {
        foreach ($original as $path => $contents) {
            if ($contents === null) {
                File::delete(base_path($path));
            } else {
                File::put(base_path($path), $contents);
            }
        }
    }
});

it('leaves commented Vite configurations unchanged for manual setup', function (string $framework, string $comment): void {
    $path = base_path('vite.config.js');
    $original = File::exists($path) ? File::get($path) : null;
    $import = $framework === 'vue'
        ? "import frameworkPlugin from '@vitejs/plugin-vue'"
        : "import { svelte as frameworkPlugin } from '@sveltejs/vite-plugin-svelte'";
    $contents = match ($comment) {
        'import' => "// {$import}\nexport default defineConfig({ plugins: [] })",
        'plugin' => "{$import}\nexport default defineConfig({ plugins: [/* frameworkPlugin() */] })",
        'input' => "export default defineConfig({ plugins: [laravel({ input: ['resources/js/app.js' /*, 'resources/js/field.js' */] })] })",
    };

    try {
        File::put($path, $contents);
        $command = app(JavaScriptRendererSetupCommand::class);

        expect($command->configureJavaScriptRendererVite($framework))->toBeFalse()
            ->and(File::get($path))->toBe($contents)
            ->and($command->registerViteInput('resources/js/field.js'))->toBeFalse()
            ->and(File::get($path))->toBe($contents);
    } finally {
        if ($original === null) {
            File::delete($path);
        } else {
            File::put($path, $original);
        }
    }
})->with(['vue', 'svelte'])->with(['import', 'plugin', 'input']);

it('leaves ambiguous Vite build configuration unchanged', function (string $properties): void {
    $path = base_path('vite.config.js');
    $original = File::exists($path) ? File::get($path) : null;
    $contents = "export default defineConfig({ {$properties}, plugins: [] })";

    try {
        File::put($path, $contents);

        expect(app(JavaScriptRendererSetupCommand::class)->configureJavaScriptRendererVite('js'))->toBeFalse()
            ->and(File::get($path))->toBe($contents);
    } finally {
        if ($original === null) {
            File::delete($path);
        } else {
            File::put($path, $original);
        }
    }
})->with([
    'spread' => '...sharedConfiguration',
    'computed property' => '[buildKey]: sharedBuild',
    'single-quoted build' => "'build': { outDir: 'public/assets' }",
    'double-quoted build' => '"build": { outDir: "public/assets" }',
    'shorthand build' => 'build',
    'build accessor' => 'get build() { return sharedBuild }',
    'quoted build accessor' => "get 'build'() { return sharedBuild }",
]);

class JavaScriptRendererSetupCommand extends Command
{
    use CanConfigureVite {
        configureJavaScriptRendererVite as public;
        configureJavaScriptRendererTypeScript as public;
        registerViteInput as public;
    }
    use CanManageJavaScriptPackages {
        getJavaScriptRendererDependencies as public;
    }
    use CanManipulateFiles;

    public function __construct(protected Filesystem $filesystem)
    {
        parent::__construct();
    }
}
