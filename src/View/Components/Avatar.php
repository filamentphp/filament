<?php

namespace Filament\View\Components;

use Illuminate\View\Component;
use Thomaswelton\LaravelGravatar\Facades\Gravatar;
use function Filament\get_image_url;

class Avatar extends Component
{
    public $dprs;

    public $size;

    public $user;

    public function __construct($user, $size = 48, $dprs = [1, 2, 3])
    {
        $this->dprs = $dprs;
        $this->size = $size;
        $this->user = $user;
    }

    public function src($dpr = 1)
    {
        if (! $this->user->avatar) {
            return Gravatar::src($this->user->email, $this->size * $dpr);
        }

        return get_image_url(
            $this->user->avatar,
            [
                'dpr' => $dpr,
                'fit' => 'crop',
                'h' => $this->size,
                'w' => $this->size,
            ]
        );
    }

    public function srcSet()
    {
        return collect($this->dprs)
            ->map(fn ($dpr) => $this->src($dpr) . ' ' . $dpr . 'x')
            ->implode(', ');
    }

    public function render()
    {
        return view('filament::components.avatar');
    }
}
