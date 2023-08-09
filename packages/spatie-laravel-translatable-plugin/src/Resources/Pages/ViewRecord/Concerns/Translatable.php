<?php

namespace Filament\Resources\Pages\ViewRecord\Concerns;

use Filament\Resources\Concerns\HasActiveLocaleSwitcher;
use Filament\Resources\Pages\Concerns\HasTranslatableFormWithExistingRecordData;
use Filament\Resources\Pages\Concerns\HasTranslatableRecord;

trait Translatable
{
    use HasActiveLocaleSwitcher;
    use HasTranslatableFormWithExistingRecordData;
    use HasTranslatableRecord;

    public function getTranslatableLocales(): array
    {
        return static::getResource()::getTranslatableLocales();
    }
}
