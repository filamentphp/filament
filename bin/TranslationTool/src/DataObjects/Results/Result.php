<?php

namespace Filament\TranslationTool\DataObjects\Results;

class Result
{
    public function __construct(
        public int $totalTranslations = 0,
        public array $missingTranslations = [],
        public array $removedTranslations = []
    ) {}

    public function correctTranslationCount(): int
    {
        return $this->totalTranslations - count($this->missingTranslations) - count($this->removedTranslations);
    }

    public function coverage(): float
    {
        return $this->totalTranslations > 0
            ? round(($this->correctTranslationCount()) / $this->totalTranslations * 100, 2)
            : 0;
    }
}
