<?php

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\Enums\ActionStatus;
use Filament\Actions\Testing\TestAction;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tests\Actions\TestCase;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Pages\Actions;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Str;

use function Filament\Tests\livewire;

uses(TestCase::class);

describe('calling actions', function (): void {
    it('can call an action', function (): void {
        livewire(Actions::class)
            ->callAction('simple')
            ->assertDispatched('simple-called');
    });

    it('can call an action with data', function (): void {
        livewire(Actions::class)
            ->callAction('data', data: [
                'payload' => $payload = Str::random(),
            ])
            ->assertHasNoFormErrors()
            ->assertDispatched('data-called', data: [
                'payload' => $payload,
            ]);
    });

    it('can call an action with arguments', function (): void {
        livewire(Actions::class)
            ->callAction('arguments', arguments: [
                'payload' => $payload = Str::random(),
            ])
            ->assertDispatched('arguments-called', arguments: [
                'payload' => $payload,
            ]);
    });

    it('can call an action and halt', function (): void {
        livewire(Actions::class)
            ->callAction('halt')
            ->assertDispatched('halt-called')
            ->assertActionHalted('halt');
    });

    it('can call an action that replaces itself with another action', function (): void {
        livewire(Actions::class)
            ->callAction('replaces-action')
            ->assertActionMounted('replaced-action')
            ->callMountedAction()
            ->assertDispatched('replaced-action-called');
    });

    it('can mount an action that replaces itself and then call the replaced action', function (): void {
        livewire(Actions::class)
            ->mountAction('replaces-action')
            ->assertActionMounted('replaced-action')
            ->callMountedAction()
            ->assertDispatched('replaced-action-called');
    });
});

describe('validation', function (): void {
    it('can validate an action\'s data', function (): void {
        livewire(Actions::class)
            ->callAction('data', data: [
                'payload' => null,
            ])
            ->assertHasFormErrors(['payload' => ['required']])
            ->assertNotDispatched('data-called');
    });

    it('can set default action data when mounted', function (): void {
        livewire(Actions::class)
            ->mountAction('data')
            ->assertSchemaStateSet([
                'foo' => 'bar',
            ]);
    });

    it('can access form data in `before` hook', function (): void {
        livewire(Actions::class)
            ->callAction('before-hook-data', data: [
                'payload' => $payload = Str::random(),
            ])
            ->assertHasNoFormErrors()
            ->assertDispatched('before-hook-called', data: [
                'payload' => $payload,
            ]);
    });
});

describe('arguments', function (): void {
    it('can mount an action with arguments', function (): void {
        livewire(Actions::class)
            ->mountAction('arguments', arguments: [
                'payload' => $payload = Str::random(),
            ])
            ->callMountedAction()
            ->assertDispatched('arguments-called', arguments: [
                'payload' => $payload,
            ]);
    });

    it('can mount an action record with arguments', function (): void {
        livewire(Actions::class)
            ->mountAction([
                TestAction::make('record-arguments')->arguments(['key' => 123]),
            ])
            ->callMountedAction()
            ->assertDispatched('record-arguments-called', arguments: [
                'key' => 123,
            ]);
    });

    it('preserves predefined arguments after calling an action with a modal', function (): void {
        livewire(Actions::class)
            ->assertActionHasLabel('predefined-arguments', 'Action for bar')
            ->callAction('predefined-arguments')
            ->assertDispatched('predefined-arguments-called', arguments: [
                'foo' => 'bar',
                'baz' => 'qux',
            ])
            ->assertActionHasLabel('predefined-arguments', 'Action for bar')
            ->callAction('predefined-arguments')
            ->assertDispatched('predefined-arguments-called', arguments: [
                'foo' => 'bar',
                'baz' => 'qux',
            ]);
    });

    it('restores original arguments after calling an action with call-time arguments', function (): void {
        livewire(Actions::class)
            ->assertActionHasLabel('predefined-arguments', 'Action for bar')
            ->mountAction('predefined-arguments')
            ->callMountedAction([
                'foo' => 'overridden',
                'extra' => 'call-time-value',
            ])
            ->assertDispatched('predefined-arguments-called', arguments: [
                'foo' => 'overridden',
                'baz' => 'qux',
                'extra' => 'call-time-value',
            ])
            ->assertActionHasLabel('predefined-arguments', 'Action for bar')
            ->callAction('predefined-arguments')
            ->assertDispatched('predefined-arguments-called', arguments: [
                'foo' => 'bar',
                'baz' => 'qux',
            ]);
    });

    it('can assert an action exists with arguments that are used to resolve a record for a schema', function (): void {
        $postId = Post::factory()->create()->getKey();

        livewire(Actions::class)
            ->assertActionExists('arguments-with-record-and-schema', arguments: [
                'post_id' => $postId,
            ])
            ->assertActionVisible('arguments-with-record-and-schema', arguments: [
                'post_id' => $postId,
            ]);
    });
});

describe('nested actions', function (): void {
    it('can call a nested action registered in the modal footer', function (): void {
        livewire(Actions::class)
            ->callAction([
                'parent',
                TestAction::make('footer'),
            ], [
                'bar' => Str::random(),
            ])
            ->assertHasNoFormErrors()
            ->fillForm([
                'foo' => $foo = Str::random(),
            ])
            ->callMountedAction()
            ->assertHasNoFormErrors()
            ->assertDispatched('parent-called', foo: $foo);
    });

    it('can call a manually registered modal nested action', function (): void {
        livewire(Actions::class)
            ->callAction([
                'parent',
                TestAction::make('manuallyRegisteredModal'),
            ], [
                'bar' => Str::random(),
            ])
            ->assertHasNoFormErrors()
            ->fillForm([
                'foo' => $foo = Str::random(),
            ])
            ->callMountedAction()
            ->assertHasNoFormErrors()
            ->assertDispatched('parent-called', foo: $foo);
    });

    it('can call a nested action registered on a schema component', function (): void {
        livewire(Actions::class)
            ->callAction([
                'parent',
                TestAction::make('nested')->schemaComponent('foo'),
            ], [
                'bar' => Str::random(),
            ])
            ->assertHasNoFormErrors()
            ->fillForm([
                'foo' => $foo = Str::random(),
            ])
            ->callMountedAction()
            ->assertHasNoFormErrors()
            ->assertDispatched('parent-called', foo: $foo);
    });

    it('can cancel a parent action when calling a nested action', function (): void {
        livewire(Actions::class)
            ->callAction([
                'parent',
                TestAction::make('cancelParent')->schemaComponent('foo'),
            ], [
                'bar' => Str::random(),
            ])
            ->assertHasNoFormErrors()
            ->assertActionNotMounted()
            ->assertNotDispatched('parent-called');
    });

    it('can mount a nested action with parent arguments', function (): void {
        livewire(Actions::class)
            ->mountAction([
                TestAction::make('arguments')->arguments(['payload' => Str::random()]),
                'nested',
            ])
            ->callMountedAction()
            ->assertDispatched('nested-called', arguments: []);

        livewire(Actions::class)
            ->mountAction('arguments.nested', arguments: [
                'arguments' => ['payload' => Str::random()],
            ])
            ->callMountedAction()
            ->assertDispatched('nested-called', arguments: []);
    });

    it('can mount a nested action with nested arguments', function (): void {
        livewire(Actions::class)
            ->mountAction([
                'arguments',
                TestAction::make('nested')->arguments(['payload' => $payload = Str::random()]),
            ])
            ->callMountedAction()
            ->assertDispatched('nested-called', arguments: [
                'payload' => $payload,
            ]);

        livewire(Actions::class)
            ->mountAction('arguments.nested', arguments: [
                'nested' => ['payload' => $payload = Str::random()],
            ])
            ->callMountedAction()
            ->assertDispatched('nested-called', arguments: [
                'payload' => $payload,
            ]);
    });

    it('can get the raw data from parent actions', function (): void {
        livewire(Actions::class)
            ->mountAction('parent')
            ->fillForm([
                'foo' => $foo = Str::random(),
            ])
            ->mountAction('manuallyRegisteredModal')
            ->fillForm([
                'bar' => $bar = Str::random(),
            ])
            ->callAction('testData', [
                'baz' => $baz = Str::random(),
            ])
            ->assertDispatched('data-test-called', foo: $foo, bar: $bar, baz: $baz);
    });

    it('can get the arguments from parent actions', function (): void {
        livewire(Actions::class)
            ->callAction([
                TestAction::make('parent')->arguments([
                    'foo' => $foo = Str::random(),
                ]),
                TestAction::make('manuallyRegisteredModal')->arguments([
                    'bar' => $bar = Str::random(),
                ]),
                TestAction::make('testArguments')->arguments([
                    'baz' => $baz = Str::random(),
                ]),
            ])
            ->assertDispatched('arguments-test-called', foo: $foo, bar: $bar, baz: $baz);
    });
});

describe('extra modal footer actions', function (): void {
    it('can mount an action that has a group in `extraModalFooterActions()`', function (): void {
        livewire(Actions::class)
            ->mountAction('withGroupedExtraActions')
            ->assertActionMounted('withGroupedExtraActions');
    });

    it('can call an action registered alongside a group in `extraModalFooterActions()`', function (): void {
        livewire(Actions::class)
            ->callAction([
                'withGroupedExtraActions',
                TestAction::make('simpleExtra'),
            ])
            ->assertDispatched('simple-extra-called');
    });

    it('can call an action with data registered in a group in `extraModalFooterActions()`', function (): void {
        livewire(Actions::class)
            ->callAction([
                'withGroupedExtraActions',
                TestAction::make('option3'),
            ], [
                'value' => $value = Str::random(),
            ])
            ->assertHasNoFormErrors()
            ->assertDispatched('option3-called', value: $value);
    });

    it('can call multiple actions registered in a group in `extraModalFooterActions()`', function (): void {
        livewire(Actions::class)
            ->callAction([
                'withGroupedExtraActions',
                TestAction::make('option1'),
            ])
            ->assertDispatched('option1-called');

        livewire(Actions::class)
            ->callAction([
                'withGroupedExtraActions',
                TestAction::make('option2'),
            ])
            ->assertDispatched('option2-called');
    });

    it('can submit parent action after calling an action registered in a group in `extraModalFooterActions()`', function (): void {
        livewire(Actions::class)
            ->callAction([
                'withGroupedExtraActions',
                TestAction::make('option1'),
            ])
            ->assertDispatched('option1-called')
            ->fillForm([
                'content' => $content = Str::random(),
            ])
            ->callMountedAction()
            ->assertHasNoActionErrors()
            ->assertDispatched('grouped-extra-actions-called', content: $content);
    });
});

describe('visibility and state', function (): void {
    it('can hide an action', function (): void {
        livewire(Actions::class)
            ->assertActionVisible('visible')
            ->assertActionHidden('hidden')
            ->assertActionExists('visible', fn (Action $action): bool => $action->isVisible())
            ->assertActionExists('hidden', fn (Action $action): bool => $action->isHidden())
            ->assertActionDoesNotExist('visible', fn (Action $action): bool => $action->isHidden())
            ->assertActionDoesNotExist('hidden', fn (Action $action): bool => $action->isVisible());
    });

    it('can disable an action', function (): void {
        livewire(Actions::class)
            ->assertActionEnabled('enabled')
            ->assertActionDisabled('disabled');
    });

    it('can state whether a page action exists', function (): void {
        livewire(Actions::class)
            ->assertActionExists('exists')
            ->assertActionDoesNotExist('doesNotExist');
    });
});

describe('properties', function (): void {
    it('can have an icon', function (): void {
        livewire(Actions::class)
            ->assertActionHasIcon('hasIcon', Heroicon::PencilSquare)
            ->assertActionDoesNotHaveIcon('hasIcon', Heroicon::Trash);
    });

    it('can have a label', function (): void {
        livewire(Actions::class)
            ->assertActionHasLabel('hasLabel', 'My Action')
            ->assertActionDoesNotHaveLabel('hasLabel', 'My Other Action');
    });

    it('can have a color', function (): void {
        livewire(Actions::class)
            ->assertActionHasColor('hasColor', 'primary')
            ->assertActionDoesNotHaveColor('hasColor', 'gray');
    });

    it('can have a URL', function (): void {
        livewire(Actions::class)
            ->assertActionHasUrl('url', 'https://filamentphp.com')
            ->assertActionDoesNotHaveUrl('url', 'https://google.com');
    });

    it('can open a URL in a new tab', function (): void {
        livewire(Actions::class)
            ->assertActionShouldOpenUrlInNewTab('urlInNewTab')
            ->assertActionShouldNotOpenUrlInNewTab('urlNotInNewTab');
    });

    it('can use `badge()` to set badge display mode', function (): void {
        $action = Action::make('test')->badge();

        expect($action->isBadge())->toBeTrue();
    });

    it('returns `false` for `isBadge()` by default', function (): void {
        $action = Action::make('test');

        expect($action->isBadge())->toBeFalse();
    });

    it('can use `button()` to set button display style', function (): void {
        $action = Action::make('test')->button();

        expect($action->isButton())->toBeTrue();
        expect($action->isIconButton())->toBeFalse();
        expect($action->isLink())->toBeFalse();
    });

    it('can use `iconButton()` to set icon button display style', function (): void {
        $action = Action::make('test')->iconButton();

        expect($action->isIconButton())->toBeTrue();
        expect($action->isButton())->toBeFalse();
        expect($action->isLink())->toBeFalse();
    });

    it('can use `link()` to set link display style', function (): void {
        $action = Action::make('test')->link();

        expect($action->isLink())->toBeTrue();
        expect($action->isButton())->toBeFalse();
        expect($action->isIconButton())->toBeFalse();
    });

    it('can set a tooltip via `tooltip()`', function (): void {
        $action = Action::make('test')
            ->tooltip('Help text');

        expect($action->getTooltip())->toBe('Help text');
    });

    it('returns `null` for `getTooltip()` by default', function (): void {
        $action = Action::make('test');

        expect($action->getTooltip())->toBeNull();
    });

    it('can set a size via `size()`', function (): void {
        $action = Action::make('test')
            ->size(Size::Large);

        expect($action->getSize())->toBe(Size::Large);
    });

    it('returns `null` for `getSize()` by default', function (): void {
        $action = Action::make('test');

        expect($action->getSize())->toBeNull();
    });

    it('can customise modal submit action label via `modalSubmitActionLabel()`', function (): void {
        $action = Action::make('test')
            ->modalSubmitActionLabel('Confirm');

        expect($action->getModalSubmitActionLabel())->toBe('Confirm');
    });

    it('can customise modal cancel action label via `modalCancelActionLabel()`', function (): void {
        $action = Action::make('test')
            ->modalCancelActionLabel('Dismiss');

        expect($action->getModalCancelActionLabel())->toBe('Dismiss');
    });

    it('can disable `closeModalByClickingAway()`', function (): void {
        $action = Action::make('test')
            ->closeModalByClickingAway(false);

        expect($action->isModalClosedByClickingAway())->toBeFalse();
    });

    it('can disable `closeModalByEscaping()`', function (): void {
        $action = Action::make('test')
            ->closeModalByEscaping(false);

        expect($action->isModalClosedByEscaping())->toBeFalse();
    });

    it('can set modal presentation properties via `modalIcon()`, `modalHeading()`, `modalDescription()`, and `modalWidth()`', function (): void {
        $action = Action::make('test')
            ->modalIcon(Heroicon::InformationCircle)
            ->modalHeading('Confirm Action')
            ->modalDescription('Are you sure you want to proceed?')
            ->modalWidth(Width::TwoExtraLarge);

        expect($action->getModalIcon())->toBe(Heroicon::InformationCircle);
        expect($action->getModalHeading())->toBe('Confirm Action');
        expect($action->getModalDescription())->toBe('Are you sure you want to proceed?');
        expect($action->getModalWidth())->toBe(Width::TwoExtraLarge);
    });
});

describe('notifications', function (): void {
    it('can show a notification', function (): void {
        livewire(Actions::class)
            ->callAction('shows-notification')
            ->assertNotified();

        livewire(Actions::class)
            ->callAction('shows-notification')
            ->assertNotified('A notification');

        livewire(Actions::class)
            ->callAction('shows-notification')
            ->assertNotified(
                Notification::make()
                    ->title('A notification')
                    ->success()
            );
    });

    it('can assert that a notification without an ID was sent', function (): void {
        livewire(Actions::class)
            ->callAction('shows-notification')
            ->assertNotified();

        livewire(Actions::class)
            ->callAction('shows-notification')
            ->assertNotified('A notification');

        livewire(Actions::class)
            ->callAction('shows-notification')
            ->assertNotified(
                Notification::make()
                    ->title('A notification')
                    ->success()
            );
    });

    it('can assert that a notification with an ID was sent', function (): void {
        livewire(Actions::class)
            ->callAction('shows-notification-with-id')
            ->assertNotified();

        livewire(Actions::class)
            ->callAction('shows-notification-with-id')
            ->assertNotified('A notification');

        livewire(Actions::class)
            ->callAction('shows-notification-with-id')
            ->assertNotified(
                Notification::make('notification_with_id')
                    ->title('A notification')
                    ->success()
            );
    });

    test('can assert that notifications are sent in any order', function (): void {
        livewire(Actions::class)
            ->callAction('two-notifications')
            ->assertNotified('Second notification');

        livewire(Actions::class)
            ->callAction('two-notifications')
            ->assertNotified('First notification');

        $this->expectException('PHPUnit\Framework\ExpectationFailedException');
        $this->expectExceptionMessage('A notification was not sent');

        livewire(Actions::class)
            ->callAction('two-notifications')
            ->assertNotified('Third notification');
    });

    it('will assert that a notification was not sent', function (): void {

        livewire(Actions::class)
            ->callAction('does-not-show-notification')
            ->assertNotNotified();

        livewire(Actions::class)
            ->callAction('shows-notification-with-id')
            ->assertNotNotified(
                Notification::make()
                    ->title('An incorrect notification')
                    ->success()
            );

        livewire(Actions::class)
            ->callAction('shows-notification-with-id')
            ->assertNotNotified('An incorrect notification');

        $this->expectException('PHPUnit\Framework\ExpectationFailedException');
        $this->expectExceptionMessage('The notification with the given configuration was sent');

        livewire(Actions::class)
            ->callAction('shows-notification-with-id')
            ->assertNotNotified(
                Notification::make()
                    ->title('A notification')
                    ->success()
            );

        $this->expectException('PHPUnit\Framework\ExpectationFailedException');
        $this->expectExceptionMessage('The notification with the given title was sent');

        livewire(Actions::class)
            ->callAction('shows-notification-with-id')
            ->assertNotNotified('A notification');
    });

    it('will raise an exception if a notification was not sent checking notification object', function (): void {
        $this->expectException('PHPUnit\Framework\ExpectationFailedException');
        $this->expectExceptionMessage('A notification was not sent');

        livewire(Actions::class)
            ->callAction('does-not-show-notification')
            ->assertNotified(
                Notification::make()
                    ->title('A notification')
                    ->success()
            );
    });

    it('will raise an exception if a notification was not sent checking notification title', function (): void {
        $this->expectException('PHPUnit\Framework\ExpectationFailedException');
        $this->expectExceptionMessage('A notification was not sent');

        livewire(Actions::class)
            ->callAction('does-not-show-notification')
            ->assertNotified('A notification');
    });

    it('will raise an exception if a notification was sent checking with a different notification title', function (): void {
        $this->expectException('PHPUnit\Framework\ExpectationFailedException');
        $this->expectExceptionMessage('Failed asserting that two arrays are identical.');

        livewire(Actions::class)
            ->callAction('shows-notification-with-id')
            ->assertNotified(
                Notification::make()
                    ->title('A different title')
                    ->success()
            );
    });

    it('will raise an exception if a notification is not sent but a previous notification was sent', function (): void {
        livewire(Actions::class)
            ->callAction('shows-notification-with-id')
            ->assertNotified(
                Notification::make()
                    ->title('A notification')
                    ->success()
            );

        $this->expectException('PHPUnit\Framework\ExpectationFailedException');
        $this->expectExceptionMessage('A notification was not sent');

        livewire(Actions::class)
            ->callAction('does-not-show-notification')
            ->assertNotified(
                Notification::make()
                    ->title('A notification')
                    ->success()
            );
    });
});

it('can set `alpineClickHandler()`', function (): void {
    $action = Action::make('test')
        ->alpineClickHandler('doSomething()');

    expect($action->getCustomAlpineClickHandler())->toBe('doSomething()');
});

it('can set `livewireTarget()`', function (): void {
    $action = Action::make('test')
        ->livewireTarget('someMethod');

    expect($action->getLivewireTarget())->toBe('someMethod');
});

it('can set `markAsRead()`', function (): void {
    $action = Action::make('test')
        ->markAsRead();

    expect($action->shouldMarkAsRead())->toBeTrue();
});

it('can set `markAsUnread()`', function (): void {
    $action = Action::make('test')
        ->markAsUnread();

    expect($action->shouldMarkAsUnread())->toBeTrue();
});

it('can set `grouped()` display style', function (): void {
    $action = Action::make('test')->grouped();

    expect($action->isButton())->toBeFalse();
    expect($action->isLink())->toBeFalse();
    expect($action->isIconButton())->toBeFalse();
});

it('can create an `ActionGroup` with `buttonGroup()` style', function (): void {
    $group = ActionGroup::make([
        Action::make('edit'),
        Action::make('delete'),
    ])->buttonGroup();

    expect($group->isButtonGroup())->toBeTrue();
    expect($group->isButton())->toBeFalse();
});

it('can get actions from `ActionGroup`', function (): void {
    $group = ActionGroup::make([
        Action::make('edit'),
        Action::make('delete'),
    ]);

    expect($group->getActions())->toHaveCount(2);
});

it('can set `triggerView()` on `ActionGroup`', function (): void {
    $group = ActionGroup::make([
        Action::make('edit'),
    ])->triggerView('custom-view');

    expect($group->getTriggerView())->toBe('custom-view');
});

it('can set `bulk()`', function (): void {
    $action = Action::make('delete');

    expect($action->isBulk())->toBeFalse();

    $action->bulk();

    expect($action->isBulk())->toBeTrue();

    $action->bulk(false);

    expect($action->isBulk())->toBeFalse();
});

it('can set `nestingIndex()`', function (): void {
    $action = Action::make('test');

    expect($action->getNestingIndex())->toBeNull();

    $action->nestingIndex(2);

    expect($action->getNestingIndex())->toBe(2);
});

it('can set `actionJs()` as alias for `alpineClickHandler()`', function (): void {
    $action = Action::make('test')
        ->actionJs('doSomething()');

    expect($action->getCustomAlpineClickHandler())->toBe('doSomething()');
});

it('tracks `success()` and `failure()` status', function (): void {
    $action = Action::make('test');

    $action->success();

    expect($action->getStatus())->toBe(ActionStatus::Success);

    $action->failure();

    expect($action->getStatus())->toBe(ActionStatus::Failure);
});

it('can serialize to array via `toArray()` and restore via `fromArray()`', function (): void {
    $action = Action::make('save')
        ->label('Save Record')
        ->color('primary')
        ->icon(Heroicon::OutlinedCheck)
        ->size(Size::Large)
        ->tooltip('Click to save')
        ->markAsRead()
        ->disabled();

    $array = $action->toArray();

    expect($array['name'])->toBe('save');
    expect($array['label'])->toBe('Save Record');
    expect($array['color'])->toBe('primary');
    expect($array['size'])->toBe(Size::Large);
    expect($array['tooltip'])->toBe('Click to save');
    expect($array['shouldMarkAsRead'])->toBeTrue();
    expect($array['isDisabled'])->toBeTrue();

    $restored = Action::fromArray($array);

    expect($restored->getName())->toBe('save');
    expect($restored->getLabel())->toBe('Save Record');
    expect($restored->getColor())->toBe('primary');
    expect($restored->getTooltip())->toBe('Click to save');
    expect($restored->shouldMarkAsRead())->toBeTrue();
    expect($restored->isDisabled())->toBeTrue();
});

it('can use `badge()` with a value to set badge content', function (): void {
    $action = Action::make('notifications')
        ->badge(5);

    expect($action->getBadge())->toBe('5');
    expect($action->isBadge())->toBeFalse();
});

it('can use `badge()` without arguments to set badge view', function (): void {
    $action = Action::make('tag')
        ->badge();

    expect($action->isBadge())->toBeTrue();
});

it('returns `close()` from `getAlpineClickHandler()` when `shouldClose()` is `true`', function (): void {
    $action = Action::make('dismiss')
        ->close();

    expect($action->getAlpineClickHandler())->toBe('close()');
});

it('returns `markAsRead()` from `getAlpineClickHandler()` when `shouldMarkAsRead()` is `true`', function (): void {
    $action = Action::make('read')
        ->markAsRead();

    expect($action->getAlpineClickHandler())->toBe('markAsRead()');
});

it('returns `markAsUnread()` from `getAlpineClickHandler()` when `shouldMarkAsUnread()` is `true`', function (): void {
    $action = Action::make('unread')
        ->markAsUnread();

    expect($action->getAlpineClickHandler())->toBe('markAsUnread()');
});

it('can set and get `parentAction()`', function (): void {
    $parent = Action::make('parent');
    $child = Action::make('child')
        ->parentAction($parent);

    expect($child->getParentAction())->toBe($parent);
    expect($child->getParentAction()->getName())->toBe('parent');
});

it('returns `null` for `getAlpineClickHandler()` by default', function (): void {
    $action = Action::make('test');

    expect($action->getAlpineClickHandler())->toBeNull();
});

it('returns `false` for `shouldClearRecordAfter()` by default', function (): void {
    $action = Action::make('test');

    expect($action->shouldClearRecordAfter())->toBeFalse();
});

it('returns `callMountedAction` for `getLivewireCallMountedActionName()`', function (): void {
    $action = Action::make('test');

    expect($action->getLivewireCallMountedActionName())->toBe('callMountedAction');
});

it('can use deprecated `hold()` as alias for `halt()`', function (): void {
    $action = Action::make('test');

    // `hold()` should not throw (it calls `halt()` internally)
    expect($action)->toBeInstanceOf(Action::class);
});

it('can set `keyBindings()` with a string', function (): void {
    $action = Action::make('save')
        ->keyBindings('mod+s');

    expect($action->getKeyBindings())->toBe(['mod+s']);
});

it('can set `keyBindings()` with an array', function (): void {
    $action = Action::make('save')
        ->keyBindings(['mod+s', 'mod+enter']);

    expect($action->getKeyBindings())->toBe(['mod+s', 'mod+enter']);
});

it('returns `null` from `getKeyBindings()` when not set', function (): void {
    $action = Action::make('test');

    expect($action->getKeyBindings())->toBeNull();
});

it('can set `keyBindings()` with a `Closure`', function (): void {
    $action = Action::make('save')
        ->keyBindings(static fn (): array => ['mod+s']);

    expect($action->getKeyBindings())->toBe(['mod+s']);
});

it('can set `requiresConfirmation()`', function (): void {
    $action = Action::make('delete');

    expect($action->isConfirmationRequired())->toBeFalse();

    $action->requiresConfirmation();

    expect($action->isConfirmationRequired())->toBeTrue();
});

it('can set `requiresConfirmation()` with a `Closure`', function (): void {
    $action = Action::make('delete')
        ->requiresConfirmation(static fn (): bool => true);

    expect($action->isConfirmationRequired())->toBeTrue();
});

it('can set `outlined()`', function (): void {
    $action = Action::make('test');

    expect($action->isOutlined())->toBeFalse();

    $action->outlined();

    expect($action->isOutlined())->toBeTrue();
});

it('can set `disabled()`', function (): void {
    $action = Action::make('test');

    expect($action->isDisabled())->toBeFalse();

    $action->disabled();

    expect($action->isDisabled())->toBeTrue();
});

it('can set `close()` and check `shouldClose()`', function (): void {
    $action = Action::make('dismiss');

    expect($action->shouldClose())->toBeFalse();

    $action->close();

    expect($action->shouldClose())->toBeTrue();
});

it('can set `close()` with a `Closure`', function (): void {
    $action = Action::make('dismiss')
        ->close(static fn (): bool => true);

    expect($action->shouldClose())->toBeTrue();
});

it('can set `sort()` and check `getSort()`', function (): void {
    $action = Action::make('test');

    expect($action->getSort())->toBe(0);

    $action->sort(5);

    expect($action->getSort())->toBe(5);
});

it('can set `sort()` with a `Closure`', function (): void {
    $action = Action::make('test')
        ->sort(static fn (): int => 10);

    expect($action->getSort())->toBe(10);
});

it('can set `deselectRecordsAfterCompletion()`', function (): void {
    $action = Action::make('bulk-delete');

    expect($action->shouldDeselectRecordsAfterCompletion())->toBeFalse();

    $action->deselectRecordsAfterCompletion();

    expect($action->shouldDeselectRecordsAfterCompletion())->toBeTrue();
});

it('can set `deselectRecordsAfterCompletion()` with a `Closure`', function (): void {
    $action = Action::make('bulk-delete')
        ->deselectRecordsAfterCompletion(static fn (): bool => true);

    expect($action->shouldDeselectRecordsAfterCompletion())->toBeTrue();
});

it('can set `groupedIcon()` and get with `getGroupedIcon()`', function (): void {
    $action = Action::make('test');

    expect($action->getGroupedIcon())->toBeNull();

    $action->groupedIcon('heroicon-o-cog');

    expect($action->getGroupedIcon())->toBe('heroicon-o-cog');
});

it('can set `tableIcon()` and get with `getTableIcon()`', function (): void {
    $action = Action::make('test');

    expect($action->getTableIcon())->toBeNull();

    $action->tableIcon('heroicon-o-pencil');

    expect($action->getTableIcon())->toBe('heroicon-o-pencil');
});

it('returns fluent `$this` from `before()`', function (): void {
    $action = Action::make('test');

    $result = $action->before(static fn () => null);

    expect($result)->toBe($action);
});

it('returns fluent `$this` from `after()`', function (): void {
    $action = Action::make('test');

    $result = $action->after(static fn () => null);

    expect($result)->toBe($action);
});

it('returns fluent `$this` from `beforeFormFilled()`', function (): void {
    $action = Action::make('test');

    $result = $action->beforeFormFilled(static fn () => null);

    expect($result)->toBe($action);
});

it('returns fluent `$this` from `afterFormValidated()`', function (): void {
    $action = Action::make('test');

    $result = $action->afterFormValidated(static fn () => null);

    expect($result)->toBe($action);
});

describe('ActionGroup trigger views', function (): void {
    it('can set `button()` trigger view on `ActionGroup`', function (): void {
        $group = ActionGroup::make([Action::make('edit')])
            ->button();

        expect($group->isButton())->toBeTrue();
        expect($group->isIconButton())->toBeFalse();
        expect($group->isLink())->toBeFalse();
        expect($group->isButtonGroup())->toBeFalse();
    });

    it('can set `iconButton()` trigger view on `ActionGroup`', function (): void {
        $group = ActionGroup::make([Action::make('edit')])
            ->iconButton();

        expect($group->isIconButton())->toBeTrue();
        expect($group->isButton())->toBeFalse();
    });

    it('can set `link()` trigger view on `ActionGroup`', function (): void {
        $group = ActionGroup::make([Action::make('edit')])
            ->link();

        expect($group->isLink())->toBeTrue();
        expect($group->isButton())->toBeFalse();
    });

    it('can set `grouped()` trigger view on `ActionGroup`', function (): void {
        $group = ActionGroup::make([Action::make('edit')])
            ->grouped();

        expect($group->getTriggerView())->toBe(ActionGroup::GROUPED_VIEW);
    });

    it('defaults to `iconButton` trigger view on `ActionGroup`', function (): void {
        $group = ActionGroup::make([Action::make('edit')]);

        expect($group->isIconButton())->toBeTrue();
    });

    it('can set `badge()` trigger view on `ActionGroup`', function (): void {
        $group = ActionGroup::make([Action::make('edit')])
            ->badge();

        expect($group->isBadge())->toBeTrue();
    });

    it('returns `false` for `isBadge()` by default on `ActionGroup`', function (): void {
        $group = ActionGroup::make([Action::make('edit')]);

        expect($group->isBadge())->toBeFalse();
    });

    it('can set `badge()` with a value on `ActionGroup`', function (): void {
        $group = ActionGroup::make([Action::make('edit')])
            ->badge(3);

        expect($group->getBadge())->toBe('3');
        expect($group->isBadge())->toBeFalse();
    });
});

describe('ActionGroup actions management', function (): void {
    it('can get `getFlatActions()` from `ActionGroup`', function (): void {
        $group = ActionGroup::make([
            Action::make('edit'),
            Action::make('delete'),
        ]);

        $flat = $group->getFlatActions();

        expect($flat)->toHaveCount(2);
        expect($flat)->toHaveKeys(['edit', 'delete']);
    });

    it('flattens nested `ActionGroup` actions into `getFlatActions()`', function (): void {
        $group = ActionGroup::make([
            Action::make('view'),
            ActionGroup::make([
                Action::make('edit'),
                Action::make('delete'),
            ]),
        ]);

        $flat = $group->getFlatActions();

        expect($flat)->toHaveCount(3);
        expect($flat)->toHaveKeys(['view', 'edit', 'delete']);
    });

    it('detects non-bulk actions via `hasNonBulkAction()` on `ActionGroup`', function (): void {
        $group = ActionGroup::make([
            Action::make('delete')->bulk(),
            Action::make('export')->bulk(),
        ]);

        expect($group->hasNonBulkAction())->toBeFalse();

        $mixed = ActionGroup::make([
            Action::make('delete')->bulk(),
            Action::make('edit'),
        ]);

        expect($mixed->hasNonBulkAction())->toBeTrue();
    });

    it('can `getClone()` an `ActionGroup` independently', function (): void {
        $original = ActionGroup::make([
            Action::make('edit'),
            Action::make('delete'),
        ]);

        $clone = $original->getClone();

        expect($clone->getFlatActions())->toHaveCount(2);
        expect($clone->getFlatActions()['edit'])->not->toBe($original->getFlatActions()['edit']);
        expect($clone->getFlatActions()['edit']->getName())->toBe('edit');
    });
});

describe('ActionGroup properties', function (): void {
    it('has a default label on `ActionGroup`', function (): void {
        $group = ActionGroup::make([Action::make('edit')]);

        expect($group->getLabel())->toBeString();
        expect($group->getLabel())->not->toBeEmpty();
    });

    it('can set custom label on `ActionGroup`', function (): void {
        $group = ActionGroup::make([Action::make('edit')])
            ->label('More Actions');

        expect($group->getLabel())->toBe('More Actions');
    });

    it('has a default icon on `ActionGroup`', function (): void {
        $group = ActionGroup::make([Action::make('edit')]);

        expect($group->getIcon())->not->toBeNull();
    });

    it('can set `extraDropdownAttributes()` on `ActionGroup`', function (): void {
        $group = ActionGroup::make([Action::make('edit')])
            ->extraDropdownAttributes(['data-test' => 'dropdown']);

        $attributes = $group->getExtraDropdownAttributes();

        expect($attributes)->toHaveKey('data-test');
        expect($attributes['data-test'])->toBe('dropdown');
    });

    it('can merge `extraDropdownAttributes()` on `ActionGroup`', function (): void {
        $group = ActionGroup::make([Action::make('edit')])
            ->extraDropdownAttributes(['data-first' => 'a'])
            ->extraDropdownAttributes(['data-second' => 'b'], merge: true);

        $attributes = $group->getExtraDropdownAttributes();

        expect($attributes)->toHaveKey('data-first');
        expect($attributes)->toHaveKey('data-second');
    });

    it('can serialize `ActionGroup` via `toArray()` and restore via `fromArray()`', function (): void {
        $group = ActionGroup::make([
            Action::make('edit')->label('Edit'),
            Action::make('delete')->label('Delete'),
        ])
            ->label('Actions')
            ->color('gray')
            ->tooltip('Click for options');

        $array = $group->toArray();

        expect($array['label'])->toBe('Actions');
        expect($array['color'])->toBe('gray');
        expect($array['tooltip'])->toBe('Click for options');
        expect($array['actions'])->toHaveCount(2);

        $restored = ActionGroup::fromArray($array);

        expect($restored->getLabel())->toBe('Actions');
        expect($restored->getColor())->toBe('gray');
        expect($restored->getTooltip())->toBe('Click for options');
        expect($restored->getFlatActions())->toHaveCount(2);
    });
});

describe('ActionGroup dropdown properties', function (): void {
    it('defaults `hasDropdown()` to `true`', function (): void {
        $group = ActionGroup::make([Action::make('edit')]);

        expect($group->hasDropdown())->toBeTrue();
    });

    it('can disable dropdown via `dropdown(false)`', function (): void {
        $group = ActionGroup::make([Action::make('edit')])
            ->dropdown(false);

        expect($group->hasDropdown())->toBeFalse();
    });

    it('can set `dropdownPlacement()`', function (): void {
        $group = ActionGroup::make([Action::make('edit')]);

        expect($group->getDropdownPlacement())->toBeNull();

        $group->dropdownPlacement('bottom-end');

        expect($group->getDropdownPlacement())->toBe('bottom-end');
    });

    it('can set `dropdownMaxHeight()`', function (): void {
        $group = ActionGroup::make([Action::make('edit')]);

        expect($group->getDropdownMaxHeight())->toBeNull();

        $group->dropdownMaxHeight('300px');

        expect($group->getDropdownMaxHeight())->toBe('300px');
    });

    it('can set `dropdownOffset()`', function (): void {
        $group = ActionGroup::make([Action::make('edit')]);

        expect($group->getDropdownOffset())->toBeNull();

        $group->dropdownOffset(12);

        expect($group->getDropdownOffset())->toBe(12);
    });

    it('can set `dropdownWidth()` with a `Width` enum', function (): void {
        $group = ActionGroup::make([Action::make('edit')])
            ->dropdownWidth(Width::Large);

        expect($group->getDropdownWidth())->toBe(Width::Large);
    });

    it('can set `dropdownWidth()` with a string that maps to enum', function (): void {
        $group = ActionGroup::make([Action::make('edit')])
            ->dropdownWidth('lg');

        expect($group->getDropdownWidth())->toBe(Width::Large);
    });

    it('defaults `hasDropdownFlip()` to `true`', function (): void {
        $group = ActionGroup::make([Action::make('edit')]);

        expect($group->hasDropdownFlip())->toBeTrue();
    });

    it('can disable `dropdownFlip()`', function (): void {
        $group = ActionGroup::make([Action::make('edit')])
            ->dropdownFlip(false);

        expect($group->hasDropdownFlip())->toBeFalse();
    });
});

describe('ActionGroup visibility', function (): void {
    it('`isHidden()` returns `false` when group has visible actions', function (): void {
        $group = ActionGroup::make([
            Action::make('edit'),
            Action::make('delete'),
        ]);

        expect($group->isHidden())->toBeFalse();
    });

    it('`isHidden()` returns `true` when group has no actions', function (): void {
        $group = ActionGroup::make([]);

        expect($group->isHidden())->toBeTrue();
    });

    it('`isHidden()` returns `true` when explicitly hidden', function (): void {
        $group = ActionGroup::make([Action::make('edit')])
            ->hidden();

        expect($group->isHidden())->toBeTrue();
    });
});

describe('ActionGroup label traits', function (): void {
    it('can set `labeledFrom()` breakpoint on `ActionGroup`', function (): void {
        $group = ActionGroup::make([Action::make('edit')]);

        expect($group->getLabeledFromBreakpoint())->toBeNull();

        $group->labeledFrom('md');

        expect($group->getLabeledFromBreakpoint())->toBe('md');
    });

    it('can set `hiddenLabel()` on `ActionGroup`', function (): void {
        $group = ActionGroup::make([Action::make('edit')]);

        expect($group->isLabelHidden())->toBeFalse();

        $group->hiddenLabel();

        expect($group->isLabelHidden())->toBeTrue();
    });

    it('can set `outlined()` on `ActionGroup`', function (): void {
        $group = ActionGroup::make([Action::make('edit')]);

        expect($group->isOutlined())->toBeFalse();

        $group->outlined();

        expect($group->isOutlined())->toBeTrue();
    });
});

describe('construction and properties', function (): void {
    it('returns `null` from `getDefaultName()`', function (): void {
        expect(Action::getDefaultName())->toBeNull();
    });

    it('defaults `shouldMarkAsRead()` to `false`', function (): void {
        $action = Action::make('test');

        expect($action->shouldMarkAsRead())->toBeFalse();
    });

    it('can set `markAsRead()` with a `Closure`', function (): void {
        $action = Action::make('test')
            ->markAsRead(static fn (): bool => true);

        expect($action->shouldMarkAsRead())->toBeTrue();
    });

    it('defaults `shouldMarkAsUnread()` to `false`', function (): void {
        $action = Action::make('test');

        expect($action->shouldMarkAsUnread())->toBeFalse();
    });

    it('can set `markAsUnread()` with a `Closure`', function (): void {
        $action = Action::make('test')
            ->markAsUnread(static fn (): bool => true);

        expect($action->shouldMarkAsUnread())->toBeTrue();
    });

    it('defaults `isBulk()` to `false`', function (): void {
        $action = Action::make('test');

        expect($action->isBulk())->toBeFalse();
    });

    it('can set `bulk()` with a `Closure`', function (): void {
        $action = Action::make('test')
            ->bulk(static fn (): bool => true);

        expect($action->isBulk())->toBeTrue();
    });

    it('defaults `getNestingIndex()` to `null`', function (): void {
        $action = Action::make('test');

        expect($action->getNestingIndex())->toBeNull();
    });

    it('can set `nestingIndex()`', function (): void {
        $action = Action::make('test')
            ->nestingIndex(3);

        expect($action->getNestingIndex())->toBe(3);
    });

    it('defaults `getParentAction()` to `null`', function (): void {
        $action = Action::make('test');

        expect($action->getParentAction())->toBeNull();
    });

    it('can set `parentAction()`', function (): void {
        $parent = Action::make('parent');
        $child = Action::make('child')
            ->parentAction($parent);

        expect($child->getParentAction())->toBe($parent);
    });

    it('defaults `getCustomAlpineClickHandler()` to `null`', function (): void {
        $action = Action::make('test');

        expect($action->getCustomAlpineClickHandler())->toBeNull();
    });

    it('can set `alpineClickHandler()`', function (): void {
        $action = Action::make('test')
            ->alpineClickHandler('doSomething()');

        expect($action->getCustomAlpineClickHandler())->toBe('doSomething()');
    });

    it('can set `alpineClickHandler()` with a `Closure`', function (): void {
        $action = Action::make('test')
            ->alpineClickHandler(static fn (): string => 'dynamic()');

        expect($action->getCustomAlpineClickHandler())->toBe('dynamic()');
    });

    it('defaults `getLivewireTarget()` to `null`', function (): void {
        $action = Action::make('test');

        expect($action->getLivewireTarget())->toBeNull();
    });

    it('can set `livewireTarget()`', function (): void {
        $action = Action::make('test')
            ->livewireTarget('save');

        expect($action->getLivewireTarget())->toBe('save');
    });

    it('defaults `getStatus()` to `Success` when not accessing selected records', function (): void {
        $action = Action::make('test');

        expect($action->getStatus())->toBe(ActionStatus::Success);
    });

    it('can set `actionJs()`', function (): void {
        $action = Action::make('test')
            ->actionJs('alert("hi")');

        // actionJs stores the string for client-side action
        expect($action)->toBeInstanceOf(Action::class);
    });

    it('can set `actionJs()` with a `Closure`', function (): void {
        $action = Action::make('test')
            ->actionJs(static fn (): string => 'alert("dynamic")');

        expect($action)->toBeInstanceOf(Action::class);
    });

    it('can convert to array with `toArray()`', function (): void {
        $action = Action::make('test')
            ->label('Test Action')
            ->color('primary')
            ->icon(Heroicon::Check);

        $array = $action->toArray();

        expect($array)->toBeArray();
        expect($array['name'])->toBe('test');
        expect($array['label'])->toBe('Test Action');
        expect($array['color'])->toBe('primary');
    });

    it('can create from array with `fromArray()`', function (): void {
        $original = Action::make('test')
            ->label('Test')
            ->color('danger');

        $array = $original->toArray();
        $restored = Action::fromArray($array);

        expect($restored->getName())->toBe('test');
        expect($restored->getLabel())->toBe('Test');
        expect($restored->getColor())->toBe('danger');
    });

    it('returns view constant names', function (): void {
        expect(Action::BUTTON_VIEW)->toBeString()->not->toBeEmpty();
        expect(Action::ICON_BUTTON_VIEW)->toBeString()->not->toBeEmpty();
        expect(Action::LINK_VIEW)->toBeString()->not->toBeEmpty();
        expect(Action::GROUPED_VIEW)->toBeString()->not->toBeEmpty();
        expect(Action::BADGE_VIEW)->toBeString()->not->toBeEmpty();
    });

    it('can set `withAttributes()`', function (): void {
        $action = Action::make('test')
            ->withAttributes(['data-test' => 'value']);

        expect($action)->toBeInstanceOf(Action::class);
    });
});

describe('authorization', function (): void {
    it('is visible by default when no `authorize()` is configured', function (): void {
        $action = Action::make('test');

        expect($action->isAuthorizedOrNotHiddenWhenUnauthorized())->toBeTrue();
        expect($action->isVisible())->toBeTrue();
    });

    it('is hidden when `authorize()` returns `false` and no auth feedback method is set', function (): void {
        $action = Action::make('test')
            ->authorize(fn (): bool => false);

        expect($action->isAuthorizedOrNotHiddenWhenUnauthorized())->toBeFalse();
        expect($action->isVisible())->toBeFalse();
    });

    it('accepts an `authorize()` closure returning a `Response`', function (): void {
        $action = Action::make('test')
            ->authorize(fn (): Response => Response::deny('Nope.'));

        expect($action->getAuthorizationResponse()->message())->toBe('Nope.');
    });

    it('can chain `authorizationMessage()` to set a fallback message', function (): void {
        $action = Action::make('test')
            ->authorizationMessage('Custom fallback.');

        expect($action->getAuthorizationMessage())->toBe('Custom fallback.');
    });

    describe('with `authorizationNotification()`', function (): void {
        it('shows the action when the user is allowed', function (): void {
            $action = Action::make('test')
                ->authorize(fn (): Response => Response::allow())
                ->authorizationNotification();

            expect($action->isAuthorizedOrNotHiddenWhenUnauthorized())->toBeTrue();
            expect($action->isVisible())->toBeTrue();
        });

        it('shows the action when denied with a message', function (): void {
            $action = Action::make('test')
                ->authorize(fn (): Response => Response::deny('You cannot do that.'))
                ->authorizationNotification();

            expect($action->isAuthorizedOrNotHiddenWhenUnauthorized())->toBeTrue();
            expect($action->isVisible())->toBeTrue();
            expect($action->hasAuthorizationNotification())->toBeTrue();
            expect($action->getAuthorizationResponseWithMessage()->message())->toBe('You cannot do that.');
        });

        it('hides the action when denied with `Response::deny()` and no message', function (): void {
            $action = Action::make('test')
                ->authorize(fn (): Response => Response::deny())
                ->authorizationNotification();

            expect($action->isAuthorizedOrNotHiddenWhenUnauthorized())->toBeFalse();
            expect($action->isVisible())->toBeFalse();
        });

        it('hides the action when the policy returns bare `false`', function (): void {
            $action = Action::make('test')
                ->authorize(fn (): bool => false)
                ->authorizationNotification();

            expect($action->isAuthorizedOrNotHiddenWhenUnauthorized())->toBeFalse();
            expect($action->isVisible())->toBeFalse();
        });

        it('shows the action when `authorizationMessage()` is set even if the policy returns bare `false`', function (): void {
            $action = Action::make('test')
                ->authorize(fn (): bool => false)
                ->authorizationMessage('Explicit message.')
                ->authorizationNotification();

            expect($action->isAuthorizedOrNotHiddenWhenUnauthorized())->toBeTrue();
            expect($action->isVisible())->toBeTrue();
            expect($action->getAuthorizationResponseWithMessage()->message())->toBe('Explicit message.');
        });

        it('is a no-op when `condition: false` is passed', function (): void {
            $action = Action::make('test')
                ->authorize(fn (): bool => false)
                ->authorizationNotification(false);

            expect($action->hasAuthorizationNotification())->toBeFalse();
            expect($action->isAuthorizedOrNotHiddenWhenUnauthorized())->toBeFalse();
            expect($action->isVisible())->toBeFalse();
        });

        it('stays hidden when `visible(false)` is also set', function (): void {
            $action = Action::make('test')
                ->authorize(fn (): Response => Response::deny('Has a message.'))
                ->authorizationNotification()
                ->visible(false);

            expect($action->isVisible())->toBeFalse();
        });
    });

    describe('with `authorizationTooltip()`', function (): void {
        it('shows the action when the user is allowed', function (): void {
            $action = Action::make('test')
                ->authorize(fn (): Response => Response::allow())
                ->authorizationTooltip();

            expect($action->isAuthorizedOrNotHiddenWhenUnauthorized())->toBeTrue();
            expect($action->isVisible())->toBeTrue();
        });

        it('shows the action with the deny message as a tooltip when denied with a message', function (): void {
            $action = Action::make('test')
                ->authorize(fn (): Response => Response::deny('You cannot do that.'))
                ->authorizationTooltip();

            expect($action->isAuthorizedOrNotHiddenWhenUnauthorized())->toBeTrue();
            expect($action->isVisible())->toBeTrue();
            expect($action->hasAuthorizationTooltip())->toBeTrue();
            expect($action->getTooltip())->toBe('You cannot do that.');
        });

        it('hides the action when denied with `Response::deny()` and no message', function (): void {
            $action = Action::make('test')
                ->authorize(fn (): Response => Response::deny())
                ->authorizationTooltip();

            expect($action->isAuthorizedOrNotHiddenWhenUnauthorized())->toBeFalse();
            expect($action->isVisible())->toBeFalse();
        });

        it('hides the action when the policy returns bare `false`', function (): void {
            $action = Action::make('test')
                ->authorize(fn (): bool => false)
                ->authorizationTooltip();

            expect($action->isAuthorizedOrNotHiddenWhenUnauthorized())->toBeFalse();
            expect($action->isVisible())->toBeFalse();
        });

        it('shows the action when `authorizationMessage()` is set even if the policy returns bare `false`', function (): void {
            $action = Action::make('test')
                ->authorize(fn (): bool => false)
                ->authorizationMessage('Explicit message.')
                ->authorizationTooltip();

            expect($action->isAuthorizedOrNotHiddenWhenUnauthorized())->toBeTrue();
            expect($action->isVisible())->toBeTrue();
            expect($action->getTooltip())->toBe('Explicit message.');
        });

        it('is a no-op when `condition: false` is passed', function (): void {
            $action = Action::make('test')
                ->authorize(fn (): bool => false)
                ->authorizationTooltip(false);

            expect($action->hasAuthorizationTooltip())->toBeFalse();
            expect($action->isAuthorizedOrNotHiddenWhenUnauthorized())->toBeFalse();
            expect($action->isVisible())->toBeFalse();
        });

        it('stays hidden when `visible(false)` is also set', function (): void {
            $action = Action::make('test')
                ->authorize(fn (): Response => Response::deny('Has a message.'))
                ->authorizationTooltip()
                ->visible(false);

            expect($action->isVisible())->toBeFalse();
        });
    });
});
