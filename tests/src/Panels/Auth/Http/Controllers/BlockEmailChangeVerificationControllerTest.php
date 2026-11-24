<?php

use Filament\Facades\Filament;
use Filament\Notifications\Livewire\Notifications;
use Filament\Notifications\Notification;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use League\Uri\Components\Query;

uses(TestCase::class);

it('renders the proposed email literally when blocking succeeds or fails', function (string $newEmail, bool $isSuccessful): void {
    $user = User::factory()->create();
    $verificationUrl = Filament::getVerifyEmailChangeUrl($user, $newEmail);
    $verificationSignature = Query::new($verificationUrl)->get('signature');

    if ($isSuccessful) {
        cache()->put("filament:auth:email_change:verification:{$verificationSignature}", true, ttl: now()->addHour());
    }

    $this->actingAs($user)
        ->get(Filament::getBlockEmailChangeVerificationUrl($user, $newEmail, $verificationSignature))
        ->assertRedirect(Filament::getProfileUrl());

    $notifications = new Notifications;
    $notifications->mount();
    $notifications->pullNotifications();
    $notification = $notifications->notifications->sole();

    expect($notification->getStatus())->toBe($isSuccessful ? 'success' : 'danger')
        ->and($notification->getBody())->toContain(e($newEmail));

    $document = new DOMDocument;
    @$document->loadHTML('<?xml encoding="UTF-8">' . $notification->toHtml());

    expect($document->textContent)->toContain($newEmail)
        ->and((new DOMXPath($document))->query('//a | //img | //em | //code | //style'))->toHaveCount(0);
})->with('proposed email addresses')->with(['successful' => true, 'failed' => false]);

it('displays the proposed email as text in the browser when blocking succeeds or fails', function (bool $isSuccessful): void {
    Artisan::call('filament:assets');

    $user = User::factory()->create();
    $this->actingAs($user);
    $newEmail = '"<a href=https://example.com>Sign in</a> *b*`code`"@example.com';
    $verificationUrl = Filament::getVerifyEmailChangeUrl($user, $newEmail);
    $verificationSignature = Query::new($verificationUrl)->get('signature');

    if ($isSuccessful) {
        cache()->put("filament:auth:email_change:verification:{$verificationSignature}", true, ttl: now()->addHour());
    }

    visit(Filament::getBlockEmailChangeVerificationUrl($user, $newEmail, $verificationSignature))
        ->assertSeeIn('[role="status"]', $newEmail)
        ->assertMissing('[role="status"] a, [role="status"] img, [role="status"] em, [role="status"] code, [role="status"] style');
})->with(['successful' => true, 'failed' => false]);

it('can block an email change using current and legacy cache keys', function (string $cacheKeyFormat): void {
    $userToVerify = User::factory()->create();
    $newEmail = fake()->email();

    expect($userToVerify->refresh())
        ->email->not->toBe($newEmail);

    $verificationUrl = Filament::getVerifyEmailChangeUrl($userToVerify, $newEmail);

    $verificationSignature = Query::new($verificationUrl)->get('signature');

    if ($cacheKeyFormat !== 'legacy') {
        cache()->put("filament:auth:email_change:verification:{$verificationSignature}", true, ttl: now()->addHour());
    }

    if ($cacheKeyFormat !== 'current') {
        cache()->put($verificationSignature, true, ttl: now()->addHour());
    }

    $blockVerificationUrl = Filament::getBlockEmailChangeVerificationUrl($userToVerify, $newEmail, $verificationSignature);

    $this
        ->actingAs($userToVerify)
        ->get($blockVerificationUrl)
        ->assertRedirect(Filament::getProfileUrl());

    Notification::assertNotified('Email address change blocked');

    expect($userToVerify->refresh())
        ->email->not->toBe($newEmail);

    expect(cache()->has($verificationSignature))->toBeFalse()
        ->and(cache()->has("filament:auth:email_change:verification:{$verificationSignature}"))->toBeFalse();

    $this
        ->actingAs($userToVerify)
        ->get($verificationUrl)
        ->assertForbidden();

    expect($userToVerify->refresh())
        ->email->not->toBe($newEmail);
})->with(['current', 'legacy', 'both']);

it('cannot block an email change when signed in as another user', function (): void {
    $userToVerify = User::factory()->create();
    $newEmail = fake()->email();

    $verificationUrl = Filament::getVerifyEmailChangeUrl($userToVerify, $newEmail);

    $verificationSignature = Query::new($verificationUrl)->get('signature');
    cache()->put("filament:auth:email_change:verification:{$verificationSignature}", true, ttl: now()->addHour());

    $blockVerificationUrl = Filament::getBlockEmailChangeVerificationUrl($userToVerify, $newEmail, $verificationSignature);

    $anotherUser = User::factory()->create();

    $this
        ->actingAs($anotherUser)
        ->get($blockVerificationUrl)
        ->assertForbidden();

    $this
        ->actingAs($userToVerify)
        ->get($verificationUrl)
        ->assertRedirect(Filament::getProfileUrl());

    expect($userToVerify->refresh())
        ->email->toBe($newEmail);
});

it('cannot block an email change once it has been verified', function (): void {
    $userToVerify = User::factory()->create();
    $newEmail = fake()->email();

    $verificationUrl = Filament::getVerifyEmailChangeUrl($userToVerify, $newEmail);

    $verificationSignature = Query::new($verificationUrl)->get('signature');
    cache()->put("filament:auth:email_change:verification:{$verificationSignature}", true, ttl: now()->addHour());

    $this
        ->actingAs($userToVerify)
        ->get($verificationUrl)
        ->assertRedirect(Filament::getProfileUrl());

    expect($userToVerify->refresh())
        ->email->toBe($newEmail);

    $blockVerificationUrl = Filament::getBlockEmailChangeVerificationUrl($userToVerify, $newEmail, $verificationSignature);

    $this
        ->actingAs($userToVerify)
        ->get($blockVerificationUrl)
        ->assertRedirect(Filament::getProfileUrl());

    Notification::assertNotified('Failed to block email address change');

    expect($userToVerify->refresh())
        ->email->toBe($newEmail);
});
