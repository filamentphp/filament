<?php

if ((PHP_SAPI !== 'cli') || (! isset($argv))) {
    throw new RuntimeException('This script must be run directly from the command line.');
}

$arguments = array_slice($argv, 1);
$isDryRun = in_array('--dry-run', $arguments, true);
$directories = array_values(array_diff($arguments, ['--dry-run'])) ?: ['resources/views'];
$hasChanges = false;

foreach ($directories as $directory) {
    if (! is_dir($directory)) {
        fwrite(STDERR, "Blade template directory does not exist: {$directory}\n");

        exit(1);
    }

    if (is_link($directory)) {
        continue;
    }

    $files = new RecursiveIteratorIterator(new RecursiveCallbackFilterIterator(
        new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS),
        static fn (SplFileInfo $file): bool => (! $file->isLink()) && (! in_array($file->getFilename(), ['vendor', 'node_modules', '.git'], true)),
    ));

    foreach ($files as $file) {
        if ((! $file->isFile()) || (! str_ends_with($file->getFilename(), '.blade.php'))) {
            continue;
        }

        $path = $file->getPathname();
        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new RuntimeException("Unable to read Blade template [{$path}].");
        }

        // Consume complete tags so component-like strings inside attributes are unchanged.
        // Blade also excludes comments, `@verbatim` blocks, and `@php` blocks from tag compilation.
        $updatedContents = preg_replace_callback(<<<'REGEX'
            ~
                (?<!@)@verbatim\b.*?@endverbatim
                | (?<!@)@php\b.*?@endphp
                | \{\{--.*?--\}\}
                | <\s*/?\s*[a-zA-Z][\w:.-]*(?:
                    @(?:class|style)(?<expression>\((?:[^()]++|(?&expression))*\))
                    | \{\{\s*\$attributes(?:[^}]+?)?\s*\}\}
                    | [^"'><]
                    | "[^"]*"
                    | '[^']*'
                )*>
            ~sx
            REGEX, static fn (array $matches): string => preg_replace_callback(
            '~^(<\s*/?\s*x[-:])filament::(page|widget|card)(?=[\s/>])~',
            static fn (array $component): string => $component[1] . match ($component[2]) {
                'page' => 'filament-panels::page',
                'widget' => 'filament-widgets::widget',
                'card' => 'filament::section',
            },
            $matches[0],
        ), $contents);

        if ($updatedContents === null) {
            throw new RuntimeException("Unable to migrate Blade template [{$path}]: " . preg_last_error_msg());
        }

        if ($updatedContents === $contents) {
            continue;
        }

        $hasChanges = true;

        if ((! $isDryRun) && (file_put_contents($path, $updatedContents) === false)) {
            throw new RuntimeException("Unable to write Blade template [{$path}].");
        }

        fwrite(STDOUT, ($isDryRun ? 'Would update: ' : 'Updated: ') . $path . "\n");
    }
}

exit(($isDryRun && $hasChanges) ? 2 : 0);
