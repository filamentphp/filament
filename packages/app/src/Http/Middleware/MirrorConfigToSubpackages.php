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
        $config->set('filament-actions.dark_mode', $darkMode);
        $config->set('filament-forms.dark_mode', $darkMode);
        $config->set('filament-notifications.dark_mode', $darkMode);
        $config->set('filament-tables.dark_mode', $darkMode);

        $defaultFilesystemDisk = $config->get('filament.default_filesystem_disk');
        $config->set('filament-forms.default_filesystem_disk', $defaultFilesystemDisk);
        $config->set('filament-tables.default_filesystem_disk', $defaultFilesystemDisk);

        $actionsAlignment = $config->get('filament.layout.actions.modal.actions.alignment');
        $config->set('filament-forms.components.actions.modal.actions.alignment', $actionsAlignment);
        $config->set('filament-tables.layout.actions.modal.actions.alignment', $actionsAlignment);

        $config->set('filament-notifications.layout.alignment.horizontal', $config->get('filament.layout.notifications.alignment'));
        $config->set('filament-notifications.layout.alignment.vertical', $config->get('filament.layout.notifications.vertical_alignment'));

        return $next($request);
    }
}
