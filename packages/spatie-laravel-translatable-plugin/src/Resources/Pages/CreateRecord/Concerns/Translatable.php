<?php

namespace Filament\Resources\Pages\CreateRecord\Concerns;

use Filament\Resources\Pages\Concerns\HasActiveFormLocaleSelect;
use Illuminate\Database\Eloquent\Model;

trait Translatable
{
    use HasActiveFormLocaleSelect;

    public function mount(): void
    {
        static::authorizeResourceAccess();

        abort_unless(static::getResource()::canCreate(), 403);

        $this->setActiveFormLocale();

        $this->fillForm();
    }

    protected function setActiveFormLocale(): void
    {
        $this->activeFormLocale = static::getResource()::getDefaultTranslatableLocale();
    }

    protected function handleRecordCreation(array $data): Model
    {
        $model = static::getModel()::newModelInstance();
        $jsonValues = collect($data)->filter(fn($value, $key) => $model->isTranslatableAttribute($key) && is_array($value));

        $values = collect($data)->except($jsonValues->keys()->toArray())->toArray();
        $record = $model->setLocale($this->activeFormLocale)->fill($values);

        $jsonValues->each(fn($value, $key) => $record->setTranslation($key, $this->activeFormLocale, $value));

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
