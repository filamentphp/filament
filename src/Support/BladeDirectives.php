<?php

namespace Alpine\Support;

class BladeDirectives {
    public static function alpineAssets()
    {
        return '{!! \Alpine::assets() !!}';
    }
}