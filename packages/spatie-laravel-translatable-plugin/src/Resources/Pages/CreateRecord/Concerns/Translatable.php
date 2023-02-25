<?php

namespace Filament\Resources\Pages\CreateRecord\Concerns;

use Filament\Resources\Pages\Concerns\HasActiveLocaleSwitcher;
use Filament\Resources\Pages\Concerns\HasTranslatableRecordTitle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

trait Translatable
{
    use HasActiveLocaleSwitcher;
    use HasTranslatableRecordTitle;

    public function mount(): void
    {
        static::authorizeResourceAccess();

        abort_unless(static::getResource()::canCreate(), 403);

        $this->setActiveLocale();

        $this->fillForm();
    }

    protected function setActiveLocale(): void
    {
        $this->activeLocale = static::getResource()::getDefaultTranslatableLocale();
    }

    protected function handleRecordCreation(array $data): Model
    {
        $record = app(static::getModel());
        $record->fill(Arr::except($data, $record->getTranslatableAttributes()));

        foreach (Arr::only($data, $record->getTranslatableAttributes()) as $key => $value) {
            $record->setTranslation($key, $this->activeLocale, $value);
        }
        $record->save();

        return $record;
    }
}
