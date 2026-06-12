<?php

namespace App\Providers\Filament;

use App\Filament\Clusters\Settings\SettingsCluster;
use App\Filament\Pages\Analytics;
use App\Filament\Resources\PostResource;
use App\Filament\Resources\TagResource;
use App\Filament\Resources\UserResource;
use App\Filament\Widgets\DashboardOrdersChart;
use App\Filament\Widgets\DashboardRevenueChart;
use App\Filament\Widgets\DashboardStatsOverview;
use App\Filament\Widgets\DashboardTableWidget;
use App\Http\Middleware\AutoLogin;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Auth\MultiFactor\Email\EmailAuthentication;
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
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $configure = require 'configure.php';

        $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->registration()
            ->passwordReset()
            ->profile()
            ->multiFactorAuthentication([
                AppAuthentication::make(),
                EmailAuthentication::make(),
            ])
            ->colors([
                'primary' => match (request()->query('primaryColor')) {
                    'blue' => Color::Blue,
                    'green' => Color::Green,
                    'rose' => Color::Rose,
                    'violet' => Color::Violet,
                    'slate' => Color::Slate,
                    default => Color::Amber,
                },
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: app()->getNamespace() . 'Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: app()->getNamespace() . 'Filament\\Pages')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: app()->getNamespace() . 'Filament\\Clusters')
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
                                NavigationItem::make('Categories')
                                    ->icon(Heroicon::OutlinedTag)
                                    ->url('#'),
                            ]),
                        NavigationGroup::make('Content')
                            ->items([
                                ...PostResource::getNavigationItems(),
                                NavigationItem::make('Pages')
                                    ->icon(Heroicon::OutlinedDocumentDuplicate)
                                    ->url('#'),
                                ...TagResource::getNavigationItems(),
                                ...UserResource::getNavigationItems(),
                            ]),
                        NavigationGroup::make('Reports')
                            ->items([
                                ...Analytics::getNavigationItems(),
                            ]),
                        NavigationGroup::make('Settings')
                            ->items([
                                ...SettingsCluster::getNavigationItems(),
                            ]),
                    ]);
            })
            ->globalSearchKeyBindings(
                request()->query('globalSearchKeyBindings')
                    ? ['command+k', 'ctrl+k']
                    : [],
            )
            ->globalSearchFieldKeyBindingSuffix()
            ->widgets([
                DashboardStatsOverview::class,
                DashboardRevenueChart::class,
                DashboardOrdersChart::class,
                DashboardTableWidget::class,
            ])
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

        $configure($panel);

        // Query-parameter-based panel config overrides for screenshot automation.
        if ($maxContentWidth = request()->query('maxContentWidth')) {
            $width = Width::tryFrom($maxContentWidth);

            if ($width) {
                $panel->maxContentWidth($width);
            }
        }

        if ($font = request()->query('font')) {
            $panel->font($font);
        }

        if ($brandName = request()->query('brandName')) {
            $panel->brandName($brandName);
        }

        if (request()->query('topNavigation')) {
            $panel->topNavigation();
        }

        if ($simplePageMaxContentWidth = request()->query('simplePageMaxContentWidth')) {
            $simpleWidth = Width::tryFrom($simplePageMaxContentWidth);

            if ($simpleWidth) {
                $panel->simplePageMaxContentWidth($simpleWidth);
            }
        }

        if ($sidebarWidth = request()->query('sidebarWidth')) {
            $panel->sidebarWidth($sidebarWidth);
        }

        if (request()->query('sidebarFullyCollapsibleOnDesktop')) {
            $panel->sidebarFullyCollapsibleOnDesktop();
        }

        if (request()->query('brandLogo')) {
            $panel->brandLogo(fn () => view('filament.logo'))
                ->brandLogoHeight('2rem');
        }

        return $panel;
    }
}
