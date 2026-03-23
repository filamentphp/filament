<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Tenancy\EditTeamProfile;
use App\Filament\Pages\Tenancy\RegisterTeam;
use App\Http\Middleware\AutoLogin;
use App\Models\Team;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class TenancyDemoPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('tenancy')
            ->path('tenancy')
            ->login()
            ->tenant(Team::class, slugAttribute: 'slug')
            ->tenantRegistration(RegisterTeam::class)
            ->tenantProfile(EditTeamProfile::class)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->pages([
                Dashboard::class,
            ])
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                return $builder
                    ->items([
                        ...Dashboard::getNavigationItems(),
                    ])
                    ->groups([
                        NavigationGroup::make('Shop')
                            ->items([
                                NavigationItem::make('Products')
                                    ->icon(Heroicon::OutlinedShoppingBag)
                                    ->badge(642)
                                    ->url('#'),
                                NavigationItem::make('Orders')
                                    ->icon(Heroicon::OutlinedShoppingCart)
                                    ->badge(12, 'warning')
                                    ->url('#'),
                                NavigationItem::make('Customers')
                                    ->icon(Heroicon::OutlinedUserGroup)
                                    ->url('#'),
                            ]),
                        NavigationGroup::make('Content')
                            ->items([
                                NavigationItem::make('Posts')
                                    ->icon(Heroicon::OutlinedDocumentText)
                                    ->url('#'),
                                NavigationItem::make('Pages')
                                    ->icon(Heroicon::OutlinedDocumentDuplicate)
                                    ->url('#'),
                            ]),
                    ]);
            })
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
                AutoLogin::class,
                Authenticate::class,
            ]);
    }
}
