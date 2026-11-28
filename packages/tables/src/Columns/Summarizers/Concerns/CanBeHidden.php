<?php

namespace Filament\Tables\Columns\Summarizers\Concerns;

use Closure;
use DateTimeInterface;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Support\Arr;
use ReflectionFunction;
use Stringable;

trait CanBeHidden
{
    protected bool | Closure $isHidden = false;

    protected bool | Closure $isVisible = true;

    /**
     * @var array<string, bool>
     */
    protected array $visibilityCache = [];

    public function hidden(bool | Closure $condition = true): static
    {
        $this->isHidden = $condition;

        return $this;
    }

    /**
     * @param  string | array<string>  $livewireComponents
     */
    public function hiddenOn(string | array $livewireComponents): static
    {
        $this->hidden(static function (HasTable $livewire) use ($livewireComponents): bool {
            foreach (Arr::wrap($livewireComponents) as $livewireComponent) {
                if ($livewire instanceof $livewireComponent) {
                    return true;
                }
            }

            return false;
        });

        return $this;
    }

    public function visible(bool | Closure $condition = true): static
    {
        $this->isVisible = $condition;

        return $this;
    }

    /**
     * @param  string | array<string>  $livewireComponents
     */
    public function visibleOn(string | array $livewireComponents): static
    {
        $this->visible(static function (HasTable $livewire) use ($livewireComponents): bool {
            foreach (Arr::wrap($livewireComponents) as $livewireComponent) {
                if ($livewire instanceof $livewireComponent) {
                    return true;
                }
            }

            return false;
        });

        return $this;
    }

    public function isHidden(): bool
    {
        $query = $this->getQuery()?->toBase()->clone();
        $queryKey = $query ? hash('sha256', serialize([
            $query->toSql(),
            array_map(
                static fn (mixed $binding): mixed => match (true) {
                    $binding instanceof DateTimeInterface => $binding->format($query->getGrammar()->getDateFormat()),
                    $binding instanceof Stringable => (string) $binding,
                    default => $binding,
                },
                $query->getBindings(),
            ),
        ])) : '';

        if (array_key_exists($queryKey, $this->visibilityCache)) {
            return $this->visibilityCache[$queryKey];
        }

        if (! $query) {
            foreach ([$this->isHidden, $this->isVisible] as $condition) {
                if (! $condition instanceof Closure) {
                    continue;
                }

                foreach ((new ReflectionFunction($condition))->getParameters() as $parameter) {
                    if (($parameter->getName() === 'query') && (! $parameter->allowsNull())) {
                        return $this->visibilityCache[$queryKey] = true;
                    }
                }
            }
        }

        if ($this->evaluate($this->isHidden)) {
            return $this->visibilityCache[$queryKey] = true;
        }

        return $this->visibilityCache[$queryKey] = ! $this->evaluate($this->isVisible);
    }

    public function isVisible(): bool
    {
        return ! $this->isHidden();
    }
}
