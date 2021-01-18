<?php

namespace Filament\View\Components;

use Filament\Filament;
use Illuminate\View\Component;

class Image extends Component
{
    public $dprs;

    public $manipulations;

    public $src;

    public function __construct($src, $manipulations, $dprs = [1, 1.5, 2, 3])
    {
        $this->src = $src;
        $this->manipulations = $manipulations;
        $this->dprs = $dprs;
    }

    public function srcSet()
    {
        $srcSet = [];
        foreach ($this->dprs as $dpr) {
            $srcSet[] = $this->src($dpr) . ' ' . $dpr . 'x';
        }

        return implode(', ', $srcSet);
    }

    public function src($dpr = 1)
    {
        return Filament::image($this->src, array_merge(['dpr' => $dpr], $this->manipulations));
    }

    public function render()
    {
        return view('filament::components.image');
    }
}
