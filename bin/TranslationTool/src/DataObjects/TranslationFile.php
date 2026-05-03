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
        return match (env('IDE')) {
            'vscode' => $this->getVSCodeUrl($locale, $line),
            'phpstorm' => $this->getPhpStormUrl($locale, $line),
            default => $this->getDefaultFileUrl($locale, $line),
        };
    }

    public function getDefaultFileUrl(Locale | string $locale, ?int $line = null): string
    {
        $url = 'file://' . $this->getFilePath($locale);
        if ($line !== null) {
            $url .= "#L{$line}";
        }

        return $url;
    }

    public function getVSCodeUrl(Locale | string $locale, ?int $line = null): string
    {
        $url = 'vscode://file/' . $this->getFilePath($locale);
        if ($line !== null) {
            $url .= ":{$line}";
        }

        return $url;
    }

    public function getPhpStormUrl(Locale | string $locale, ?int $line = null): string
    {
        $url = 'phpstorm://open?file=' . $this->getFilePath($locale);
        if ($line !== null) {
            $url .= "&line={$line}";
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

        // Build a map of full dotted key => line using PHP tokenizer
        $keyLines = (new TranslationFileLineParser)->parse($filePath);

        $result = [];

        foreach ($dotted as $key => $value) {
            $result[$key] = [
                'line' => $keyLines[$key] ?? null,
                'value' => $value,
            ];
        }

        return $result;
    }
}
