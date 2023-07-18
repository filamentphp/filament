<?php

namespace Filament\Forms\Concerns;

trait ListensToEvents
{
    public function dispatchEvent(string $event, mixed ...$parameters): static
    {
        foreach ($this->getComponents() as $component) {
            $component->dispatchEvent($event, ...$parameters);

            foreach ($component->getChildComponentContainers() as $container) {
                if ($container->isHidden()) {
                    continue;
                }

                $container->dispatchEvent($event, ...$parameters);
            }
        }

        return $this;
    }
}
