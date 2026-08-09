<?php

namespace Filament\Tests\Fixtures\Pages;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tests\Fixtures\Models\Post;

class Actions extends Page
{
    protected string $view = 'pages.actions';

    public bool $shouldPauseActions = true;

    /**
     * Counts hook calls across requests, which dispatched events cannot do, since only
     * the events of the latest request are asserted against.
     *
     * @var array<string, int>
     */
    public array $pauseHookCallCounts = [];

    /**
     * Not a Livewire property, so that it only lives for the request that sets it, the
     * way that a confirmation of the user's identity should.
     */
    protected bool $isPauseConfirmed = false;

    /**
     * Confirms the paused parent action, unmounts this nested action, and resumes the
     * parent, which is how a plugin would resume an action once the user has satisfied
     * whatever the pause was waiting for.
     */
    public function countPauseHookCall(string $hook): void
    {
        $this->pauseHookCallCounts[$hook] = ($this->pauseHookCallCounts[$hook] ?? 0) + 1;
    }

    public function confirmPause(): void
    {
        $this->isPauseConfirmed = true;

        array_pop($this->mountedActions);

        $this->callMountedAction();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('simple')
                ->action(function (): void {
                    $this->dispatch('simple-called');
                }),
            Action::make('cancelWithDatabaseTransaction')
                ->databaseTransaction()
                ->action(function (Action $action): void {
                    $action->cancel();
                }),
            Action::make('data')
                ->mountUsing(fn (Schema $form) => $form->fill(['foo' => 'bar']))
                ->schema([
                    TextInput::make('payload')->required(),
                ])
                ->action(function (array $data): void {
                    $this->dispatch('data-called', data: $data);
                }),
            Action::make('before-hook-data')
                ->schema([
                    TextInput::make('payload')->required(),
                ])
                ->before(function (Action $action): void {
                    $this->dispatch('before-hook-called', data: $action->getData());
                }),
            Action::make('arguments')
                ->requiresConfirmation()
                ->action(function (array $arguments): void {
                    $this->dispatch('arguments-called', arguments: $arguments);
                })
                ->extraModalFooterActions(fn (): array => [
                    Action::make('nested')
                        ->requiresConfirmation()
                        ->action(function (array $arguments): void {
                            $this->dispatch('nested-called', arguments: $arguments);
                        }),
                ]),
            Action::make('record-arguments')
                ->record(fn (array $arguments) => $arguments['key'])
                ->resolveRecordUsing(fn () => null)
                ->action(function (array $arguments): void {
                    $this->dispatch('record-arguments-called', arguments: $arguments);
                }),
            Action::make('parent')
                ->schema([
                    TextInput::make('foo')
                        ->required()
                        ->registerActions([
                            Action::make('nested')
                                ->schema([
                                    TextInput::make('bar')
                                        ->required(),
                                ])
                                ->action(fn () => null),
                            Action::make('cancelParent')
                                ->schema([
                                    TextInput::make('bar')
                                        ->required(),
                                ])
                                ->action(fn () => null)
                                ->cancelParentActions(),
                        ]),
                ])
                ->action(function (array $data): void {
                    $this->dispatch('parent-called', foo: $data['foo']);
                })
                ->extraModalFooterActions([
                    Action::make('footer')
                        ->schema([
                            TextInput::make('bar')
                                ->required(),
                        ])
                        ->action(fn () => null),
                ])
                ->registerModalActions([
                    Action::make('manuallyRegisteredModal')
                        ->schema([
                            TextInput::make('bar')
                                ->required(),
                        ])
                        ->registerModalActions([
                            Action::make('testData')
                                ->schema([
                                    TextInput::make('baz')
                                        ->required(),
                                ])
                                ->action(fn (array $mountedActions) => $this->dispatch(
                                    'data-test-called',
                                    foo: $mountedActions[0]->getRawFormData()['foo'],
                                    bar: $mountedActions[1]->getRawFormData()['bar'],
                                    baz: $mountedActions[2]->getRawFormData()['baz'],
                                )),
                            Action::make('testArguments')
                                ->action(function (array $mountedActions): void {
                                    $this->dispatch(
                                        'arguments-test-called',
                                        foo: $mountedActions[0]->getArguments()['foo'],
                                        bar: $mountedActions[1]->getArguments()['bar'],
                                        baz: $mountedActions[2]->getArguments()['baz'],
                                    );
                                }),
                        ])
                        ->action(fn () => null),
                ]),
            Action::make('grandparentWithModalCloseCancellation')
                ->schema([
                    TextInput::make('grandparentValue')
                        ->required()
                        ->registerActions([
                            Action::make('parentWithModalCloseCancellation')
                                ->schema([
                                    TextInput::make('parentValue')
                                        ->required()
                                        ->registerActions([
                                            Action::make('modalClosePreservesParentActions')
                                                ->requiresConfirmation()
                                                ->action(fn () => null),
                                            Action::make('modalCloseCancelsAllParentActions')
                                                ->requiresConfirmation()
                                                ->cancelParentActionsOnClose()
                                                ->action(fn () => null),
                                            Action::make('modalCloseCancelsToNamedParentAction')
                                                ->requiresConfirmation()
                                                ->cancelParentActionsOnClose('parentWithModalCloseCancellation')
                                                ->action(fn () => null),
                                        ]),
                                ])
                                ->action(fn () => null),
                        ]),
                ])
                ->action(fn () => null),
            Action::make('halt')
                ->requiresConfirmation()
                ->action(function (Action $action): void {
                    $this->dispatch('halt-called');

                    $action->halt();
                }),
            Action::make('withGroupedExtraActions')
                ->schema([
                    TextInput::make('content')
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $this->dispatch('grouped-extra-actions-called', content: $data['content']);
                })
                ->extraModalFooterActions([
                    Action::make('simpleExtra')
                        ->action(fn () => $this->dispatch('simple-extra-called')),
                    ActionGroup::make([
                        Action::make('option1')
                            ->action(fn () => $this->dispatch('option1-called')),
                        Action::make('option2')
                            ->action(fn () => $this->dispatch('option2-called')),
                        Action::make('option3')
                            ->schema([
                                TextInput::make('value')
                                    ->required(),
                            ])
                            ->action(fn (array $data) => $this->dispatch('option3-called', value: $data['value'])),
                    ])->button()->label('More Options'),
                ]),
            Action::make('visible'),
            Action::make('hidden')
                ->hidden(),
            Action::make('enabled'),
            Action::make('disabled')
                ->disabled(),
            Action::make('hasIcon')
                ->icon(Heroicon::PencilSquare),
            Action::make('hasLabel')
                ->label('My Action'),
            Action::make('hasColor')
                ->color('primary'),
            Action::make('exists'),
            Action::make('url')
                ->url('https://filamentphp.com'),
            Action::make('urlInNewTab')
                ->url('https://filamentphp.com', true),
            Action::make('urlNotInNewTab')
                ->url('https://filamentphp.com', false),
            Action::make('shows-notification')
                ->action(function (): void {
                    Notification::make()
                        ->title('A notification')
                        ->success()
                        ->send();
                }),
            Action::make('does-not-show-notification'),
            Action::make('shows-notification-with-id')
                ->action(function (): void {
                    Notification::make('notification_with_id')
                        ->title('A notification')
                        ->success()
                        ->send();
                }),
            Action::make('two-notifications')
                ->action(function (): void {
                    Notification::make('first_notification')
                        ->title('First notification')
                        ->success()
                        ->send();
                    Notification::make('second_notification')
                        ->title('Second notification')
                        ->success()
                        ->send();
                }),
            Action::make('rate-limited')
                ->rateLimit(5)
                ->action(fn () => $this->dispatch(
                    'rate-limited-called',
                )),
            Action::make('pause')
                ->requiresConfirmation()
                ->pauseWhen(fn (): bool => $this->shouldPauseActions)
                ->before(function (): void {
                    $this->countPauseHookCall('pause-before');
                    $this->dispatch('pause-before-called');
                })
                ->action(fn () => $this->dispatch('pause-called'))
                ->after(fn () => $this->dispatch('pause-after-called')),
            Action::make('pause-with-schema')
                ->schema([
                    TextInput::make('payload')
                        ->required(),
                ])
                ->beforeFormValidated(fn () => $this->countPauseHookCall('pause-with-schema-before-form-validated'))
                ->afterFormValidated(fn () => $this->countPauseHookCall('pause-with-schema-after-form-validated'))
                ->pauseWhen(fn (): bool => $this->shouldPauseActions)
                ->before(fn () => $this->countPauseHookCall('pause-with-schema-before'))
                ->action(fn (array $data) => $this->dispatch('pause-with-schema-called', data: $data)),
            Action::make('pause-from-before')
                ->requiresConfirmation()
                ->before(function (Action $action): void {
                    $this->countPauseHookCall('pause-from-before-before');

                    if (! $this->shouldPauseActions) {
                        return;
                    }

                    $action->pause();
                })
                ->action(fn () => $this->dispatch('pause-from-before-called')),
            Action::make('rate-limited-pause')
                ->rateLimit(2)
                ->pauseWhen(fn (): bool => $this->shouldPauseActions)
                ->action(fn () => $this->dispatch('rate-limited-pause-called')),
            Action::make('pause-until-filled')
                ->schema([
                    TextInput::make('title')
                        ->required(),
                    TextInput::make('reference'),
                ])
                ->pauseWhen(function (Action $action): bool {
                    if (filled($action->getData()['reference'] ?? null)) {
                        return false;
                    }

                    $action->getLivewire()->mountAction('generate-reference');

                    return true;
                })
                ->registerModalActions([
                    Action::make('generate-reference')
                        ->schema([
                            TextInput::make('prefix')
                                ->required(),
                        ])
                        ->action(function (Action $action, array $data): void {
                            $livewire = $action->getLivewire();

                            data_set($livewire->mountedActions, '0.data.reference', "{$data['prefix']}-123");

                            array_pop($livewire->mountedActions);

                            $livewire->callMountedAction();
                        }),
                ])
                ->action(fn (array $data) => $this->dispatch('pause-until-filled-called', data: $data)),
            Action::make('pause-until-confirmed')
                ->requiresConfirmation()
                ->pauseWhen(function (): bool {
                    if ($this->isPauseConfirmed) {
                        return false;
                    }

                    $this->mountAction('confirm-pause');

                    return true;
                })
                ->registerModalActions([
                    Action::make('confirm-pause')
                        ->schema([
                            TextInput::make('code')
                                ->required()
                                ->rule(static fn (): Closure => static function (string $attribute, mixed $value, Closure $fail): void {
                                    if ($value === '123456') {
                                        return;
                                    }

                                    $fail('The code is invalid.');
                                }),
                        ])
                        ->action(function (Actions $livewire): void {
                            $livewire->confirmPause();
                        }),
                ])
                ->action(fn () => $this->dispatch('pause-until-confirmed-called')),
            Action::make('predefined-arguments')
                ->arguments(['foo' => 'bar', 'baz' => 'qux'])
                ->label(fn (array $arguments): string => "Action for {$arguments['foo']}")
                ->requiresConfirmation()
                ->action(function (array $arguments): void {
                    $this->dispatch('predefined-arguments-called', arguments: $arguments);
                }),
            Action::make('replaces-action')
                ->action(function (): void {
                    $this->replaceMountedAction('replaced-action');
                }),
            Action::make('replaced-action')
                ->requiresConfirmation()
                ->action(function (): void {
                    $this->dispatch('replaced-action-called');
                }),
            Action::make('arguments-with-record-and-schema')
                ->schema(
                    fn (Post $record, Schema $schema) => $schema
                        ->record($record)
                        ->schema([
                            TextInput::make('foo'),
                        ])
                )
                ->record(fn (array $arguments) => Post::findOrFail($arguments['post_id'])),
            Action::make('enforcementHidden')
                ->hidden()
                ->action(function (): void {
                    $this->dispatch('enforcement-hidden-called');
                }),
            Action::make('enforcementInvisible')
                ->visible(false)
                ->action(function (): void {
                    $this->dispatch('enforcement-invisible-called');
                }),
            Action::make('enforcementDisabled')
                ->disabled()
                ->action(function (): void {
                    $this->dispatch('enforcement-disabled-called');
                }),
            Action::make('enforcementUnauthorized')
                ->authorize(false)
                ->action(function (): void {
                    $this->dispatch('enforcement-unauthorized-called');
                }),
            Action::make('enforcementAuthorized')
                ->authorize(true)
                ->action(function (): void {
                    $this->dispatch('enforcement-authorized-called');
                }),
        ];
    }
}
