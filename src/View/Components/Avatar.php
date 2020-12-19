<?php

namespace Filament\View\Components;

use Illuminate\View\Component;
use Thomaswelton\LaravelGravatar\Facades\Gravatar;
use Filament\Facades\Filament;

class Avatar extends Component
{
    public $user;
    public $size;
    public $dprs;

    /**
     * Create the component instance.
     *
     * @param   object  $user
     * @param   int     $size 
     * 
     * @return  void
     */
    public function __construct($user, $size = 48, $dprs = [1, 2, 3])
    {
        $this->user = $user;
        $this->size = $size;
        $this->dprs = $dprs;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|\Closure|string
     */
    public function render()
    {
        return view('filament::components.avatar');
    }

    public function srcSet(): string
    {
        $srcSet = [];
        foreach($this->dprs as $dpr) {
            $srcSet[] = $this->src($dpr).' '.$dpr.'x';
        }
        
        return implode(', ', $srcSet);
    }

    public function src(int $dpr = 1): string
    {
        if (!$this->user->avatar) {
            return Gravatar::src($this->user->email, $this->size * $dpr);
        }        

        return Filament::url($this->user->avatar, [
            'w' => $this->size,
            'h' => $this->size,
            'fit' => 'crop',
            'dpr' => $dpr,
        ]);
    }
}