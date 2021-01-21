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
        $this->dprs = $dprs;
        $this->src = $src;
        $this->manipulations = $manipulations;
    }

    public function srcSet()
    {
        collect($this->dprs)->map(fn ($dpr) => $this->src($dpr) . ' ' . $dpr . 'x')->implode(', ')->toArray();
    }

    public function src($dpr = 1)
    {
        return \Filament\get_image_url($this->src, array_merge(['dpr' => $dpr], $this->manipulations));
    }

    public function render()
    {
        return view('filament::components.image');
    }
}
