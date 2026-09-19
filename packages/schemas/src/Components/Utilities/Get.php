<?php

namespace Filament\Schemas\Components\Utilities;

use BackedEnum;
use Carbon\CarbonInterface;
use Filament\Schemas\Components\Component;
use Illuminate\Support\Carbon;

class Get
{
    /**
     * @var array<Component>
     */
    protected static array $skipComponentsChildContainersWhileSearching = [];

    protected bool $shouldSkipComponentsChildContainersWhileSearching = true;

    public function __construct(
        protected Component $component,
    ) {}

    public function __invoke(string | Component $path = '', bool $isAbsolute = false): mixed
    {
        $livewire = $this->component->getLivewire();

        $path = $this->component->resolveRelativeStatePath($path, $isAbsolute);

        static::$skipComponentsChildContainersWhileSearching[] = $this->component;

        $component = ($this->component->getStatePath() === $path)
            ? $this->component
            : $this->component->getRootContainer()->getComponentByStatePath(
                $path,
                withHidden: true,
                withAbsoluteStatePath: true,
                skipComponentsChildContainersWhileSearching: $this->shouldSkipComponentsChildContainersWhileSearching ? static::$skipComponentsChildContainersWhileSearching : [],
            );

        try {
            // The primary search above may have missed a field that is a descendant of
            // `$this->component` itself (e.g. read from that same component's own
            // `badge()`/`label()` closure). Retry dropping only the entry this call
            // pushed, so a re-entrant `$get()` from this component's own dynamic child
            // schema still sees the component excluded.
            if ((! $component) && $this->shouldSkipComponentsChildContainersWhileSearching) {
                $component = $this->component->getRootContainer()->getComponentByStatePath(
                    $path,
                    withHidden: true,
                    withAbsoluteStatePath: true,
                    skipComponentsChildContainersWhileSearching: array_slice(
                        static::$skipComponentsChildContainersWhileSearching,
                        0,
                        -1,
                    ),
                );
            }

            if (! $component) {
                return data_get($livewire, $path);
            }

            return $component->getState();
        } finally {
            array_pop(static::$skipComponentsChildContainersWhileSearching);
        }
    }

    /**
     * @template TNullable of bool
     *
     * @param  TNullable  $isNullable
     * @return (TNullable is true ? ?string : string)
     */
    public function string(string $key, bool $isNullable = false, bool $isAbsolute = false): ?string
    {
        $state = $this($key, $isAbsolute);

        if ($isNullable && blank($state)) {
            return null;
        }

        return (string) $state;
    }

    /**
     * @template TNullable of bool
     *
     * @param  TNullable  $isNullable
     * @return (TNullable is true ? ?int : int)
     */
    public function integer(string $key, bool $isNullable = false, bool $isAbsolute = false): ?int
    {
        $state = $this($key, $isAbsolute);

        if ($isNullable && blank($state)) {
            return null;
        }

        return (int) $state;
    }

    /**
     * @template TNullable of bool
     *
     * @param  TNullable  $isNullable
     * @return (TNullable is true ? ?float : float)
     */
    public function float(string $key, bool $isNullable = false, bool $isAbsolute = false): ?float
    {
        $state = $this($key, $isAbsolute);

        if ($isNullable && blank($state)) {
            return null;
        }

        return (float) $state;
    }

    /**
     * @template TNullable of bool
     *
     * @param  TNullable  $isNullable
     * @return (TNullable is true ? ?bool : bool)
     */
    public function boolean(string $key, bool $isNullable = false, bool $isAbsolute = false): ?bool
    {
        $state = $this($key, $isAbsolute);

        if ($isNullable && blank($state)) {
            return null;
        }

        return (bool) $state;
    }

    /**
     * @template TNullable of bool
     *
     * @param  TNullable  $isNullable
     * @return (TNullable is true ? ?array<mixed, mixed> : array<mixed, mixed>)
     */
    public function array(string $key, bool $isNullable = false, bool $isAbsolute = false): ?array
    {
        $state = $this($key, $isAbsolute);

        if ($isNullable && (! is_array($state))) {
            return null;
        }

        return (array) ($state ?? []);
    }

    /**
     * @template TNullable of bool
     *
     * @param  TNullable  $isNullable
     * @return (TNullable is true ? ?CarbonInterface : CarbonInterface)
     */
    public function date(string $key, bool $isNullable = false, bool $isAbsolute = false): ?CarbonInterface
    {
        $state = $this($key, $isAbsolute);

        if ($isNullable && blank($state)) {
            return null;
        }

        return Carbon::parse($state);
    }

    /**
     * @template T of BackedEnum
     * @template TNullable of bool
     *
     * @param  class-string<T>  $enumClass
     * @param  TNullable  $isNullable
     * @return (TNullable is true ? T|null : T)
     */
    public function enum(string $key, string $enumClass, bool $isNullable = false, bool $isAbsolute = false): ?BackedEnum
    {
        $state = $this($key, $isAbsolute);

        if ($isNullable && blank($state)) {
            return null;
        }

        if ($state instanceof BackedEnum) {
            return $state;
        }

        return $enumClass::tryFrom($state);
    }

    public function filled(string $key, bool $isAbsolute = false): bool
    {
        return filled($this($key, $isAbsolute));
    }

    public function blank(string $key, bool $isAbsolute = false): bool
    {
        return blank($this($key, $isAbsolute));
    }

    public function skipComponentsChildContainersWhileSearching(bool $condition = true): static
    {
        $this->shouldSkipComponentsChildContainersWhileSearching = $condition;

        return $this;
    }
}
