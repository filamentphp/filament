<?php

namespace Filament\Resources\Pages\Concerns;

use Filament\Pages\Actions\Action;
use Filament\Pages\Actions\LocaleSwitcher;

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
