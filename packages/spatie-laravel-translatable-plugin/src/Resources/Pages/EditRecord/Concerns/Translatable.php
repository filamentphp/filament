<?php

namespace Filament\Resources\Pages\EditRecord\Concerns;

use Filament\Resources\Concerns\HasActiveLocaleSwitcher;
use Filament\Resources\Pages\Concerns\HasTranslatableFormWithExistingRecordData;
use Filament\Resources\Pages\Concerns\HasTranslatableRecord;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

trait Translatable
{
    use HasActiveLocaleSwitcher;
    use HasTranslatableFormWithExistingRecordData;
    use HasTranslatableRecord;

    protected ?string $oldActiveLocale = null;

    public function getTranslatableLocales(): array
    {
        return static::getResource()::getTranslatableLocales();
    }

    public function save(bool $shouldRedirect = true): void
    {
        $this->authorizeAccess();

        $originalActiveLocale = $this->activeLocale;

        $nonTranslatableData = Arr::except(
            $this->data[$originalActiveLocale] ?? [],
            $this->getRecord()->getTranslatableAttributes(),
        );

        try {
            foreach ($this->getTranslatableLocales() as $locale) {
                $this->setActiveLocale($locale);

                $this->data[$locale] = array_merge(
                    $this->data[$locale] ?? [],
                    $nonTranslatableData,
                );

                /** @internal Read the DocBlock above the following method. */
                $this->validateFormAndUpdateRecordAndCallHooks();
            }
        } catch (Halt $exception) {
            return;
        }

        $this->setActiveLocale($originalActiveLocale);

        /** @internal Read the DocBlock above the following method. */
        $this->sendSavedNotificationAndRedirect(shouldRedirect: $shouldRedirect);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->fill(Arr::except($data, $record->getTranslatableAttributes()));

        foreach (Arr::only($data, $record->getTranslatableAttributes()) as $key => $value) {
            $record->setTranslation($key, $this->activeLocale, $value);
        }

        $record->save();

        return $record;
    }

    public function updatingActiveLocale(): void
    {
        $this->oldActiveLocale = $this->activeLocale;
    }

    public function updatedActiveLocale(string $newActiveLocale): void
    {
        if (blank($this->oldActiveLocale)) {
            return;
        }

        $this->setActiveLocale($this->oldActiveLocale);

        try {
            $this->form->validate();
        } catch (ValidationException $exception) {
            $this->activeLocale = $this->oldActiveLocale;

            throw $exception;
        }

        $this->setActiveLocale($newActiveLocale);

        if (blank($this->oldActiveLocale)) {
            return;
        }

        $translatableAttributes = app(static::getModel())->getTranslatableAttributes();

        $this->data[$newActiveLocale] = array_merge(
            $this->data[$newActiveLocale] ?? [],
            Arr::except(
                $this->data[$this->oldActiveLocale] ?? [],
                $translatableAttributes,
            ),
        );

        $this->data[$this->oldActiveLocale] = Arr::only(
            $this->data[$this->oldActiveLocale] ?? [],
            $translatableAttributes,
        );
    }
}
