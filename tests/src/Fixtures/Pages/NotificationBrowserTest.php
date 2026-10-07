<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class NotificationBrowserTest extends Page
{
    protected string $view = 'pages.notification-browser-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedBell;

    protected static bool $shouldRegisterNavigation = false;

    public bool $isInlineNotificationShown = false;

    public function showInlineNotification(): void
    {
        $this->isInlineNotificationShown = true;
    }

    public function sendTimedNotification(int $duration = 800): void
    {
        Notification::make('timed-notification')
            ->title('Timed notification')
            ->duration($duration)
            ->send();
    }

    public function sendPersistentNotification(): void
    {
        Notification::make('persistent-notification')
            ->title('Persistent notification')
            ->persistent()
            ->send();
    }

    public function sendGroupedActionNotification(): void
    {
        Notification::make('grouped-action-notification')
            ->title('Grouped action notification')
            ->persistent()
            ->actions([
                ActionGroup::make([
                    Action::make('grouped-action')
                        ->label('Grouped action')
                        ->extraAttributes(['data-testid' => 'grouped-action']),
                    Action::make('disabled-action')
                        ->label('Disabled action')
                        ->disabled()
                        ->extraAttributes(['data-testid' => 'disabled-action']),
                    Action::make('link-action')
                        ->label('Link action')
                        ->url('#link-action')
                        ->extraAttributes(['data-testid' => 'link-action']),
                    Action::make('post-action')
                        ->label('Post action')
                        ->url('/notification-browser-test')
                        ->postToUrl()
                        ->extraAttributes(['data-testid' => 'post-action']),
                    ActionGroup::make([
                        Action::make('nested-action')
                            ->label('Nested action')
                            ->extraAttributes(['data-testid' => 'nested-action']),
                    ])
                        ->label('Nested group')
                        ->extraAttributes(['data-testid' => 'nested-group']),
                ])
                    ->label('More actions')
                    ->color('gray')
                    ->extraAttributes(['data-testid' => 'action-group'])
                    ->button(),
            ])
            ->send();
    }

    public function getResponsiveActionGroup(): ActionGroup
    {
        return ActionGroup::make([
            Action::make('responsive-action')
                ->label('Responsive action')
                ->url('#responsive-action')
                ->extraAttributes(['data-testid' => 'responsive-action']),
            ActionGroup::make([
                Action::make('styled-nested-action')
                    ->label('Nested action')
                    ->extraAttributes(['data-testid' => 'styled-nested-action']),
            ])
                ->button()
                ->label('Nested actions')
                ->extraAttributes(['data-testid' => 'styled-nested-trigger']),
        ])
            ->label('Responsive actions')
            ->labeledFrom('md')
            ->button()
            ->extraAttributes(['data-testid' => 'responsive-action-group']);
    }
}
