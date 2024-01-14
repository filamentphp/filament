<?php

namespace Filament\Tables\Actions;

use Filament\Actions\Concerns\HasTranslatableLocaleOptions;

class LocaleSwitcher extends SelectAction
{
    use HasTranslatableLocaleOptions;

    public static function getDefaultName(): ?string
    {
        return 'activeLocale';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-spatie-laravel-translatable-plugin::actions.active_locale.label'));

        $this->setTranslatableLocaleOptions();
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match($parameterName) {
            'localeSwitcher', 'switcher' => [$this],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }
}
