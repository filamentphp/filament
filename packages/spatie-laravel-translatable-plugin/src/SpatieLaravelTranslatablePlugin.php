<?php

namespace Filament;

use Closure;
use Filament\Contracts\Plugin;

class SpatieLaravelTranslatablePlugin implements Plugin
{
    /**
     * @var array<string>
     */
    protected array $defaultLocales = [];

    protected ?Closure $getLocaleLabelUsing = null;

    final public function __construct() {}

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'spatie-laravel-translatable';
    }

    public function register(Panel $panel): void
    {
        //
    }

    public function boot(Panel $panel): void
    {
        //
    }

    /**
     * @return array<string>
     */
    public function getDefaultLocales(): array
    {
        return $this->defaultLocales;
    }

    /**
     * @param  array<string> | null  $defaultLocales
     */
    public function defaultLocales(?array $defaultLocales = null): static
    {
        $this->defaultLocales = $defaultLocales;

        return $this;
    }

    public function getLocaleLabelUsing(?Closure $callback): void
    {
        $this->getLocaleLabelUsing = $callback;
    }

    public function getLocaleLabel(string $locale, ?string $displayLocale = null): ?string
    {
        $displayLocale ??= app()->getLocale();

        $label = null;

        if ($callback = $this->getLocaleLabelUsing) {
            $label = $callback($locale, $displayLocale);
        }

        return $label ?? (locale_get_display_name($locale, $displayLocale) ?: null);
    }
}
