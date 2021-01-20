<?php

namespace Filament\Http\Controllers;

use Filament\Traits\CanPretendToBeAFile;

class AssetController
{
    use CanPretendToBeAFile;

    public function css()
    {
        return $this->pretendResponseIsFile('css/filament.css', 'text/css; charset=utf-8');
    }

    public function cssMap()
    {
        return $this->pretendResponseIsFile('css/filament.css.map', 'application/javascript; charset=utf-8');
    }

    public function js()
    {
        return $this->pretendResponseIsFile('js/filament.js', 'application/javascript; charset=utf-8');
    }

    public function jsMap()
    {
        return $this->pretendResponseIsFile('js/filament.js.map', 'application/javascript; charset=utf-8');
    }
}
