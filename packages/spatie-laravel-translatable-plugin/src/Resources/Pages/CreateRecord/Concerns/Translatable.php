<?php

namespace Filament\Resources\Pages\CreateRecord\Concerns;

use Filament\Resources\Concerns\HasActiveLocaleSwitcher;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

trait Translatable
{
    use HasActiveLocaleSwitcher;

    protected ?string $oldActiveLocale = null;

    protected function fillForm(): void
    {
        foreach ($this->getTranslatableLocales() as $locale) {
            $this->setActiveLocale($locale);

            /** @internal Read the DocBlock above the following method. */
            $this->fillFormWithDefaultsAndCallHooks();
        }

        $this->setActiveLocale();
    }

    public function getTranslatableLocales(): array
    {
        return static::getResource()::getTranslatableLocales();
    }

    protected function setActiveLocale(?string $locale = null): void
    {
        $this->activeLocale = filled($locale) ? $locale : static::getResource()::getDefaultTranslatableLocale();
        $this->cacheForm('form', $this->form($this->makeForm()));
    }

    public function create(bool $another = false): void
    {
        $this->authorizeAccess();

        try {
            $this->callHook('beforeValidate');

            $data = [
                $this->activeLocale => $this->form->getState(),
            ];

            $this->callHook('afterValidate');

            $originalActiveLocale = $this->activeLocale;

            $translatableAttributes = app(static::getModel())->getTranslatableAttributes();

            $nonTranslatableData = Arr::except(
                $this->data[$originalActiveLocale] ?? [],
                $translatableAttributes,
            );

            foreach ($this->getTranslatableLocales() as $locale) {
                if ($locale === $originalActiveLocale) {
                    continue;
                }

                try {
                    $this->setActiveLocale($locale);

                    $this->data[$locale] = array_merge(
                        $this->data[$locale] ?? [],
                        $nonTranslatableData,
                    );

                    $this->callHook('beforeValidate');

                    $data[$locale] = $this->form->getState();

                    $this->callHook('afterValidate');
                } catch (ValidationException $exception) {
                    // If the validation fails for a non-active locale,
                    // we'll just ignore it and continue, since it's
                    // likely that the user hasn't filled out the
                    // required fields for that locale.
                }
            }

            $this->setActiveLocale($originalActiveLocale);

            foreach ($data as $locale => $localeData) {
                if ($locale !== $this->activeLocale) {
                    $localeData = Arr::only(
                        $localeData,
                        $translatableAttributes,
                    );
                }

                $data[$locale] = $this->mutateFormDataBeforeCreate($localeData);
            }

            /** @internal Read the DocBlock above the following method. */
            $this->createRecordAndCallHooks($data);
        } catch (Halt $exception) {
            return;
        }

        /** @internal Read the DocBlock above the following method. */
        $this->sendCreatedNotificationAndRedirect(shouldCreateAnotherInsteadOfRedirecting: $another);
    }

    protected function handleRecordCreation(array $data): Model
    {
        $record = app(static::getModel());

        $translatableAttributes = $record->getTranslatableAttributes();

        $record->fill(Arr::except(Arr::first($data), $translatableAttributes));

        foreach ($data as $locale => $localeData) {
            if ($locale === $this->activeLocale) {
                $localeData = Arr::only(
                    $localeData,
                    app(static::getModel())->getTranslatableAttributes(),
                );
            }

            foreach ($localeData as $key => $value) {
                $record->setTranslation($key, $locale, $value);
            }
        }

        $record->save();

        return $record;
    }

    public function getFormStatePath(): ?string
    {
        return "data.{$this->activeLocale}";
    }

    public function updatingActiveLocale(): void
    {
        $this->oldActiveLocale = $this->activeLocale;
    }

    public function updatedActiveLocale(string $newActiveLocale): void
    {
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
