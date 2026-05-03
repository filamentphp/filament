<?php

namespace Filament\TranslationTool\DataObjects;

final class TranslationFileLineParser
{
    /**
     * Parse PHP file and extract line numbers for each array key path.
     *
     * @return array<string, int>
     */
    public function parse(string $filePath): array
    {
        $tokens = token_get_all(file_get_contents($filePath));
        $keyLines = [];
        $path = [];  // stack of parent keys
        $pendingKey = null;
        $pendingKeyLine = null;

        $tokenCount = count($tokens);

        for ($index = 0; $index < $tokenCount; $index++) {
            $token = $tokens[$index];

            if (is_array($token)) {
                [$type, $content, $line] = $token;

                // Skip whitespace and comments
                if ($type === T_WHITESPACE || $type === T_COMMENT || $type === T_DOC_COMMENT) {
                    continue;
                }

                // String that might be an array key
                if ($type === T_CONSTANT_ENCAPSED_STRING) {
                    $key = trim($content, "'\"");

                    // Look ahead for =>
                    for ($lookAhead = $index + 1; $lookAhead < $tokenCount; $lookAhead++) {
                        $next = $tokens[$lookAhead];
                        if (is_array($next) && ($next[0] === T_WHITESPACE || $next[0] === T_COMMENT)) {
                            continue;
                        }
                        if (is_array($next) && $next[0] === T_DOUBLE_ARROW) {
                            $pendingKey = $key;
                            $pendingKeyLine = $line;
                        }

                        break;
                    }
                }
            } else {
                // Single character token
                if ($token === '[') {
                    if ($pendingKey !== null) {
                        // This is a nested array: 'key' => [...]
                        $path[] = $pendingKey;
                        $pendingKey = null;
                        $pendingKeyLine = null;
                    }
                    // else: opening [ of return [...] or indexed array
                } elseif ($token === ']') {
                    // First, record any pending leaf before closing bracket
                    if ($pendingKey !== null) {
                        $fullPath = count($path) > 0
                            ? implode('.', $path) . '.' . $pendingKey
                            : $pendingKey;
                        $keyLines[$fullPath] = $pendingKeyLine;
                        $pendingKey = null;
                        $pendingKeyLine = null;
                    }
                    // Then pop from path
                    if (count($path) > 0) {
                        array_pop($path);
                    }
                } elseif ($token === ',') {
                    // Record pending leaf if exists
                    if ($pendingKey !== null) {
                        $fullPath = count($path) > 0
                            ? implode('.', $path) . '.' . $pendingKey
                            : $pendingKey;
                        $keyLines[$fullPath] = $pendingKeyLine;
                        $pendingKey = null;
                        $pendingKeyLine = null;
                    }
                }
            }
        }

        return $keyLines;
    }
}
