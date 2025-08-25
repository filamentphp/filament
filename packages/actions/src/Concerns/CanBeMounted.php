<?php

namespace Filament\Actions\Concerns;

use Closure;
use Filament\Schemas\Schema;

trait CanBeMounted
{
    protected ?Closure $mountUsing = null;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function mount(array $parameters): mixed
    {
        return $this->evaluate($this->getMountUsing(), $parameters);
    }

    public function mountUsing(?Closure $callback): static
    {
        $this->mountUsing = $callback;

        return $this;
    }

    /**
     * @param  array<string, mixed> | Closure | null  $data
     */
    public function fillForm(array | Closure | null $data): static
    {
        $this->mountUsing(function (?Schema $schema) use ($data): void {
            $schema?->fill($this->evaluate($data));
        });

        return $this;
    }

    public function getMountUsing(): Closure
    {
        return $this->mountUsing ?? static function (?Schema $schema = null): void {
            if (! $schema) {
                return;
            }

            $schema->fill();
        };
    }
}
