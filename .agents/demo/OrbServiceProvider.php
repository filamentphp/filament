<?php

namespace Filament\Orb;

use Filament\Support\Facades\FilamentRealtime;
use Illuminate\Foundation\Bootstrap\RegisterProviders;
use Illuminate\Support\ServiceProvider;

class OrbServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->booting(static function (): void {
            $host = parse_url(config('app.url'), PHP_URL_HOST);
            $secret = trim(file_get_contents(base_path('.orb/reverb-secret')));

            config([
                'queue.default' => 'sync',
                'broadcasting.default' => 'reverb',
                'broadcasting.connections.reverb' => [
                    'driver' => 'pusher',
                    'key' => 'orb-demo',
                    'secret' => $secret,
                    'app_id' => 'orb-demo',
                    'options' => [
                        'host' => '127.0.0.1',
                        'port' => 6001,
                        'scheme' => 'http',
                        'useTLS' => false,
                    ],
                ],
                'reverb.apps.apps.0.key' => 'orb-demo',
                'reverb.apps.apps.0.secret' => $secret,
                'reverb.apps.apps.0.app_id' => 'orb-demo',
                'reverb.apps.apps.0.allowed_origins' => [$host],
                'filament.broadcasting.echo' => [
                    'broadcaster' => 'reverb',
                    'key' => 'orb-demo',
                    'wsHost' => $host,
                    'wsPort' => 80,
                    'wssPort' => 443,
                    'forceTLS' => true,
                    'enabledTransports' => ['ws', 'wss'],
                ],
            ]);
        });
    }

    public function boot(): void
    {
        if (env('FILAMENT_ORB_REALTIME', true) && class_exists(FilamentRealtime::class)) {
            FilamentRealtime::subscribe()->modelsInNamespace('App\\Models');
        }
    }
}

RegisterProviders::merge([OrbServiceProvider::class]);
