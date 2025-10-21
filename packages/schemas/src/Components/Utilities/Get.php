<?php

namespace Filament\Schemas\Components\Utilities;

use Carbon\CarbonInterface;
use Filament\Schemas\Components\Component;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

use function Illuminate\Support\enum_value;

class Get
{
    public function __construct(
        protected Component $component,
    ) {}

    public function __invoke(string | Component $path = '', bool $isAbsolute = false): mixed
    {
        $livewire = $this->component->getLivewire();

        $path = $this->component->resolveRelativeStatePath($path, $isAbsolute);

        $component = ($this->component->getStatePath() === $path)
            ? $this->component
            : $this->component->getRootContainer()->getComponentByStatePath(
                $path,
                withHidden: true,
                withAbsoluteStatePath: true,
                skipComponentChildContainersWhileSearching: $this->component,
            );

        if (! $component) {
            return data_get($livewire, $path);
        }

        return $component->getState();
    }

    /** @param mixed $default */
    public function string(string $key, $default = null, bool $isAbsolute = false): Stringable
    {
        return Str::of($this($key, $isAbsolute) ?? $default);
    }

    /** @param mixed $default */
    public function integer(string $key, $default = 0, bool $isAbsolute = false): int
    {
        return intval($this($key, $isAbsolute) ?? $default);
    }

    /** @param mixed $default */
    public function float(string $key, $default = 0.0, bool $isAbsolute = false): float
    {
        return floatval($this($key, $isAbsolute) ?? $default);
    }

    /** @param mixed $default */
    public function boolean(string $key, $default = false, bool $isAbsolute = false): bool
    {
        return filter_var($this($key, $isAbsolute) ?? $default, FILTER_VALIDATE_BOOLEAN);
    }

    public function array(string $key, bool $isAbsolute = false): array
    {
        return (array) ($this($key, $isAbsolute) ?? []);
    }

    public function collect(string $key, bool $isAbsolute = false): Collection
    {
        return collect($this($key, $isAbsolute) ?? []);
    }

    public function date(string $key, ?string $format = null, ?string $tz = null, bool $isAbsolute = false): ?CarbonInterface
    {
        $state = $this($key, $isAbsolute);
        $tz = enum_value($tz);

        if (! is_string($state)) {
            return null;
        }

        if (is_null($format)) {
            return Date::parse($state, $tz);
        }

        return Date::createFromFormat($format, $state, $tz);
    }

    /**
     * @template TEnum of \BackedEnum
     *
     * @param  class-string<TEnum>  $enumClass
     * @param  TEnum|null  $default
     * @return TEnum|null
     */
    public function enum(string $key, string $enumClass, $default = null, bool $isAbsolute = false)
    {
        $state = $this($key, $isAbsolute);

        if (
            ! enum_exists($enumClass) ||
            ! method_exists($enumClass, 'tryFrom') ||
            ! is_string($state)
        ) {
            return $default;
        }

        return $enumClass::tryFrom($state) ?: $default;
    }
}
