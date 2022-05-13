<?php

namespace Filament\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MirrorConfigToSubpackages
{
    public function handle(Request $request, Closure $next)
    {
        $config = config();

        $darkMode = $config->get('filament.dark_mode');
        $config->set('forms.dark_mode', $darkMode);
        $config->set('tables.dark_mode', $darkMode);

        $defaultFilesystemDisk = $config->get('filament.default_filesystem_disk');
        $config->set('forms.default_filesystem_disk', $defaultFilesystemDisk);
        $config->set('tables.default_filesystem_disk', $defaultFilesystemDisk);

        $actionsAlignment = $config->get('filament.layout.forms.actions.alignment');
        $config->set('forms.components.actions.modal.actions.alignment', $actionsAlignment);
        $config->set('tables.layout.actions.modal.actions.alignment', $actionsAlignment);

        return $next($request);
    }
}
