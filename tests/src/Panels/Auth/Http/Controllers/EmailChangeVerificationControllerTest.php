<?php

use Filament\Auth\Events\EmailChangeVerified;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\ReadReplica;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Event;
use League\Uri\Components\Query;

uses(TestCase::class);

it('can verify an email change', function (): void {
    Event::fake([EmailChangeVerified::class]);

    $userToVerify = User::factory()->create();
    $newEmail = fake()->email();
    $previousEmail = $userToVerify->email;

    expect($userToVerify->refresh())
        ->email->not->toBe($newEmail);

    $verificationUrl = Filament::getVerifyEmailChangeUrl($userToVerify, $newEmail);

    $verificationSignature = Query::new($verificationUrl)->get('signature');
    cache()->put("filament:auth:email_change:verification:{$verificationSignature}", true, ttl: now()->addHour());

    $this
        ->actingAs($userToVerify)
        ->get($verificationUrl)
        ->assertRedirect(Filament::getProfileUrl());

    Notification::assertNotified('Email address changed');

    expect($userToVerify->refresh())
        ->email->toBe($newEmail);

    Event::assertDispatchedTimes(EmailChangeVerified::class, 1);
    Event::assertDispatched(EmailChangeVerified::class, fn (EmailChangeVerified $event): bool => $event->getUser()->is($userToVerify) && ($event->getPreviousEmail() === $previousEmail) && ($event->getUser()->getAttribute('email') === $newEmail));

    expect(cache()->has("filament:auth:email_change:verification:{$verificationSignature}"))->toBeFalse();

    $this->get($verificationUrl)->assertForbidden();
});

it('can verify an email with a custom slug', function (): void {
    Filament::setCurrentPanel('slugs');

    $userToVerify = User::factory()->create();
    $newEmail = fake()->email();

    $verificationUrl = Filament::getVerifyEmailChangeUrl($userToVerify, $newEmail);

    $verificationSignature = Query::new($verificationUrl)->get('signature');
    cache()->put("filament:auth:email_change:verification:{$verificationSignature}", true, ttl: now()->addHour());

    expect($userToVerify)
        ->email->not->toBe($newEmail)
        ->and($verificationUrl)->toContain('/email-change-verification-test/verify-change-test/');

    $this
        ->actingAs($userToVerify)
        ->get($verificationUrl)
        ->assertRedirect(Filament::getProfileUrl());

    expect($userToVerify->refresh())
        ->email->toBe($newEmail);
});

it('cannot verify an email when signed in as another user', function (): void {
    Event::fake([EmailChangeVerified::class]);

    $userToVerify = User::factory()->create();
    $newEmail = fake()->email();

    expect($userToVerify->refresh())
        ->email->not->toBe($newEmail);

    $verificationUrl = Filament::getVerifyEmailChangeUrl($userToVerify, $newEmail);

    $verificationSignature = Query::new($verificationUrl)->get('signature');
    cache()->put("filament:auth:email_change:verification:{$verificationSignature}", true, ttl: now()->addHour());

    $anotherUser = User::factory()->create();

    $this
        ->actingAs($anotherUser)
        ->get($verificationUrl)
        ->assertForbidden();

    expect($userToVerify->refresh())
        ->email->not->toBe($newEmail);

    Event::assertNotDispatched(EmailChangeVerified::class);
});

it('cannot verify an email change to an address that has been taken since the verification link was issued', function (): void {
    Event::fake([EmailChangeVerified::class]);

    $connection = ReadReplica::connection('CREATE TABLE users (id INTEGER PRIMARY KEY, email TEXT, email_verified_at TEXT)');
    $connection->getPdo()->exec("INSERT INTO users (id, email) VALUES (1, 'original@example.com'), (2, 'taken@example.com')");
    $connection->getReadPdo()->exec("INSERT INTO users (id, email) VALUES (1, 'original@example.com')");

    $userToVerify = ReadReplicaEmailChangeUser::query()->findOrFail(1);
    $newEmail = 'taken@example.com';
    $originalEmail = $userToVerify->email;

    $verificationUrl = Filament::getVerifyEmailChangeUrl($userToVerify, $newEmail);

    $verificationSignature = Query::new($verificationUrl)->get('signature');
    cache()->put("filament:auth:email_change:verification:{$verificationSignature}", true, ttl: now()->addHour());

    $this
        ->actingAs($userToVerify)
        ->get($verificationUrl)
        ->assertRedirect(Filament::getProfileUrl());

    Notification::assertNotified('That email address is no longer available.');
    Notification::assertNotNotified('Email address changed');

    expect($userToVerify->refresh())
        ->email->toBe($originalEmail);

    expect(cache()->has("filament:auth:email_change:verification:{$verificationSignature}"))
        ->toBeFalse();

    Event::assertNotDispatched(EmailChangeVerified::class);
});

it('cannot verify an email change with the same URL twice', function (): void {
    Event::fake([EmailChangeVerified::class]);

    $userToVerify = User::factory()->create();
    $newEmail = fake()->email();

    expect($userToVerify->refresh())
        ->email->not->toBe($newEmail);

    $verificationUrl = Filament::getVerifyEmailChangeUrl($userToVerify, $newEmail);

    $verificationSignature = Query::new($verificationUrl)->get('signature');
    cache()->put("filament:auth:email_change:verification:{$verificationSignature}", true, ttl: now()->addHour());

    $this
        ->actingAs($userToVerify)
        ->get($verificationUrl)
        ->assertRedirect(Filament::getProfileUrl());

    expect($userToVerify->refresh())
        ->email->toBe($newEmail);

    Event::assertDispatchedTimes(EmailChangeVerified::class, 1);

    expect(cache()->has("filament:auth:email_change:verification:{$verificationSignature}"))
        ->toBeFalse();

    $this
        ->actingAs($userToVerify)
        ->get($verificationUrl)
        ->assertForbidden();

    Event::assertDispatchedTimes(EmailChangeVerified::class, 1);

    $userToVerify->update(['email' => fake()->email()]);

    $this
        ->actingAs($userToVerify)
        ->get($verificationUrl)
        ->assertForbidden();

    Event::assertDispatchedTimes(EmailChangeVerified::class, 1);
});

it('does not dispatch `EmailChangeVerified` when saving leaves the email address unchanged', function (): void {
    Event::fake([EmailChangeVerified::class]);

    $createdUser = User::factory()->create([
        'email' => 'user@example.com',
    ]);

    $userToVerify = LowercasingEmailChangeUser::query()->findOrFail($createdUser->getKey());
    $newEmail = 'User@example.com';

    $verificationUrl = Filament::getVerifyEmailChangeUrl($userToVerify, $newEmail);

    $verificationSignature = Query::new($verificationUrl)->get('signature');
    cache()->put("filament:auth:email_change:verification:{$verificationSignature}", true, ttl: now()->addHour());

    $this
        ->actingAs($userToVerify)
        ->get($verificationUrl)
        ->assertRedirect(Filament::getProfileUrl());

    Notification::assertNotified('Email address changed');

    expect($userToVerify->refresh())
        ->email->toBe('user@example.com');

    Event::assertNotDispatched(EmailChangeVerified::class);
});

it('does not dispatch `EmailChangeVerified` when the authenticated user already saved the new email address', function (): void {
    $this->freezeTime();

    Event::fake([EmailChangeVerified::class]);

    $userToVerify = User::factory()->create();
    $newEmail = fake()->email();

    $verificationUrl = Filament::getVerifyEmailChangeUrl($userToVerify, $newEmail);

    $verificationSignature = Query::new($verificationUrl)->get('signature');
    cache()->put("filament:auth:email_change:verification:{$verificationSignature}", true, ttl: now()->addHour());

    $userToVerify->update(['email' => $newEmail]);

    $this
        ->actingAs($userToVerify)
        ->get($verificationUrl)
        ->assertRedirect(Filament::getProfileUrl());

    expect($userToVerify->refresh())
        ->email->toBe($newEmail);

    Event::assertNotDispatched(EmailChangeVerified::class);
});

it('does not dispatch `EmailChangeVerified` when a model event prevents the email address from being saved', function (): void {
    Event::fake([EmailChangeVerified::class]);

    $createdUser = User::factory()->create();
    $previousEmail = $createdUser->email;

    UpdatePreventingEmailChangeUser::updating(static fn (): bool => false);

    $userToVerify = UpdatePreventingEmailChangeUser::query()->findOrFail($createdUser->getKey());
    $newEmail = fake()->email();

    $verificationUrl = Filament::getVerifyEmailChangeUrl($userToVerify, $newEmail);

    $verificationSignature = Query::new($verificationUrl)->get('signature');
    cache()->put("filament:auth:email_change:verification:{$verificationSignature}", true, ttl: now()->addHour());

    $this
        ->actingAs($userToVerify)
        ->get($verificationUrl)
        ->assertRedirect(Filament::getProfileUrl());

    expect($userToVerify->refresh())
        ->email->toBe($previousEmail);

    Event::assertNotDispatched(EmailChangeVerified::class);
});

class LowercasingEmailChangeUser extends User
{
    protected $table = 'users';

    protected function setEmailAttribute(string $email): void
    {
        $this->attributes['email'] = strtolower($email);
    }
}

class UpdatePreventingEmailChangeUser extends User
{
    protected $table = 'users';
}

class ReadReplicaEmailChangeUser extends User
{
    protected $connection = 'read-replica';

    protected $table = 'users';
}
