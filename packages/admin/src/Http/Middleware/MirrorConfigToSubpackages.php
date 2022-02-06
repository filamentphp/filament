<?php

namespace Filament\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MirrorConfigToSubpackages
{
    public function handle(Request $request, Closure $next)
    {
        $config = app()->config;

        $darkMode = $config->get('filament.dark_mode');
        $config->set('forms.dark_mode', $darkMode);
        $config->set('tables.dark_mode', $darkMode);

        $defaultFilesystemDisk = $config->get('filament.default_filesystem_disk');
        $config->set('forms.default_filesystem_disk', $defaultFilesystemDisk);
        $config->set('tables.default_filesystem_disk', $defaultFilesystemDisk);

        return $next($request);
    }
}
