<?php

namespace Filament\Resources\Forms\Components\Concerns;

trait CanBeDependentOnResourceRecord
{
    public function when($condition, $callback = null)
    {
        $this->configure(function () use ($callback, $condition) {
            if (! $callback) {
                $this->hidden();

                $callback = fn ($component, $record) => $component->visible();
            }

            try {
                $shouldExecuteCallback = $condition((object) $this->getLivewire()->record);
            } catch (\Exception $exception) {
                $shouldExecuteCallback = false;
            }

            if ($shouldExecuteCallback) {
                $callback($this, (object) $this->getLivewire()->record);
            }
        });

        return $this;
    }
}
