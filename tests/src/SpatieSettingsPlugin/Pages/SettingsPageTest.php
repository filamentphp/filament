<?php

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\SettingsPage;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Pages\ManageSiteSettings;
use Filament\Tests\Fixtures\Settings\SiteSettings;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\DB;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    DB::table('spatie_settings')->insert([
        ['group' => 'site', 'name' => 'site_name', 'locked' => false, 'payload' => json_encode('My Site')],
        ['group' => 'site', 'name' => 'site_description', 'locked' => false, 'payload' => json_encode('A test site')],
        ['group' => 'site', 'name' => 'site_active', 'locked' => false, 'payload' => json_encode(true)],
    ]);

    $this->actingAs(User::factory()->create());
});

it('can render page', function (): void {
    $this->get(ManageSiteSettings::getUrl())
        ->assertSuccessful();
});

it('can retrieve data', function (): void {
    livewire(ManageSiteSettings::class)
        ->assertFormSet([
            'site_name' => 'My Site',
            'site_description' => 'A test site',
            'site_active' => true,
        ]);
});

it('can save', function (): void {
    livewire(ManageSiteSettings::class)
        ->fillForm([
            'site_name' => 'Updated Site',
            'site_description' => 'Updated description',
            'site_active' => false,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $settings = app(SiteSettings::class);

    expect($settings->site_name)->toBe('Updated Site');
    expect($settings->site_description)->toBe('Updated description');
    expect($settings->site_active)->toBeFalse();
});

it('can validate input on save', function (): void {
    livewire(ManageSiteSettings::class)
        ->fillForm([
            'site_name' => '',
        ])
        ->call('save')
        ->assertHasFormErrors(['site_name' => 'required']);
});

it('sends a notification after saving', function (): void {
    livewire(ManageSiteSettings::class)
        ->fillForm([
            'site_name' => 'Notified Site',
            'site_description' => 'desc',
            'site_active' => true,
        ])
        ->call('save')
        ->assertNotified();
});

describe('page configuration', function (): void {
    it('returns the configured `$settings` class from `getSettings()`', function (): void {
        expect(ManageSiteSettings::getSettings())->toBe(SiteSettings::class);
    });

    it('derives settings class name when `$settings` is not set', function (): void {
        expect(DerivedGeneralSettings::getSettings())->toBe('App\\Settings\\DerivedGeneralSettings');
    });

    it('returns `true` for `hasFormWrapper()`', function (): void {
        expect((new ManageSiteSettings)->hasFormWrapper())->toBeTrue();
    });

    it('returns `null` for `getRedirectUrl()`', function (): void {
        expect((new ManageSiteSettings)->getRedirectUrl())->toBeNull();
    });

    it('returns `true` for `canEdit()`', function (): void {
        expect((new ManageSiteSettings)->canEdit())->toBeTrue();
    });
});

describe('actions', function (): void {
    it('returns an `Action` named `save` from `getSaveFormAction()`', function (): void {
        $action = (new ManageSiteSettings)->getSaveFormAction();

        expect($action)->toBeInstanceOf(Action::class);
        expect($action->getName())->toBe('save');
    });

    it('delegates `getSubmitFormAction()` to `getSaveFormAction()`', function (): void {
        $page = new ManageSiteSettings;

        expect($page->getSubmitFormAction()->getName())->toBe($page->getSaveFormAction()->getName());
    });

    it('returns one save action from `getFormActions()`', function (): void {
        $actions = (new ManageSiteSettings)->getFormActions();

        expect($actions)->toBeArray();
        expect($actions)->toHaveCount(1);
        expect($actions[0])->toBeInstanceOf(Action::class);
    });
});

describe('notifications', function (): void {
    it('returns a `Notification` from `getSavedNotification()`', function (): void {
        expect((new ManageSiteSettings)->getSavedNotification())->toBeInstanceOf(Notification::class);
    });

    it('returns a non-blank title from `getSavedNotificationTitle()`', function (): void {
        $title = (new ManageSiteSettings)->getSavedNotificationTitle();

        expect($title)->toBeString();
        expect($title)->not->toBeEmpty();
    });
});

class DerivedGeneralSettings extends SettingsPage
{
    // No $settings property — should derive from class name
}
