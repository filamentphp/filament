<?php

namespace Filament\Pages\Actions\Concerns;

use Closure;
use Filament\Forms\ComponentContainer;

trait CanBeMounted
{
    protected ?Closure $mountUsing = null;

    public function mountUsing(?Closure $callback): static
    {
        $this->mountUsing = $callback;

        return $this;
    }

    public function getMountUsing(): Closure
    {
        return $this->mountUsing ?? function ($action, ?ComponentContainer $form = null): void {
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
