<?php

namespace Filament;

class NavigationItem
{
    public $activeRule;

    public $icon = 'heroicon-o-collection';

    public $label;

    public $sort = 0;

    public $url;

    public function __construct($label, $url)
    {
        $this->label($label);
        $this->url($url);
    }

    public function activeRule($rule)
    {
        $this->activeRule = $rule;

        return $this;
    }

    public function icon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    public function label($label)
    {
        $this->label = $label;

        return $this;
    }

    public static function make($label, $url)
    {
        return new static($label, $url);
    }

    public function sort($sort)
    {
        $this->sort = $sort;

        return $this;
    }

    public function url($url)
    {
        $this->url = $url;

        return $this;
    }
}
