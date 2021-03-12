<?php

namespace Filament\Forms\Components;

use Illuminate\Support\Str;

class Tabs extends Component
{
    public static function make($label = null)
    {
        return (new static())->label($label);
    }

    public function getTabsConfig()
    {
        return collect($this->getSchema())
            ->mapWithKeys(fn ($tab) => [$this->getId() . '.' . $tab->getId() => $tab->getLabel()])
            ->toArray();
    }

    public function id($id)
    {
        if ($this->id === null && $this->getLabel()) {
            return Str::slug($this->getLabel());
        }

        return parent::id($id);
    }

    public function tabs($tabs)
    {
        $this->configure(function () use ($tabs) {
            $this->schema($tabs);
        });

        return $this;
    }
}
