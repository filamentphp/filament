<?php

namespace Filament\Support\Commands\Concerns;

use Filament\Support\Commands\Exceptions\FailureCommandOutput;
use JsonException;

trait CanConfigureVite
{
    protected function configureJavaScriptRendererVite(string $framework): bool
    {
        $path = base_path('vite.config.js');

        if (! $this->filesystem->exists($path)) {
            return false;
        }

        $contents = $this->filesystem->get($path);
        $configPattern = '/\bexport\s+default\s+defineConfig\(\s*\{/';

        // Leave commented or computed configurations and existing build customization to the user.
        if ($this->hasUnsupportedViteSyntax($contents) || (! preg_match($configPattern, $contents))) {
            return false;
        }

        if (preg_match('/\bbuild\s*:/', $contents)) {
            if (! preg_match('/\bpreserveEntrySignatures\s*:\s*[\'"](?:exports-only|strict)[\'"]/', $contents)) {
                return false;
            }
        } else {
            $options = version_compare($this->getViteVersion(), '8.0.0', '>=') ? 'rolldownOptions' : 'rollupOptions';
            $contents = preg_replace($configPattern, '$0' . "\n    build: {\n        {$options}: { preserveEntrySignatures: 'exports-only' },\n    },", $contents, 1);
        }

        if (in_array($framework, ['vue', 'svelte'])) {
            $package = $framework === 'vue' ? '@vitejs/plugin-vue' : '@sveltejs/vite-plugin-svelte';
            $importPattern = $framework === 'vue'
                ? '/import\s+(\w+)\s+from\s+[\'"]@vitejs\/plugin-vue[\'"]/'
                : '/import\s*\{\s*(svelte)(?:\s+as\s+(\w+))?\s*\}\s*from\s*[\'"]@sveltejs\/vite-plugin-svelte[\'"]/';

            if (preg_match($importPattern, $contents, $matches)) {
                $plugin = $matches[2] ?? $matches[1];
                $import = '';
            } else {
                $plugin = $framework === 'vue' ? 'filamentVue' : 'filamentSvelte';

                if (str_contains($contents, $package) || preg_match('/\b' . $plugin . '\b/', $contents)) {
                    return false;
                }

                $import = $framework === 'vue'
                    ? "import {$plugin} from '{$package}'\n"
                    : "import { svelte as {$plugin} } from '{$package}'\n";
            }

            // Match nested arrays, such as the Laravel plugin's `input` array.
            if (preg_match_all('/\bplugins\s*:\s*(?<plugins>\[(?:[^\[\]]|(?&plugins))*\])/', $contents, $pluginArrays) !== 1) {
                return false;
            }

            if (! preg_match('/\b' . preg_quote($plugin, '/') . '\s*\(/', $pluginArrays['plugins'][0])) {
                $contents = preg_replace('/\bplugins\s*:\s*\[/', '$0' . "\n        {$plugin}(),", $contents, 1);
            }

            $contents = $import . $contents;
        }

        $this->filesystem->put($path, $contents);

        return true;
    }

    protected function configureJavaScriptRendererTypeScript(string $alias, string $declarationPath): bool
    {
        $path = base_path('tsconfig.json');

        try {
            $configuration = $this->filesystem->exists($path)
                ? json_decode($this->filesystem->get($path), associative: true, flags: JSON_THROW_ON_ERROR)
                : [
                    'compilerOptions' => [
                        'target' => 'ES2020',
                        'module' => 'ESNext',
                        'moduleResolution' => 'Bundler',
                        'jsx' => 'react-jsx',
                        'strict' => true,
                        'noEmit' => true,
                    ],
                    'include' => ['resources/js/**/*'],
                ];
        } catch (JsonException) {
            return false;
        }

        // Leave JSONC, inherited configurations and existing aliases to the user.
        if (! is_array($configuration) || isset($configuration['extends'])) {
            return false;
        }

        if (isset($configuration['compilerOptions']['paths'][$alias])) {
            return true;
        }

        $configuration['compilerOptions']['paths'][$alias] = [
            './' . $this->getRelativePath(
                base_path($declarationPath),
                base_path($configuration['compilerOptions']['baseUrl'] ?? ''),
            ),
        ];

        $this->filesystem->put($path, json_encode($configuration, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");

        return true;
    }

    protected function getViteVersion(): string
    {
        $path = base_path('node_modules/vite/package.json');

        if (! $this->filesystem->exists($path)) {
            $this->components->error('Install your application\'s existing JavaScript dependencies, including Vite, before generating a JavaScript renderer.');

            throw new FailureCommandOutput;
        }

        return $this->filesystem->json($path)['version'];
    }

    protected function registerViteInput(string $entry): bool
    {
        $viteConfigPath = base_path('vite.config.js');

        if (! $this->filesystem->exists($viteConfigPath)) {
            return false;
        }

        $contents = $this->filesystem->get($viteConfigPath);

        if ($this->hasUnsupportedViteSyntax($contents)) {
            return false;
        }

        if (str_contains($contents, $entry)) {
            return true;
        }

        $pattern = '/(\binput\s*:\s*\[)([^\]]*?)(\])/s';

        if (! preg_match($pattern, $contents, $matches)) {
            return false;
        }

        $inputArrayContents = $matches[2];

        if (! preg_match('/[\'"]resources\/(css|js)\//', $inputArrayContents)) {
            return false;
        }

        $quoteStyle = str_contains($inputArrayContents, "'") ? "'" : '"';

        if (! preg_match('/^(.*[\'"][^\'"]+[\'"]),?(\s*)$/s', $inputArrayContents, $lastEntryMatch)) {
            return false;
        }

        $beforeTrailing = $lastEntryMatch[1];
        $trailingWhitespace = $lastEntryMatch[2];
        $newEntry = "{$quoteStyle}{$entry}{$quoteStyle}";

        if (str_contains($trailingWhitespace, "\n")) {
            preg_match('/\n(\s+)[\'"]/', $inputArrayContents, $indentMatch);
            $indentation = $indentMatch[1] ?? '            ';
            $newInputArrayContents = $beforeTrailing . ",\n{$indentation}{$newEntry}," . $trailingWhitespace;
        } else {
            $newInputArrayContents = $beforeTrailing . ", {$newEntry}" . $trailingWhitespace;
        }

        $newContents = preg_replace(
            $pattern,
            '$1' . str_replace(['\\', '$'], ['\\\\', '\\$'], $newInputArrayContents) . '$3',
            $contents,
            1,
        );

        if (($newContents === null) || ($newContents === $contents)) {
            return false;
        }

        $this->filesystem->put($viteConfigPath, $newContents);
        $this->components->info("Added [{$entry}] to the vite.config.js input array.");

        return true;
    }

    protected function hasUnsupportedViteSyntax(string $contents): bool
    {
        // Skip quoted paths so glob patterns and URLs are not mistaken for comments.
        // Comments and template literals require manual configuration instead of regex edits.
        return (bool) preg_match(<<<'REGEX'
            ~'(?:\\.|[^'\\])*'(*SKIP)(*F)|"(?:\\.|[^"\\])*"(*SKIP)(*F)|//|/\*|`~s
            REGEX, $contents);
    }
}
