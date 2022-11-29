<?php

namespace Filament\Actions\Concerns;

use Closure;
use Filament\Forms\ComponentContainer;

trait CanBeMounted
{
    protected ?Closure $mountUsing = null;

    /**
     * @param  array<string, mixed>  $parameters
     * @return mixed
     */
    public function mount(array $parameters)
    {
        return $this->evaluate($this->getMountUsing(), $parameters);
    }

    public function mountUsing(?Closure $callback): static
    {
        $this->mountUsing = $callback;

        return $this;
    }

    public function getMountUsing(): Closure
    {
        return $this->mountUsing ?? static function (?ComponentContainer $form = null): void {
            if (! $form) {
                return;
            }

            $form->fill();
        };
    }
}
