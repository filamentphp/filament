<?php

namespace Filament\Pages;

use Illuminate\Support\Str;

class Route
{
    public $name;

    public $uri;

    public function __construct($uri, $name)
    {
        $this->name($name);
        $this->uri($uri);
    }

    public static function make($uri, $name)
    {
        return new static($uri, $name);
    }

    protected function name($name)
    {
        $this->name = $name;

        return $this;
    }

    public function uri($uri)
    {
        $this->uri = (string) Str::of($uri)->trim('/');

        return $this;
    }
}
