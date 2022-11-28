<?php

namespace Filament\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MirrorConfigToSubpackages
{
    /**
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $config = config();

        $defaultFilesystemDisk = $config->get('filament.default_filesystem_disk');
        $config->set('filament-forms.default_filesystem_disk', $defaultFilesystemDisk);
        $config->set('filament-tables.default_filesystem_disk', $defaultFilesystemDisk);

        $config->set('blade-icons.components.disabled', true);

        return $next($request);
    }
}
