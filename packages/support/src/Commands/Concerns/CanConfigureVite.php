<?php

namespace Filament\Support\Commands\Concerns;

trait CanConfigureVite
{
    protected function registerViteInput(string $entry): bool
    {
        $viteConfigPath = base_path('vite.config.js');

        if (! $this->filesystem->exists($viteConfigPath)) {
            return false;
        }

        $contents = $this->filesystem->get($viteConfigPath);

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
}
