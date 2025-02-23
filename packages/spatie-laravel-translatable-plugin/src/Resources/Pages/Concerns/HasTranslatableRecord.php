<?php

namespace Filament\Resources\Pages\Concerns;

use Illuminate\Database\Eloquent\Model;

trait HasTranslatableRecord /** @phpstan-ignore trait.unused */
{
    public function getRecord(): Model
    {
        if (blank($this->activeLocale)) {
            return $this->record;
        }

        return $this->record->setLocale($this->activeLocale);
    }
}
