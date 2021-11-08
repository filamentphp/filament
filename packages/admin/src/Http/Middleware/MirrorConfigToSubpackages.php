<?php

namespace Filament\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MirrorConfigToSubpackages
{
    public function handle(Request $request, Closure $next)
    {
        $config = app()->config;

        $config->set('forms.default_filesystem_disk', $config->get('filament.default_filesystem_disk'));
        $config->set('tables.default_filesystem_disk', $config->get('filament.default_filesystem_disk'));

        return $next($request);
    }
}
