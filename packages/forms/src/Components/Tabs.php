<?php

namespace Filament\Forms\Components;

use Illuminate\Support\Str;

class Tabs extends Component
{
    public static function make($label = null)
    {
        $tabs = (new static())->label($label);

        if ($label) $tabs = $tabs->id(Str::slug($label));

        return $tabs;
    }

    public function getTabsConfig()
    {
        return collect($this->schema)
            ->mapWithKeys(fn ($tab) => [$this->getId() . '.' . $tab->getId() => __($tab->label)])
            ->toArray();
    }

    public function tabs($tabs)
    {
        $this->schema($tabs);

        return $this;
    }
}
