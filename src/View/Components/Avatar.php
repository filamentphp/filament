<?php

namespace Filament\View\Components;

use Filament\Filament;
use function Filament\get_image_url;
use Illuminate\View\Component;

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

    public function render()
    {
        return view('filament::components.avatar');
    }

    public function src($dpr = 1)
    {
        $avatar = $this->user->getFilamentAvatar();

        if ($avatar === null) {
            return Filament::avatarProvider()->get($this->user, $this->size, $dpr);
        }

        return get_image_url(
            $avatar,
            [
                'dpr' => $dpr,
                'fit' => 'crop',
                'h' => $this->size,
                'w' => $this->size,
            ],
        );
    }

    public function srcSet()
    {
        return collect($this->dprs)
            ->map(fn ($dpr) => $this->src($dpr) . ' ' . $dpr . 'x')
            ->implode(', ');
    }
}
