<?php

namespace Filament;

class BladeDirectives 
{
    public static function styles(): string
    {
        return '{!! \Filament::styles() !!}';
    }

    public static function scripts(): string
    {
        return '{!! \Filament::scripts() !!}';
    }

    public static function pushOnce($expression): string
    {
        $var = '$__env->{"__pushonce_" . md5(__FILE__ . ":" . __LINE__)}';
    
        return "<?php if(!isset({$var})): {$var} = true; \$__env->startPush({$expression}); ?>";
    }
    
    public static function endPushOnce(): string
    {
        return '<?php $__env->stopPush(); endif; ?>';
    }
}
