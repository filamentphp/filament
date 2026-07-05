<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;

class ModalBrowserTest extends Page
{
    protected string $view = 'pages.modal-browser-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedBolt;

    protected static ?int $navigationSort = 5;

    protected static bool $shouldRegisterNavigation = false;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('modalFocusRestoration')
                ->label('Modal focus restoration')
                ->requiresConfirmation()
                ->action(static fn () => null)
                ->extraAttributes(['data-testid' => 'basic-trigger'])
                ->extraModalWindowAttributes(['data-testid' => 'basic-modal']),
            Action::make('nonOverlayFocusRestoration')
                ->label('Non-overlay focus restoration')
                ->requiresConfirmation()
                ->modalContent(function (): HtmlString {
                    if (count($this->mountedActions ?? []) === 1) {
                        usleep(250_000);
                    }

                    return new HtmlString('<p>Simulated network delay.</p>');
                })
                ->action(static fn () => null)
                ->extraAttributes(['data-testid' => 'non-overlay-trigger'])
                ->extraModalWindowAttributes(['data-testid' => 'non-overlay-modal'])
                ->extraModalFooterActions([
                    Action::make('nonOverlayNestedFocusRestoration')
                        ->label('Open nested modal')
                        ->requiresConfirmation()
                        ->action(static fn () => null)
                        ->extraAttributes(['data-testid' => 'non-overlay-nested-trigger'])
                        ->extraModalWindowAttributes(['data-testid' => 'non-overlay-nested-modal']),
                ]),
            Action::make('overlayFocusRestoration')
                ->label('Overlay focus restoration')
                ->requiresConfirmation()
                ->action(static fn () => null)
                ->extraAttributes(['data-testid' => 'overlay-trigger'])
                ->extraModalWindowAttributes(['data-testid' => 'overlay-modal'])
                ->extraModalFooterActions([
                    Action::make('overlayNestedFocusRestoration')
                        ->label('Open nested modal')
                        ->requiresConfirmation()
                        ->overlayParentActions()
                        ->action(static fn () => null)
                        ->extraAttributes(['data-testid' => 'overlay-nested-trigger'])
                        ->extraModalWindowAttributes(['data-testid' => 'overlay-nested-modal']),
                    Action::make('overlayCancelParentFocusRestoration')
                        ->label('Close all')
                        ->requiresConfirmation()
                        ->overlayParentActions()
                        ->cancelParentActions()
                        ->action(static fn () => null)
                        ->extraModalWindowAttributes(['data-testid' => 'overlay-cancel-modal']),
                ]),
        ];
    }
}
