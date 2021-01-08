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
        [$pushName, $pushSub] = explode(':', trim(substr($expression, 1, -1)));

        $key = '__pushonce_'.str_replace('-', '_', $pushName).'_'.str_replace('-', '_', $pushSub);

        return "<?php if(! isset(\$__env->{$key})): \$__env->{$key} = 1; \$__env->startPush('{$pushName}'); ?>";
    }
    
    public static function endPushOnce(): string
    {
        return '<?php $__env->stopPush(); endif; ?>';
    }
}
