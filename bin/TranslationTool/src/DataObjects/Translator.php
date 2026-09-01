<?php

namespace Filament\TranslationTool\DataObjects;

use Illuminate\Support\Collection;

final class Translator
{
    protected static array $translators = [];

    public function __construct(
        public string $githubHandle,
        public string $discordHandle,
        public ?string $discordId = null,
        public array $locales = []
    ) {}

    public function getDiscordLink(): string
    {
        return $this->discordId
            ? createLink('discord://-/users/' . $this->discordId, $this->discordHandle)
            : $this->discordHandle;
    }

    public function getGithubLink(): string
    {
        return createLink('https:://github.com/' . $this->githubHandle, $this->githubHandle);
    }

    public function getPreferredHandle(): string
    {
        return filled($this->githubHandle) ? $this->githubHandle : $this->discordHandle;
    }

    public function getLocales(): string
    {
        return implode(', ', $this->locales);
    }

    public static function count(): int
    {
        return count(self::$translators);
    }

    public static function getTranslatorsForLocale(Locale $locale): Collection
    {
        return collect(self::$translators[$locale->code] ?? []);
    }

    public static function setTranslatorForLocale(Locale $locale, Translator $translator): void
    {
        if (! isset(self::$translators[$locale->code])) {
            self::$translators[$locale->code] = [];
        }

        self::$translators[$locale->code][] = $translator;
    }

    public static function fromCsvLine(string $line): self
    {
        [$locales, $githubHandle, $discordHandle, $discordId] = str_getcsv($line, ';', '"', escape: false);

        $translator = new self(
            githubHandle: $githubHandle,
            discordHandle: $discordHandle,
            discordId: $discordId,
            locales: explode(',', $locales)
        );

        foreach ($translator->locales as $locale) {
            self::setTranslatorForLocale(new Locale($locale), $translator);
        }

        return $translator;
    }

    public static function loadFromFile(): Collection
    {
        $translatorsFile = __DIR__ . '/../../../../translators.csv';

        return collect(file($translatorsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES))
            ->map(fn (string $line) => self::fromCsvLine($line));
    }
}
