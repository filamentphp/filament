<?php

namespace Filament\Tables\Actions\Concerns;

use Filament\Forms\ComponentContainer;

trait CanBeMounted
{
    protected $mountUsing = null;

    public function mountUsing(callable $callback): static
    {
        $this->mountUsing = $callback;

        return $this;
    }

    public function getMountUsing(): callable
    {
        return $this->mountUsing ?? function ($action, ?ComponentContainer $form = null) {
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
