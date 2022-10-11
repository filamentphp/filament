<?php

namespace Filament\Resources\Pages\Concerns;

trait HasTranslatableRecordTitle
{
    public function getRecordTitle(): string
    {
        if ($this->activeLocale) {
            $this->record->setLocale($this->activeLocale);
        }

        return parent::getRecordTitle();
    }
}
