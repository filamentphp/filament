<?php

namespace Filament\Tables\Concerns;

trait CanSelectRecords
{
    public $selected = [];

    public function deleteSelected()
    {
        static::getModel()::destroy($this->selected);

        $this->selected = [];
    }

    public function toggleSelectAll()
    {
        $records = $this->getRecords();

        if (! $records->count()) {
            return;
        }

        $keyName = $records->first()->getKeyName();

        if ($records->count() !== count($this->selected)) {
            $this->selected = $records->pluck($keyName)->all();
        } else {
            $this->selected = [];
        }
    }

    public function toggleSelected($record)
    {
        if (! in_array($record, $this->selected)) {
            $this->selected[] = $record;
        } else {
            $key = array_search($record, $this->selected);

            unset($this->selected[$key]);
        }
    }
}
