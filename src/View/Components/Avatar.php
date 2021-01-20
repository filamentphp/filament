<?php

namespace Filament\View\Components;

use Filament\Filament;
use Illuminate\View\Component;
use Thomaswelton\LaravelGravatar\Facades\Gravatar;

class Avatar extends Component
{
    public $dprs;

    public $size;

    public $user;

    public function __construct($user, $size = 48, $dprs = [1, 2, 3])
    {
        $this->user = $user;
        $this->size = $size;
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

    public function src(int $dpr = 1)
    {
        if (! $this->user->avatar) {
            return Gravatar::src($this->user->email, $this->size * $dpr);
        }

        return Filament::image($this->user->avatar, [
            'w' => $this->size,
            'h' => $this->size,
            'fit' => 'crop',
            'dpr' => $dpr,
        ]);
    }

    public function render()
    {
        return view('filament::components.avatar');
    }
}
