<?php

namespace Filament\Fields;

use Illuminate\Support\Str;

class Tabs extends Field
{
    public static function make($label = null)
    {
        $tabs = (new static())->label($label);

        if ($label) $tabs = $tabs->id(Str::slug($label));

        return $tabs;
    }

    public function getTabsConfig()
    {
        return collect($this->fields)
            ->mapWithKeys(fn ($tab) => [$this->id . '.' . $tab->id => __($tab->label)])
            ->toArray();
    }

    public function tabs($tabs)
    {
        $this->fields($tabs);

        return $this;
    }
}
