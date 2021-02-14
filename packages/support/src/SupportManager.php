<?php

namespace Filament;

class SupportManager
{
    public $scripts = [];

    public $styles = [];

    public function getScripts()
    {
        return $this->scripts;
    }

    public function getStyles()
    {
        return $this->styles;
    }

    public function registerScript($filename, $path)
    {
        $this->scripts[$filename] = $path;
    }

    public function registerStyles($filename, $path)
    {
        $this->styles[$filename] = $path;
    }
}
