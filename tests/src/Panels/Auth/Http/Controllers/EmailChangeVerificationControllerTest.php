<?php

use Filament\Auth\Events\EmailChangeVerified;
use Filament\Auth\Http\Requests\BlockEmailChangeVerificationRequest;
use Filament\Auth\Http\Requests\EmailChangeVerificationRequest;
use Filament\Facades\Filament;
use Filament\Notifications\Livewire\Notifications;
use Filament\Notifications\Notification;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\ReadReplica;
use Filament\Tests\TestCase;
use Illuminate\Cache\Events\CacheHit;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Event;
use League\Uri\Components\Query;

uses(TestCase::class);

it('renders the verified email literally', function (string $newEmail): void {
    $user = User::factory()->create();
    $verificationUrl = Filament::getVerifyEmailChangeUrl($user, $newEmail);
    $verificationSignature = Query::new($verificationUrl)->get('signature');
    cache()->put("filament:auth:email_change:verification:{$verificationSignature}", true, ttl: now()->addHour());

    $this->actingAs($user)->get($verificationUrl)->assertRedirect(Filament::getProfileUrl());

    $notifications = new Notifications;
    $notifications->mount();
    $notifications->pullNotifications();
    $notification = $notifications->notifications->sole();

    expect($notification->getBody())->toContain(e($newEmail));

    $document = new DOMDocument;
    @$document->loadHTML('<?xml encoding="UTF-8">' . $notification->toHtml());

    expect($document->textContent)->toContain($newEmail)
        ->and((new DOMXPath($document))->query('//a | //img | //em | //code | //style'))->toHaveCount(0);
})->with('proposed email addresses');

it('can verify an email change using current and legacy cache keys', function (string $cacheKeyFormat): void {
    Event::fake([EmailChangeVerified::class]);

    $userToVerify = User::factory()->create();
    $newEmail = fake()->email();
    $previousEmail = $userToVerify->email;

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

    $this
        ->actingAs($userToVerify)
        ->get($verificationUrl)
        ->assertRedirect(Filament::getProfileUrl());

    Notification::assertNotified('Email address changed');

    expect($userToVerify->refresh())
        ->email->toBe($newEmail);

    Event::assertDispatchedTimes(EmailChangeVerified::class, 1);
    Event::assertDispatched(EmailChangeVerified::class, fn (EmailChangeVerified $event): bool => $event->getUser()->is($userToVerify) && ($event->getPreviousEmail() === $previousEmail) && ($event->getUser()->getAttribute('email') === $newEmail));

    expect(cache()->has("filament:auth:email_change:verification:{$verificationSignature}"))->toBeFalse()
        ->and(cache()->has($verificationSignature))->toBeFalse();

    $this->get($verificationUrl)->assertForbidden();
})->with(['current', 'legacy', 'both']);

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

    Notification::assertNotified(Notification::make()
        ->title('That email address is no longer available.')
        ->body('It was claimed by another account while your verification link was outstanding.')
        ->danger());
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

it('only allows one overlapping email change operation to succeed', function (bool $isFirstVerification, bool $isSecondVerification, string $cacheKeyFormat): void {
    $user = User::factory()->create();
    $newEmail = 'changed@example.com';
    $oldEmail = $user->email;
    $verificationUrl = Filament::getVerifyEmailChangeUrl($user, $newEmail);
    $signature = Query::new($verificationUrl)->get('signature');
    $cacheKey = "filament:auth:email_change:verification:{$signature}";

    if ($cacheKeyFormat !== 'legacy') {
        cache()->put($cacheKey, true, now()->addHour());
    }

    if ($cacheKeyFormat !== 'current') {
        cache()->put($signature, true, now()->addHour());
    }

    $requests = [];

    foreach ([$isFirstVerification, $isSecondVerification] as $isVerification) {
        $requestClass = $isVerification ? EmailChangeVerificationRequest::class : BlockEmailChangeVerificationRequest::class;
        $request = $requestClass::create($verificationUrl . '&verificationSignature=' . $signature);
        $route = new Route('GET', 'email-change/{email}', static fn () => null);
        $route->bind($request);
        $route->setParameter('email', encrypt($newEmail));
        $request->setRouteResolver(static fn (): Route => $route);
        $request->setUserResolver(static fn (): User => $user);
        $requests[] = $request;
    }

    $secondResult = null;
    $hasOverlapped = false;

    // Start the competing operation while the first is reading the pending request.
    Event::listen(CacheHit::class, function (CacheHit $event) use ($cacheKey, $signature, $requests, &$secondResult, &$hasOverlapped): void {
        if ($hasOverlapped || (! in_array($event->key, [$cacheKey, $signature], true))) {
            return;
        }

        $hasOverlapped = true;
        $secondResult = $requests[1]->fulfill();
    });

    $firstResult = $requests[0]->fulfill();

    expect($hasOverlapped)->toBeTrue()
        ->and((int) $firstResult + (int) $secondResult)->toBe(1)
        ->and($user->refresh()->email)->toBe((($firstResult && $isFirstVerification) || ($secondResult && $isSecondVerification)) ? $newEmail : $oldEmail);
})->with([
    'verify and verify' => [true, true],
    'verify and block' => [true, false],
    'block and verify' => [false, true],
    'block and block' => [false, false],
])->with(['current', 'legacy', 'both']);

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

it('reports a failed email change and does not dispatch `EmailChangeVerified` when a model event prevents the email address from being saved', function (): void {
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

    Notification::assertNotified(Notification::make()
        ->title('Email address could not be changed')
        ->body('The email change could not be completed. Please try changing your email address again.')
        ->danger());
    Notification::assertNotNotified('Email address changed');

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
