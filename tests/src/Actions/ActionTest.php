<?php

use Filament\Notifications\Notification;
use Filament\Tests\Actions\Fixtures\Pages\Actions;
use Filament\Tests\Actions\TestCase;
use Illuminate\Support\Str;
use function Pest\Livewire\livewire;

uses(TestCase::class);

it('can call an action', function () {
    livewire(Actions::class)
        ->callAction('simple')
        ->assertEmitted('simple-called');
});

it('can call an action with data', function () {
    livewire(Actions::class)
        ->callAction('data', data: [
            'payload' => $payload = Str::random(),
        ])
        ->assertHasNoActionErrors()
        ->assertEmitted('data-called', [
            'payload' => $payload,
        ]);
});

it('can validate an action\'s data', function () {
    livewire(Actions::class)
        ->callAction('data', data: [
            'payload' => null,
        ])
        ->assertHasActionErrors(['payload' => ['required']])
        ->assertNotEmitted('data-called');
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
        ->assertEmitted('arguments-called', [
            'payload' => $payload,
        ]);
});

it('can call an action with arguments', function () {
    livewire(Actions::class)
        ->callAction('arguments', arguments: [
            'payload' => $payload = Str::random(),
        ])
        ->assertEmitted('arguments-called', [
            'payload' => $payload,
        ]);
});

it('can call an action and halt', function () {
    livewire(Actions::class)
        ->callAction('halt')
        ->assertEmitted('halt-called')
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
        ->assertActionHasIcon('has-icon', 'heroicon-m-pencil-square')
        ->assertActionDoesNotHaveIcon('has-icon', 'heroicon-m-trash');
});

it('can have a label', function () {
    livewire(Actions::class)
        ->assertActionHasLabel('has-label', 'My Action')
        ->assertActionDoesNotHaveLabel('has-label', 'My Other Action');
});

it('can have a color', function () {
    livewire(Actions::class)
        ->assertActionHasColor('has-color', 'primary')
        ->assertActionDoesNotHaveColor('has-color', 'gray');
});

it('can have a URL', function () {
    livewire(Actions::class)
        ->assertActionHasUrl('url', 'https://filamentphp.com')
        ->assertActionDoesNotHaveUrl('url', 'https://google.com');
});

it('can open a URL in a new tab', function () {
    livewire(Actions::class)
        ->assertActionShouldOpenUrlInNewTab('url_in_new_tab')
        ->assertActionShouldNotOpenUrlInNewTab('url_not_in_new_tab');
});

it('can state whether a page action exists', function () {
    livewire(Actions::class)
        ->assertActionExists('exists')
        ->assertActionDoesNotExist('does_not_exist');
});

it('can show a notification', function () {
    livewire(Actions::class)
        ->callAction('shows_notification')
        ->assertNotified();

    livewire(Actions::class)
        ->callAction('shows_notification')
        ->assertNotified('A notification');

    livewire(Actions::class)
        ->callAction('shows_notification')
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
        ->callAction('does_not_show_notification')
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
        ->callAction('does_not_show_notification')
        ->assertNotified('A notification');
});

it('can assert that a notification without an ID was sent', function () {
    livewire(Actions::class)
        ->callAction('shows_notification')
        ->assertNotified();

    livewire(Actions::class)
        ->callAction('shows_notification')
        ->assertNotified('A notification');

    livewire(Actions::class)
        ->callAction('shows_notification')
        ->assertNotified(
            Notification::make()
                ->title('A notification')
                ->success()
        );
});

it('can assert that a notification with an ID was sent', function () {
    livewire(Actions::class)
        ->callAction('shows_notification_with_id')
        ->assertNotified();

    livewire(Actions::class)
        ->callAction('shows_notification_with_id')
        ->assertNotified('A notification');

    livewire(Actions::class)
        ->callAction('shows_notification_with_id')
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
        ->callAction('shows_notification_with_id')
        ->assertNotified(
            Notification::make()
                ->title('A different title')
                ->success()
        );
});

it('will raise an exception if a notification is not sent but a previous notification was sent', function () {
    livewire(Actions::class)
        ->callAction('shows_notification_with_id')
        ->assertNotified(
            Notification::make()
                ->title('A notification')
                ->success()
        );

    $this->expectException('PHPUnit\Framework\ExpectationFailedException');
    $this->expectExceptionMessage('A notification was not sent');

    livewire(Actions::class)
        ->callAction('does_not_show_notification')
        ->assertNotified(
            Notification::make()
                ->title('A notification')
                ->success()
        );
});

test('can assert that notifications are sent in any order', function () {
    livewire(Actions::class)
        ->callAction('two_notifications')
        ->assertNotified('Second notification');

    livewire(Actions::class)
        ->callAction('two_notifications')
        ->assertNotified('First notification');

    $this->expectException('PHPUnit\Framework\ExpectationFailedException');
    $this->expectExceptionMessage('A notification was not sent');

    livewire(Actions::class)
        ->callAction('two_notifications')
        ->assertNotified('Third notification');
});
