<?php

namespace Filament\Forms\Components;

class KeyValue extends Field
{
    public $addButtonLabel = 'forms::fields.keyValue.addButtonLabel';

    public $canAddRows = true;

    public $canDeleteRows = true;

    public $canEditKeys = true;

    public $deleteButtonLabel = 'forms::fields.keyValue.deleteButtonLabel';

    public $keyLabel = 'forms::fields.keyValue.keyLabel';

    public $valueLabel = 'forms::fields.keyValue.valueLabel';

    protected function setUp()
    {
        $this->default([]);
    }

    public function addButtonLabel($label)
    {
        $this->addButtonLabel = $label;

        return $this;
    }

    public function deleteButtonLabel($label)
    {
        $this->deleteButtonLabel = $label;

        return $this;
    }

    public function disableAddingRows($state = true)
    {
        $this->canAddRows = !$state;

        return $this;
    }

    public function disableDeletingRows($state = true)
    {
        $this->canDeleteRows = !$state;

        return $this;
    }

    public function disableEditingKeys($state = true)
    {
        $this->canEditKeys = !$state;

        return $this;
    }

    public function keyLabel($label)
    {
        $this->keyLabel = $label;

        return $this;
    }

    public function valueLabel($label)
    {
        $this->valueLabel = $label;

        return $this;
    }
}
