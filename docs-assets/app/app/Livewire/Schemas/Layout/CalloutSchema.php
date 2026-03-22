<?php

namespace App\Livewire\Schemas\Layout;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\EmptyState;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class CalloutSchema
{
    public static function schema(): array
    {
        return [
            Group::make()
                ->id('callout')
                ->extraAttributes([
                    'class' => 'p-16 max-w-2xl',
                ])
                ->schema([
                    Callout::make('New version available')
                        ->description('Filament v4 has been released with exciting new features and improvements.')
                        ->info(),
                ]),
            Group::make()
                ->id('calloutStatuses')
                ->extraAttributes([
                    'class' => 'p-16 max-w-2xl space-y-4',
                ])
                ->schema([
                    Callout::make('Payment successful')
                        ->description('Your order has been confirmed and is being processed.')
                        ->success(),
                    Callout::make('Session expiring soon')
                        ->description('Your session will expire in 5 minutes. Save your work to avoid losing changes.')
                        ->warning(),
                    Callout::make('Connection failed')
                        ->description('Unable to connect to the server. Please check your internet connection.')
                        ->danger(),
                ]),
            Group::make()
                ->id('calloutWithoutBackground')
                ->extraAttributes([
                    'class' => 'p-16 max-w-2xl',
                ])
                ->schema([
                    Callout::make('Scheduled maintenance')
                        ->description('The system will be unavailable on Sunday from 2:00 AM to 4:00 AM.')
                        ->warning()
                        ->color(null),
                ]),
            Group::make()
                ->id('calloutCustomColor')
                ->extraAttributes([
                    'class' => 'p-16 max-w-2xl',
                ])
                ->schema([
                    Callout::make('Pro tip')
                        ->description('You can use keyboard shortcuts to navigate faster. Press ? to see all available shortcuts.')
                        ->color('primary')
                        ->icon(Heroicon::OutlinedLightBulb)
                        ->iconColor('primary'),
                ]),
            Group::make()
                ->id('calloutActions')
                ->extraAttributes([
                    'class' => 'p-16 max-w-2xl',
                ])
                ->schema([
                    Callout::make('Your trial ends in 3 days')
                        ->description('Upgrade now to keep access to all premium features.')
                        ->warning()
                        ->actions([
                            Action::make('upgrade')
                                ->label('Upgrade to Pro')
                                ->button(),
                            Action::make('compare')
                                ->label('Compare plans'),
                        ]),
                ]),
            Group::make()
                ->id('calloutCustomIcon')
                ->extraAttributes([
                    'class' => 'p-16 max-w-2xl',
                ])
                ->schema([
                    Callout::make('Pro tip')
                        ->description('You can use keyboard shortcuts to navigate faster. Press ? to see all available shortcuts.')
                        ->icon(Heroicon::OutlinedLightBulb)
                        ->iconColor('primary'),
                ]),
            Group::make()
                ->id('calloutIconSize')
                ->extraAttributes([
                    'class' => 'p-16 max-w-2xl',
                ])
                ->schema([
                    Callout::make('Quick note')
                        ->description('This callout has a smaller icon.')
                        ->info()
                        ->iconSize(IconSize::Small),
                ]),
            Group::make()
                ->id('calloutFooter')
                ->extraAttributes([
                    'class' => 'p-16 max-w-2xl',
                ])
                ->schema([
                    Callout::make('Backup complete')
                        ->description('Your data has been successfully backed up to the cloud.')
                        ->success()
                        ->footer([
                            Text::make('Last backup: 5 minutes ago')
                                ->color('gray'),
                            Action::make('viewBackups')
                                ->label('View All Backups')
                                ->button(),
                        ]),
                ]),
            Group::make()
                ->id('calloutControlActions')
                ->extraAttributes([
                    'class' => 'p-16 max-w-2xl',
                ])
                ->schema([
                    Callout::make('New version available')
                        ->description('Filament v4 has been released with exciting new features and improvements.')
                        ->info()
                        ->controlActions([
                            Action::make('dismiss')
                                ->icon(Heroicon::XMark)
                                ->iconButton()
                                ->color('gray'),
                        ]),
                ]),
            Group::make()
                ->id('calloutActionsAlignedEnd')
                ->extraAttributes([
                    'class' => 'p-16 max-w-2xl',
                ])
                ->schema([
                    Callout::make('Updates available')
                        ->description('New features and improvements are ready to install.')
                        ->info()
                        ->actions([
                            Action::make('install')
                                ->label('Install Now')
                                ->button(),
                            Action::make('later')
                                ->label('Remind Me Later'),
                        ])
                        ->footerActionsAlignment(Alignment::End),
                ]),
        ];
    }
}
