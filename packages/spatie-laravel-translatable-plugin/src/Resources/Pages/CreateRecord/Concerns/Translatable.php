<?php

namespace Filament\Resources\Pages\CreateRecord\Concerns;

use Filament\Resources\Pages\Concerns\HasActiveFormLocaleSwitcher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

trait Translatable
{
    use HasActiveFormLocaleSwitcher;

    public function mount(): void
    {
        static::authorizeResourceAccess();

        abort_unless(static::getResource()::canCreate(), 403);

        $this->setActiveFormLocale();

        $this->fillForm();
    }

    protected function setActiveFormLocale(): void
    {
        $this->activeLocale = $this->activeFormLocale = static::getResource()::getDefaultTranslatableLocale();
    }

    protected function handleRecordCreation(array $data): Model
    {
        $record = app(static::getModel());
        $record->fill(Arr::except($data, $record->getTranslatableAttributes()));

        foreach (Arr::only($data, $record->getTranslatableAttributes()) as $key => $value) {
            $record->setTranslation($key, $this->activeFormLocale, $value);
        }

        $record->save();

        return $record;
    }

    protected function getActions(): array
    {
        return array_merge(
            [$this->getActiveFormLocaleSelectAction()],
            parent::getActions() ?? [],
        );
    }
}
