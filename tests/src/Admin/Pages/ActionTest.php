<?php

use Filament\Notifications\Notification;
use Filament\Tests\Admin\Fixtures\Pages\PageActions;
use Filament\Tests\Admin\Pages\TestCase;
use Illuminate\Support\Str;
use function Pest\Livewire\livewire;

uses(TestCase::class);

it('can call an action', function () {
    livewire(PageActions::class)
        ->callPageAction('simple')
        ->assertEmitted('simple-called');
});

it('can call an action with data', function () {
    livewire(PageActions::class)
        ->callPageAction('data', data: [
            'payload' => $payload = Str::random(),
        ])
        ->assertHasNoPageActionErrors()
        ->assertEmitted('data-called', [
            'payload' => $payload,
        ]);
});

it('can validate an action\'s data', function () {
    livewire(PageActions::class)
        ->callPageAction('data', data: [
            'payload' => null,
        ])
        ->assertHasPageActionErrors(['payload' => ['required']])
        ->assertNotEmitted('data-called');
});

it('can set default action data when mounted', function () {
    livewire(PageActions::class)
        ->mountPageAction('data')
        ->assertPageActionDataSet([
            'foo' => 'bar',
        ]);
});

it('can call an action with arguments', function () {
    livewire(PageActions::class)
        ->callPageAction('arguments', arguments: [
            'payload' => $payload = Str::random(),
        ])
        ->assertEmitted('arguments-called', [
            'payload' => $payload,
        ]);
});

it('can call an action and halt', function () {
    livewire(PageActions::class)
        ->callPageAction('halt')
        ->assertEmitted('halt-called')
        ->assertPageActionHalted('halt');
});

it('can hide an action', function () {
    livewire(PageActions::class)
        ->assertPageActionVisible('visible')
        ->assertPageActionHidden('hidden');
});

it('can disable an action', function () {
    livewire(PageActions::class)
        ->assertPageActionEnabled('enabled')
        ->assertPageActionDisabled('disabled');
});

it('can have an icon', function () {
    livewire(PageActions::class)
        ->assertPageActionHasIcon('has-icon', 'heroicon-s-pencil')
        ->assertPageActionDoesNotHaveIcon('has-icon', 'heroicon-o-trash');
});

it('can have a label', function () {
    livewire(PageActions::class)
        ->assertPageActionHasLabel('has-label', 'My Action')
        ->assertPageActionDoesNotHaveLabel('has-label', 'My Other Action');
});

it('can have a color', function () {
    livewire(PageActions::class)
        ->assertPageActionHasColor('has-color', 'primary')
        ->assertPageActionDoesNotHaveColor('has-color', 'secondary');
});

it('can have a URL', function () {
    livewire(PageActions::class)
        ->assertPageActionHasUrl('url', 'https://filamentphp.com')
        ->assertPageActionDoesNotHaveUrl('url', 'https://google.com');
});

it('can open a URL in a new tab', function () {
    livewire(PageActions::class)
        ->assertPageActionShouldOpenUrlInNewTab('url_in_new_tab')
        ->assertPageActionShouldNotOpenUrlInNewTab('url_not_in_new_tab');
});

it('can state whether a page action exists', function () {
    livewire(PageActions::class)
        ->assertPageActionExists('exists')
        ->assertPageActionDoesNotExist('does_not_exist');
});

it('can state whether several page actions exist in order', function () {
    livewire(PageActions::class)
        ->assertPageActionsExistInOrder(['exists', 'exists_in_order']);
});

it('can show a notification', function () {
    livewire(PageActions::class)
        ->callPageAction('shows_notification')
        ->assertNotified();

    livewire(PageActions::class)
        ->callPageAction('shows_notification')
        ->assertNotified('A notification');

    livewire(PageActions::class)
        ->callPageAction('shows_notification')
        ->assertNotified(
            Notification::make()
                ->title('A notification')
                ->success()
        );
});

it('will raise an exception if a notification was not sent checking notification object', function () {
    $this->expectException('PHPUnit\Framework\ExpectationFailedException');
    $this->expectExceptionMessage('A notification was not sent');

    livewire(PageActions::class)
        ->callPageAction('does_not_show_notification')
        ->assertNotified(
            Notification::make()
                ->title('A notification')
                ->success()
        );
});

it('will raise an exception if a notification was not sent checking notification title', function () {
    $this->expectException('PHPUnit\Framework\ExpectationFailedException');
    $this->expectExceptionMessage('A notification was not sent');

    livewire(PageActions::class)
        ->callPageAction('does_not_show_notification')
        ->assertNotified('A notification');
});

it('can assert that a notification without an ID was sent', function () {
    livewire(PageActions::class)
        ->callPageAction('shows_notification')
        ->assertNotified();

    livewire(PageActions::class)
        ->callPageAction('shows_notification')
        ->assertNotified('A notification');

    livewire(PageActions::class)
        ->callPageAction('shows_notification')
        ->assertNotified(
            Notification::make()
                ->title('A notification')
                ->success()
        );
});

it('can assert that a notification with an ID was sent', function () {
    livewire(PageActions::class)
        ->callPageAction('shows_notification_with_id')
        ->assertNotified();

    livewire(PageActions::class)
        ->callPageAction('shows_notification_with_id')
        ->assertNotified('A notification');

    livewire(PageActions::class)
        ->callPageAction('shows_notification_with_id')
        ->assertNotified(
            Notification::make('notification_with_id')
                ->title('A notification')
                ->success()
        );
});

it('will raise an exception if a notification was sent checking with a different notification title', function () {
    $this->expectException('PHPUnit\Framework\ExpectationFailedException');
    $this->expectExceptionMessage('Failed asserting that two arrays are identical.');

    livewire(PageActions::class)
        ->callPageAction('shows_notification_with_id')
        ->assertNotified(
            Notification::make()
                ->title('A different title')
                ->success()
        );
});

it('will raise an exception if a notification is not sent but a previous notification was sent', function () {
    livewire(PageActions::class)
        ->callPageAction('shows_notification_with_id')
        ->assertNotified(
            Notification::make()
                ->title('A notification')
                ->success()
        );

    $this->expectException('PHPUnit\Framework\ExpectationFailedException');
    $this->expectExceptionMessage('A notification was not sent');

    livewire(PageActions::class)
        ->callPageAction('does_not_show_notification')
        ->assertNotified(
            Notification::make()
                ->title('A notification')
                ->success()
        );
});

test('can assert that notifications are sent in any order', function () {
    livewire(PageActions::class)
        ->callPageAction('two_notifications')
        ->assertNotified('Second notification');

    livewire(PageActions::class)
        ->callPageAction('two_notifications')
        ->assertNotified('First notification');

    $this->expectException('PHPUnit\Framework\ExpectationFailedException');
    $this->expectExceptionMessage('A notification was not sent');

    livewire(PageActions::class)
        ->callPageAction('two_notifications')
        ->assertNotified('Third notification');
});
