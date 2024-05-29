<?php

use Filament\Notifications\Notification;
use Filament\Tests\Actions\Fixtures\Pages\Actions;
use Filament\Tests\Actions\TestCase;
use Illuminate\Support\Str;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can call an action', function () {
    livewire(Actions::class)
        ->callAction('simple')
        ->assertDispatched('simple-called');
});

it('can call an action with data', function () {
    livewire(Actions::class)
        ->callAction('data', data: [
            'payload' => $payload = Str::random(),
        ])
        ->assertHasNoActionErrors()
        ->assertDispatched('data-called', data: [
            'payload' => $payload,
        ]);
});

it('can validate an action\'s data', function () {
    livewire(Actions::class)
        ->callAction('data', data: [
            'payload' => null,
        ])
        ->assertHasActionErrors(['payload' => ['required']])
        ->assertNotDispatched('data-called');
});

it('can access form data in before hook', function () {
    livewire(Actions::class)
        ->callAction('before-hook-data', data: [
            'payload' => $payload = Str::random(),
        ])
        ->assertHasNoActionErrors()
        ->assertDispatched('before-hook-called', data: [
            'payload' => $payload,
        ]);
});

it('can set default action data when mounted', function () {
    livewire(Actions::class)
        ->mountAction('data')
        ->assertActionDataSet([
            'foo' => 'bar',
        ]);
});

it('can mount an action with arguments', function () {
    livewire(Actions::class)
        ->mountAction('arguments', arguments: [
            'payload' => $payload = Str::random(),
        ])
        ->callMountedAction()
        ->assertDispatched('arguments-called', arguments: [
            'payload' => $payload,
        ]);
});

it('can mount a nested action with parent arguments', function () {
    livewire(Actions::class)
        ->mountAction('arguments.nested', arguments: [
            'arguments' => ['payload' => Str::random()],
        ])
        ->callMountedAction()
        ->assertDispatched('nested-called', arguments: []);
});

it('can mount a nested action with nested arguments', function () {
    livewire(Actions::class)
        ->mountAction('arguments.nested', arguments: [
            'nested' => ['payload' => $payload = Str::random()],
        ])
        ->callMountedAction()
        ->assertDispatched('nested-called', arguments: [
            'payload' => $payload,
        ]);
});

it('can call an action with arguments', function () {
    livewire(Actions::class)
        ->callAction('arguments', arguments: [
            'payload' => $payload = Str::random(),
        ])
        ->assertDispatched('arguments-called', arguments: [
            'payload' => $payload,
        ]);
});

it('can call an action and halt', function () {
    livewire(Actions::class)
        ->callAction('halt')
        ->assertDispatched('halt-called')
        ->assertActionHalted('halt');
});

it('can hide an action', function () {
    livewire(Actions::class)
        ->assertActionVisible('visible')
        ->assertActionHidden('hidden');
});

it('can disable an action', function () {
    livewire(Actions::class)
        ->assertActionEnabled('enabled')
        ->assertActionDisabled('disabled');
});

it('can have an icon', function () {
    livewire(Actions::class)
        ->assertActionHasIcon('hasIcon', 'heroicon-m-pencil-square')
        ->assertActionDoesNotHaveIcon('hasIcon', 'heroicon-m-trash');
});

it('can have a label', function () {
    livewire(Actions::class)
        ->assertActionHasLabel('hasLabel', 'My Action')
        ->assertActionDoesNotHaveLabel('hasLabel', 'My Other Action');
});

it('can have a color', function () {
    livewire(Actions::class)
        ->assertActionHasColor('hasColor', 'primary')
        ->assertActionDoesNotHaveColor('hasColor', 'gray');
});

it('can have a URL', function () {
    livewire(Actions::class)
        ->assertActionHasUrl('url', 'https://filamentphp.com')
        ->assertActionDoesNotHaveUrl('url', 'https://google.com');
});

it('can open a URL in a new tab', function () {
    livewire(Actions::class)
        ->assertActionShouldOpenUrlInNewTab('urlInNewTab')
        ->assertActionShouldNotOpenUrlInNewTab('urlNotInNewTab');
});

it('can state whether a page action exists', function () {
    livewire(Actions::class)
        ->assertActionExists('exists')
        ->assertActionDoesNotExist('doesNotExist');
});

it('can show a notification', function () {
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

it('will raise an exception if a notification was not sent checking notification object', function () {
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

it('will raise an exception if a notification was not sent checking notification title', function () {
    $this->expectException('PHPUnit\Framework\ExpectationFailedException');
    $this->expectExceptionMessage('A notification was not sent');

    livewire(Actions::class)
        ->callAction('does-not-show-notification')
        ->assertNotified('A notification');
});

it('can assert that a notification without an ID was sent', function () {
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

it('can assert that a notification with an ID was sent', function () {
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

it('will raise an exception if a notification was sent checking with a different notification title', function () {
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

it('will raise an exception if a notification is not sent but a previous notification was sent', function () {
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

test('can assert that notifications are sent in any order', function () {
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

it('will assert that a notification was not sent', function () {

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
    $this->expectExceptionMessage('The notification with the given configration was sent');

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
