<?php

if ((PHP_SAPI !== 'cli') || (! isset($argv))) {
    throw new RuntimeException('This script must be run directly from the command line.');
}

$arguments = array_slice($argv, 1);
$isDryRun = in_array('--dry-run', $arguments, true);
$directories = array_values(array_diff($arguments, ['--dry-run'])) ?: ['resources/views'];
$hasChanges = false;

$componentNames = [
    'filament::page' => 'filament-panels::page',
    'filament::widget' => 'filament-widgets::widget',
    'filament::card' => 'filament::section',
];

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
        // Blade also excludes Blade comments, `@verbatim` blocks, and `@php` blocks from tag compilation.
        $updatedContents = preg_replace_callback(<<<'REGEX'
            ~
                (?<!@)@verbatim\b.*?@endverbatim
                | (?<!@)@php\b.*?@endphp
                | \{\{--.*?--\}\}
                | <\?(?:php\b|=)(?>'(?:\\.|[^'\\])*'|"(?:\\.|[^"\\])*"|(?! \?>).)*\?>
                | (?:
                    @?\{!!(?>'(?:\\.|[^'\\])*'|"(?:\\.|[^"\\])*"|(?! !!\}).)*!!\}
                    | @?\{\{(?>'(?:\\.|[^'\\])*'|"(?:\\.|[^"\\])*"|(?! \}\}).)*\}\}
                )
                | @?@[a-zA-Z]+\s*(?<arguments>\((?:[^()'"/]+|'(?:\\.|[^'\\])*'|"(?:\\.|[^"\\])*"|/\*.*?\*/|/(?!\*)|(?&arguments))*\))
                | <\s*/?\s*[a-zA-Z][\w:.-]*(?:
                    @(?:class|style)(?<expression>\((?:[^()]++|(?&expression))*\))
                    | \{\{\s*\$attributes(?:[^}]+?)?\s*\}\}
                    | [^"'><]
                    | "[^"]*"
                    | '[^']*'
                )*>
            ~sx
            REGEX, static function (array $matches) use ($componentNames): string {
            $source = $matches[0];

            $source = preg_replace_callback(
                '~^(<\s*/?\s*x[-:])(filament::(?:page|widget|card))(?=[\s/>])~',
                static fn (array $component): string => $component[1] . $componentNames[$component[2]],
                $source,
            );

            if (! preg_match('~^<\s*x[-:]dynamic-component(?=[\s/>])~', $source)) {
                return $source;
            }

            // Consume every quoted attribute, including unrelated ones containing example markup.
            return preg_replace_callback(
                <<<'REGEX'
                    ~
                        @(?:class|style)(?<expression>\((?:[^()'"/]+|'(?:\\.|[^'\\])*'|"(?:\\.|[^"\\])*"|/(?!\*)|/\*.*?\*/|(?&expression))*\))
                        | \{\{.*?\}\}
                        | (?<attribute>[^\s=<>/]+)\s*=\s*(?:"(?<double>[^"]*)"|'(?<single>[^']*)')
                    ~sx
                    REGEX,
                static function (array $attribute) use ($componentNames): string {
                    if (empty($attribute['attribute'])) {
                        return $attribute[0];
                    }

                    $value = $attribute['single'] ?? $attribute['double'];
                    $name = $value;
                    $isBound = in_array($attribute['attribute'], [':component', 'bind:component'], true);

                    if ($attribute['attribute'] === 'component') {
                        if (preg_match('~^(?:\{\{\{(.*?)\}\}\}|\{\{(.*?)\}\}|\{!!(.*?)!!\})$~s', $value, $echo, PREG_UNMATCHED_AS_NULL)) {
                            $value = $echo[1] ?? $echo[2] ?? $echo[3];
                            $isBound = true;
                        }
                    } elseif (! $isBound) {
                        return $attribute[0];
                    }

                    if ($isBound) {
                        if (! preg_match('~^\s*([\'\"])(filament::(?:page|widget|card))\1\s*$~', $value, $literal)) {
                            return $attribute[0];
                        }

                        $name = $literal[2];
                    }

                    return isset($componentNames[$name]) ? str_replace($name, $componentNames[$name], $attribute[0]) : $attribute[0];
                },
                $source,
            );
        }, $contents);

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
