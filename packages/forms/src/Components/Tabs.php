<?php

namespace Filament\Forms\Components;

class Tabs extends Component
{
    public function getTabsConfig()
    {
        return collect($this->getSchema())
            ->filter(fn (Tab $tab) => ! $tab->isHidden())
            ->mapWithKeys(fn ($tab) => [$tab->getId() => $tab->getLabel()])
            ->toArray();
    }

    public static function make($label = null)
    {
        return (new static())->label($label);
    }

    public function tabs($tabs)
    {
        $this->schema($tabs);

        return $this;
    }
}
