<?php

namespace Filament\TranslationTool\DataObjects;

use Illuminate\Support\Arr;

final class TranslationFile
{
    public function __construct(
        public Package $package,
        public string $name,
    ) {}

    public function exists(Locale | string $locale): bool
    {
        return file_exists($this->getFilePath($locale));
    }

    public function getFilePath(Locale | string $locale): string
    {
        return $this->package->getLangFolder($locale) . DIRECTORY_SEPARATOR . $this->name;
    }

    public function getFileUrl(Locale | string $locale, ?int $line = null): string
    {
        $url = 'vscode://file/' . $this->getFilePath($locale);
        if ($line !== null) {
            $url .= ":{$line}";
        }
        return $url;
    }

    public function getTranslations(Locale | string $locale): array
    {
        $filePath = $this->getFilePath($locale);
        if (! file_exists($filePath)) {
            return [];
        }

        $translations = require $filePath;
        $dotted = Arr::dot($translations);

        // Build a map of key => line using PHP tokenizer
        $tokens = token_get_all(file_get_contents($filePath));
        $keyLines = [];

        foreach ($tokens as $token) {
            if (is_array($token)) {
                [$type, $content, $line] = $token;

                if ($type === T_CONSTANT_ENCAPSED_STRING) {
                    $key = trim($content, "'\"");
                    // Store first occurrence only
                    if (! isset($keyLines[$key])) {
                        $keyLines[$key] = $line;
                    }
                }
            }
        }

        $result = [];

        foreach ($dotted as $key => $value) {
            // Find line for the last segment of the key (the actual array key in the file)
            $segments = explode('.', $key);
            $lastSegment = end($segments);
            $line = $keyLines[$lastSegment] ?? null;

            $result[$key] = [
                'line' => $line,
                'value' => $value,
            ];
        }

        return $result;
    }
}
