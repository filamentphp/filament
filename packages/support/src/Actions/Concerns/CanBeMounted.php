<?php

namespace Filament\Support\Actions\Concerns;

use Closure;
use Filament\Forms\ComponentContainer;
use Filament\Support\Actions\Action;

trait CanBeMounted
{
    protected ?Closure $mountUsing = null;

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
        return $this->mountUsing ?? static function (Action $action, ?ComponentContainer $form = null): void {
            if (! $action->shouldOpenModal()) {
                return;
            }

            if (! $form) {
                return;
            }

            $form->fill();
        };
    }
}
