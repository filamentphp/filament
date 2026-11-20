<?php

it('does not mix alphabets in a word of a translation', function (): void {
    $invalid = [];

    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/../../../packages', FilesystemIterator::SKIP_DOTS));

    foreach ($files as $file) {
        if (! preg_match('#/resources/lang/([^/]+)/.+\.php$#', $file->getPathname(), $matches)) {
            continue;
        }

        $translations = require $file->getPathname();

        array_walk_recursive($translations, function ($translation) use ($matches, &$invalid): void {
            // :attribute style placeholders and escaped line breaks are not words
            $text = preg_replace('/:[A-Za-z_]+|\\\\[nrt]/', ' ', $translation);

            foreach (preg_split('/[^\p{L}]+/u', $text, -1, PREG_SPLIT_NO_EMPTY) as $word) {
                $alphabets = preg_match('/\p{sc=Latin}/u', $word)
                    + preg_match('/\p{sc=Cyrillic}/u', $word)
                    + preg_match('/\p{sc=Greek}/u', $word);

                if ($alphabets > 1) {
                    $invalid[] = "{$matches[1]}: {$word}";
                }
            }
        });
    }

    expect($invalid)->toBe([]);
});
