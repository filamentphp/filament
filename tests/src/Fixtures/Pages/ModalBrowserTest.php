<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;

class ModalBrowserTest extends Page
{
    protected string $view = 'pages.modal-browser-test';

    public bool $didRunActionAfterClosingChild = false;

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
            Action::make('escapeCloseDisabled')
                ->label('Escape close disabled')
                ->closeModalByEscaping(false)
                ->schema([
                    TextInput::make('name'),
                    TextInput::make('email'),
                ])
                ->action(static fn () => null)
                ->extraAttributes(['data-testid' => 'escape-close-disabled-trigger'])
                ->extraModalWindowAttributes(['data-testid' => 'escape-close-disabled-modal']),
            Action::make('scrollPreservation')
                ->label('Scroll preservation')
                ->modalSubmitAction(false)
                ->stickyModalFooter()
                ->schema(array_map(
                    fn (int $index): TextInput => TextInput::make("scrollField{$index}"),
                    range(1, 25),
                ))
                ->extraModalWindowAttributes(['data-testid' => 'scroll-modal'])
                ->extraModalFooterActions([
                    Action::make('scrollPreservationNested')
                        ->label('Open nested modal')
                        ->requiresConfirmation()
                        ->action(static fn () => null)
                        ->extraModalWindowAttributes(['data-testid' => 'scroll-nested-modal']),
                ]),
            Action::make('clickThrough')
                ->label('Click through')
                ->modalClickThrough()
                ->modalSubmitAction(false)
                ->modalHeading('Click-through modal')
                ->modalContent(new HtmlString('<p>You can still scroll the page behind this modal.</p>'))
                ->extraAttributes(['data-testid' => 'click-through-trigger'])
                ->extraModalWindowAttributes(['data-testid' => 'click-through-modal']),
            Action::make('cancelParentActionsOnClose')
                ->label('Cancel parents on close')
                ->modalSubmitAction(false)
                ->modalDescription('Grandparent (level 1).')
                ->extraAttributes(['data-testid' => 'cancel-on-close-trigger'])
                ->extraModalWindowAttributes(['data-testid' => 'cancel-on-close-modal'])
                ->extraModalFooterActions([
                    Action::make('cancelParentActionsOnCloseParent')
                        ->label('Open level 2')
                        ->modalSubmitAction(false)
                        ->modalDescription('Parent (level 2).')
                        ->extraAttributes(['data-testid' => 'cancel-on-close-parent-trigger'])
                        ->extraModalWindowAttributes(['data-testid' => 'cancel-on-close-parent-modal'])
                        ->extraModalFooterActions([
                            Action::make('cancelParentActionsOnCloseNested')
                                ->label('Open level 3')
                                ->requiresConfirmation()
                                ->modalCloseButton()
                                ->cancelParentActionsOnClose()
                                ->action(static fn () => null)
                                ->extraAttributes(['data-testid' => 'cancel-on-close-nested-trigger'])
                                ->extraModalWindowAttributes(['data-testid' => 'cancel-on-close-nested-modal']),
                        ]),
                ]),
        ];
    }

    public function modalLessParentWithChildAction(): Action
    {
        return Action::make('modalLessParentWithChild')
            ->registerModalActions([
                Action::make('modalLessParentChild')
                    ->requiresConfirmation()
                    ->color('gray')
                    ->action(static fn () => null)
                    ->extraModalWindowAttributes(['data-testid' => 'modal-less-parent-child-modal']),
            ])
            ->action(function (Action $action): void {
                $action->getLivewire()->mountAction('modalLessParentChild');

                $action->halt();
            });
    }

    public function runAfterClosingChildAction(): Action
    {
        return Action::make('runAfterClosingChild')
            ->action(function (): void {
                $this->didRunActionAfterClosingChild = true;
            });
    }
}
