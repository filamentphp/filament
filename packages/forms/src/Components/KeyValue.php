<?php

namespace Filament\Forms\Components;

class KeyValue extends Field
{
    public $addButtonLabel = 'forms::fields.keyValue.addButtonLabel';

    public $canAddRows = true;

    public $canDeleteRows = true;

    public $canEditKeys = true;

    public $deleteButtonLabel = 'forms::fields.keyValue.deleteButtonLabel';

    public $isSortable = false;

    public $keyLabel = 'forms::fields.keyValue.keyLabel';

    public $keyPlaceholder = 'forms::fields.keyValue.keyPlaceholder';

    public $sortButtonLabel = 'forms::fields.keyValue.sortButtonLabel';

    public $valueLabel = 'forms::fields.keyValue.valueLabel';

    public $valuePlaceholder = 'forms::fields.keyValue.valuePlaceholder';

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

    public function sortButtonLabel($label)
    {
        $this->sortButtonLabel = $label;

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

    public function keyPlaceholder($placeholder)
    {
        $this->keyPlaceholder = $placeholder;

        return $this;
    }

    public function sortable($sortable = true)
    {
        $this->isSortable = $sortable;

        return $this;
    }

    public function valueLabel($label)
    {
        $this->valueLabel = $label;

        return $this;
    }

    public function valuePlaceholder($placeholder)
    {
        $this->valuePlaceholder = $placeholder;

        return $this;
    }
}
