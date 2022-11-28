<?php

namespace Filament\Forms\Concerns;

trait ListensToEvents
{
    /**
     * @param mixed ...$parameters
     */
    public function dispatchEvent(string $event, ...$parameters): static
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
