<?php

namespace Filament\Forms\Components;

class KeyValue extends Field
{
    protected $addButtonLabel = 'forms::fields.keyValue.addButtonLabel';

    protected $canAddRows = true;

    protected $canDeleteRows = true;

    protected $canEditKeys = true;

    protected $deleteButtonLabel = 'forms::fields.keyValue.deleteButtonLabel';

    protected $isSortable = false;

    protected $keyLabel = 'forms::fields.keyValue.keyLabel';

    protected $keyPlaceholder = 'forms::fields.keyValue.keyPlaceholder';

    protected $sortButtonLabel = 'forms::fields.keyValue.sortButtonLabel';

    protected $valueLabel = 'forms::fields.keyValue.valueLabel';

    protected $valuePlaceholder = 'forms::fields.keyValue.valuePlaceholder';

    protected function setUp()
    {
        $this->default([]);
    }

    public function addButtonLabel($label)
    {
        $this->configure(function () use ($label) {
            $this->addButtonLabel = $label;
        });

        return $this;
    }

    public function canAddRows()
    {
        return $this->canAddRows;
    }

    public function canDeleteRows()
    {
        return $this->canDeleteRows;
    }

    public function canEditKeys()
    {
        return $this->canEditKeys;
    }

    public function deleteButtonLabel($label)
    {
        $this->configure(function () use ($label) {
            $this->deleteButtonLabel = $label;
        });

        return $this;
    }

    public function disableAddingRows($state = true)
    {
        $this->configure(function () use ($state) {
            $this->canAddRows = ! $state;
        });

        return $this;
    }

    public function disableDeletingRows($state = true)
    {
        $this->configure(function () use ($state) {
            $this->canDeleteRows = ! $state;
        });

        return $this;
    }

    public function disableEditingKeys($state = true)
    {
        $this->configure(function () use ($state) {
            $this->canEditKeys = ! $state;
        });

        return $this;
    }

    public function getAddButtonLabel()
    {
        return $this->addButtonLabel;
    }

    public function getDeleteButtonLabel()
    {
        return $this->deleteButtonLabel;
    }

    public function getKeyLabel()
    {
        return $this->keyLabel;
    }

    public function getKeyPlaceholder()
    {
        return $this->keyPlaceholder;
    }

    public function getSortButtonLabel()
    {
        return $this->sortButtonLabel;
    }

    public function getValueLabel()
    {
        return $this->valueLabel;
    }

    public function getValuePlaceholder()
    {
        return $this->valuePlaceholder;
    }

    public function isSortable()
    {
        return $this->isSortable;
    }

    public function keyLabel($label)
    {
        $this->configure(function () use ($label) {
            $this->keyLabel = $label;
        });

        return $this;
    }

    public function keyPlaceholder($placeholder)
    {
        $this->configure(function () use ($placeholder) {
            $this->keyPlaceholder = $placeholder;
        });

        return $this;
    }

    public function sortable($sortable = true)
    {
        $this->configure(function () use ($sortable) {
            $this->isSortable = $sortable;
        });

        return $this;
    }

    public function sortButtonLabel($label)
    {
        $this->configure(function () use ($label) {
            $this->sortButtonLabel = $label;
        });

        return $this;
    }

    public function valueLabel($label)
    {
        $this->configure(function () use ($label) {
            $this->valueLabel = $label;
        });

        return $this;
    }

    public function valuePlaceholder($placeholder)
    {
        $this->configure(function () use ($placeholder) {
            $this->valuePlaceholder = $placeholder;
        });

        return $this;
    }
}
