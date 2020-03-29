<?php

namespace Filament\Support;

class BladeDirectives {
    /**
     * Get Filament Assets
     * 
     * @return string
     */
    public static function assets()
    {
        return '{!! \Filament::assets() !!}';
    }

    /**
     * Push to named stacks only once
     *
     * Example:
     * @pushonce('scripts')
     *     ...
     *
     * @param string $expression
     * @return string
     */
    public static function pushOnce($expression)
    {
        $var = '$__env->{"__pushonce_" . md5(__FILE__ . ":" . __LINE__)}';
        return "<?php if (!isset({$var})): {$var} = true; \$__env->startPush({$expression}); ?>";
    }

    /**
     * End Push to named stacks only once
     *
     * Example:
     * @endpushonce
     *
     * @param string $expression
     * @return string
     */
    public static function endPushOnce($expression)
    {
        return '<?php $__env->stopPush(); endif; ?>';
    }

}