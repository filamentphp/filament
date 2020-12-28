<?php

namespace Filament\View\Components;

use Illuminate\View\Component;
use Filament\Facades\Filament;

class Image extends Component
{
    public $src;
    public $manipulations;
    public $dprs;

    /**
     * Create the component instance.
     *
     * @param   string  $src
     * @param   array   $manipulations
     * @param   array   $dprs 
     * 
     * @return  void
     */
    public function __construct($src, $manipulations, $dprs = [1, 1.5, 2, 3])
    {
        $this->src = $src;
        $this->manipulations = $manipulations;
        $this->dprs = $dprs;
    }

    public function src($dpr = 1): string
    {
        return Filament::image($this->src, array_merge(['dpr' => $dpr], $this->manipulations));
    }

    public function srcSet(): string
    {
        $srcSet = [];
        foreach($this->dprs as $dpr) {
            $srcSet[] = $this->src($dpr).' '.$dpr.'x';
        }
        
        return implode(', ', $srcSet);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|\Closure|string
     */
    public function render()
    {
        return view('filament::components.image');
    }
}