<?php

namespace Filament\Tests\Inertia;

use Filament\Facades\Filament;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Inertia\Fixtures\InertiaPanelProvider;
use Filament\Tests\Inertia\Fixtures\PanelUser;
use Filament\Tests\Inertia\Fixtures\RequestState;
use Filament\Tests\Inertia\Fixtures\TenancyPanelProvider;
use Filament\Tests\TestCase as BaseTestCase;
use Inertia\ServiceProvider;

class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [...parent::getPackageProviders($app), ServiceProvider::class, InertiaPanelProvider::class, TenancyPanelProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('auth.guards.panel', ['driver' => 'session', 'provider' => 'panel-users']);
        $app['config']->set('auth.providers.panel-users', ['driver' => 'eloquent', 'model' => PanelUser::class]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        RequestState::$allowed = true;
        RequestState::$responses = 0;
        RequestState::$mounts = 0;
        RequestState::$terminations = 0;
        RequestState::$shares = 0;
        config(['inertia.ssr.enabled' => false]);
        Filament::setCurrentPanel(Filament::getPanel('inertia-tests'));
        $this->actingAs(PanelUser::query()->findOrFail(User::factory()->create()->getKey()));
    }
}
