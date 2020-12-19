<?php

namespace Filament\Http\Controllers;

use Filament\Traits\CanPretendToBeAFile;

class AssetController
{
    use CanPretendToBeAFile;
    
    /**
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function css()
    {
        return $this->pretendResponseIsFile('css/filament.css', 'text/css; charset=utf-8');
    }

    /**
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function cssMap()
    {
        return $this->pretendResponseIsFile('css/filament.css.map', 'application/javascript; charset=utf-8');
    }

    /**
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function js()
    {
        return $this->pretendResponseIsFile('js/filament.js', 'application/javascript; charset=utf-8');
    }

    /**
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function jsMap()
    {
        return $this->pretendResponseIsFile('js/filament.js.map', 'application/javascript; charset=utf-8');
    }
}