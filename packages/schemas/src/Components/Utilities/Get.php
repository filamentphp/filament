<?php

namespace Filament\Schemas\Components\Utilities;

use BackedEnum;
use Carbon\CarbonInterface;
use Filament\Schemas\Components\Component;
use Illuminate\Support\Carbon;
use Throwable;

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

    public function string(string $key, bool $isAbsolute = false): string
    {
        return (string) $this($key, $isAbsolute);
    }

    public function integer(string $key, bool $isAbsolute = false): int
    {
        return (int) $this($key, $isAbsolute);
    }

    public function float(string $key, bool $isAbsolute = false): float
    {
        return (float) $this($key, $isAbsolute);
    }

    public function boolean(string $key, bool $isAbsolute = false): bool
    {
        return (bool) $this($key, $isAbsolute);
    }

    /** 
     * @return array<mixed, mixed> 
     */
    public function array(string $key, bool $isAbsolute = false): array
    {
        return (array) ($this($key, $isAbsolute) ?? []);
    }

    public function date(string $key, bool $isAbsolute = false): ?CarbonInterface
    {
        $state = $this($key, $isAbsolute);

        if (blank($state)) {
            return null;
        }

        return Carbon::parse($state);
    }

    /** 
     * @param class-string<BackedEnum> $enumClass 
     */
    public function enum(string $key, string $enumClass, bool $isAbsolute = false): ?BackedEnum
    {
        $state = $this($key, $isAbsolute);

        if (blank($state)) {
            return null;
        }

        return $enumClass::tryFrom($state);
    }
}
