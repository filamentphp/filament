<?php

namespace Alpine;

class AlpineBladeDirectives {
    public static function alpineAssets()
    {
        return '{!! \Alpine::assets() !!}';
    }
}