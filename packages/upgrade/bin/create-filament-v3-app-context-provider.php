<?php

$config = file_exists('config/filament.php') ? file_get_contents('config/filament.php') : '';

$path = $config['path'] ?? 'admin';

$pathPhp = preg_match('/\'path\'\s*=>\s*(.*),/', $config, $matches) ? $matches[1] : '\'admin\'';
$path = preg_match('/env\(\'FILAMENT_PATH\',\s*(.*)\)/', $pathPhp, $matches) ? $matches[1] : 'admin';

$isAdmin = trim($path, '/') === 'admin';
$className = $isAdmin ? 'AdminContextProvider' : 'AppContextProvider';
$id = $isAdmin ? 'admin' : 'app';

$domainPhp = preg_match('/\'domain\'\s*=>\s*(.*),/', $config, $matches) ? $matches[1] : null;
$domainPhp = $domainPhp ? "\n            ->domain({$domainPhp})" : '';

$authGuardPhp = preg_match('/\'guard\'\s*=>\s*(.*),/', $config, $matches) ? $matches[1] : null;
if ($authGuardPhp === 'env(\'FILAMENT_AUTH_GUARD\', \'web\')') {
    $authGuardPhp = null;
}
$authGuardPhp = $authGuardPhp ? "\n            ->authGuard({$authGuardPhp})" : '';

$resourcesNamespacePhp = preg_match("/'resources'\s*=>\s*\[\s*'namespace'\s*=>\s*(.*),\s*'path'\s*=>\s*(.*),/", $config, $matches) ? $matches[1] : 'app_path(\'Filament/Resources\')';
$resourcesPathPhp = preg_match("/'resources'\s*=>\s*\[\s*'namespace'\s*=>\s*(.*),\s*'path'\s*=>\s*(.*),/", $config, $matches) ? $matches[2] : '\'App\\\\Filament\\\\Resources\'';

$pagesNamespacePhp = preg_match("/'pages'\s*=>\s*\[\s*'namespace'\s*=>\s*(.*),\s*'path'\s*=>\s*(.*),/", $config, $matches) ? $matches[1] : 'app_path(\'Filament/Pages\')';
$pagesPathPhp = preg_match("/'pages'\s*=>\s*\[\s*'namespace'\s*=>\s*(.*),\s*'path'\s*=>\s*(.*),/", $config, $matches) ? $matches[2] : '\'App\\\\Filament\\\\Pages\'';

$widgetsNamespacePhp = preg_match("/'widgets'\s*=>\s*\[\s*'namespace'\s*=>\s*(.*),\s*'path'\s*=>\s*(.*),/", $config, $matches) ? $matches[1] : 'app_path(\'Filament/Widgets\')';
$widgetsPathPhp = preg_match("/'widgets'\s*=>\s*\[\s*'namespace'\s*=>\s*(.*),\s*'path'\s*=>\s*(.*),/", $config, $matches) ? $matches[2] : '\'App\\\\Filament\\\\Widgets\'';

$databaseNotificationsPhp = preg_match("/'database_notifications'\s*=>\s*\[\s*'enabled'\s*=>\s*(.*),/", $config, $matches) ? $matches[1] : null;
if ($databaseNotificationsPhp === 'false') {
    $databaseNotificationsPhp = '';
} elseif ($databaseNotificationsPhp === 'true') {
    $databaseNotificationsPhp = "\n            ->databaseNotifications()";
} else {
    $databaseNotificationsPhp = "\n            ->databaseNotifications({$databaseNotificationsPhp})";
}

$databaseNotificationsPollingIntervalPhp = preg_match("/'database_notifications'\s*=>\s*\[\s*'enabled'\s*=>\s*(.*),\s*'polling_interval'\s*=>\s*(.*),/", $config, $matches) ? $matches[2] : null;
if ($databaseNotificationsPollingIntervalPhp === '\'30s\'') {
    $databaseNotificationsPollingIntervalPhp = null;
}
$databaseNotificationsPollingIntervalPhp = $databaseNotificationsPollingIntervalPhp ? "\n            ->databaseNotificationsPollingInterval({$databaseNotificationsPollingIntervalPhp})" : '';

$navigationGroupsPhp = null;

$serviceProviders = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('app/Providers'), RecursiveIteratorIterator::SELF_FIRST);

foreach ($serviceProviders as $serviceProvider) {
    if ($serviceProvider->isDir()) {
        continue;
    }

    $serviceProviderContents = file_get_contents($serviceProvider->getPathname());

    if (! preg_match('/Filament::registerNavigationGroups\((\[[^\]]*\])\)/', $serviceProviderContents, $matches)) {
        continue;
    }

    $navigationGroupsPhp = preg_replace('/\n\s*/', "\n                ", $matches[1]);
    $navigationGroupsPhp = str_replace("\n                ]", "\n            ]", $navigationGroupsPhp);

    $serviceProviderContents = preg_replace('/Filament::registerNavigationGroups\((\[[^\]]*\])\);/', '', $serviceProviderContents);

    file_put_contents($serviceProvider->getPathname(), $serviceProviderContents);

    break;
}

$navigationGroupsPhp = $navigationGroupsPhp ? "\n            ->navigationGroups({$navigationGroupsPhp})" : '';

if (! file_exists('app/Providers/Filament')) {
    mkdir('app/Providers/Filament', 0777, true);
}
file_put_contents("app/Providers/Filament/{$className}.php", "<?php

namespace App\Providers\Filament;

use Filament\Context;
use Filament\ContextProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Support\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class {$className} extends ContextProvider
{
    public function context(Context \$context): Context
    {
        return \$context
            ->default()
            ->id('{$id}')
            ->path({$pathPhp}){$domainPhp}
            ->login(){$authGuardPhp}
            ->primaryColor(Color::Amber)
            ->discoverResources(in: {$resourcesPathPhp}, for: {$resourcesNamespacePhp})
            ->discoverPages(in: {$pagesPathPhp}, for: {$pagesNamespacePhp})
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: {$widgetsPathPhp}, for: {$widgetsNamespacePhp})
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ]){$navigationGroupsPhp}{$databaseNotificationsPhp}{$databaseNotificationsPollingIntervalPhp}
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}");

file_put_contents('config/app.php', str_replace(
    'App\\Providers\\RouteServiceProvider::class,' . PHP_EOL,
    "App\\Providers\\Filament\\{$className}::class," . PHP_EOL . '        App\\Providers\\RouteServiceProvider::class,' . PHP_EOL,
    file_get_contents('config/app.php'),
));

if (file_exists('config/filament.php')) {
    file_put_contents('config/filament.php', '<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Broadcasting
    |--------------------------------------------------------------------------
    |
    | By uncommenting the Laravel Echo configuration, you may connect Filament
    | to any Pusher-compatible websockets server.
    |
    | This will allow your users to receive real-time notifications.
    |
    */

    \'broadcasting\' => [

        // \'echo\' => [
        //     \'broadcaster\' => \'pusher\',
        //     \'key\' => env(\'VITE_PUSHER_APP_KEY\'),
        //     \'cluster\' => env(\'VITE_PUSHER_APP_CLUSTER\'),
        //     \'forceTLS\' => true,
        // ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | This is the storage disk Filament will use to put media. You may use any
    | of the disks defined in the `config/filesystems.php`.
    |
    */

    \'default_filesystem_disk\' => env(\'FILAMENT_FILESYSTEM_DISK\', \'public\'),

];');
}
