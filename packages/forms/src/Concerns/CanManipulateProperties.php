<?php

namespace Filament\Forms\Concerns;

trait CanManipulateProperties
{
    public function fillWithFormDefaults()
    {
        $this->fill($this->getPropertyDefaults());
    }

    public function getPropertyDefaults()
    {
        return $this->getForm()->getDefaultValues();
    }

    public function reset(...$properties)
    {
        parent::reset(...$properties);

        $defaults = $this->getPropertyDefaults();

        if (count($properties) && is_array($properties[0])) {
            $properties = $properties[0];
        }

        if (empty($properties)) {
            $properties = array_keys($defaults);
        }

        $propertiesToFill = collect($properties)
            ->filter(fn ($property) => in_array($property, $defaults))
            ->mapWithKeys(fn ($property) => [$property => $defaults[$property]])
            ->toArray();

        $this->fill($propertiesToFill);
    }
}
