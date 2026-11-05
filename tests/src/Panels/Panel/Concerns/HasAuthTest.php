<?php

use Filament\Panel;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('configures distinct password reset endpoint slugs', function (): void {
    $panel = Panel::make()->passwordResetRoutePrefix(prefix: 'account-recovery');

    expect($panel->requestPasswordResetRouteSlug(slug: 'send-link'))->toBe($panel)
        ->and($panel->resetPasswordRouteSlug(slug: '/choose-password'))->toBe($panel)
        ->and($panel->getRequestPasswordResetRouteSlug())->toBe('/send-link')
        ->and($panel->getResetPasswordRouteSlug())->toBe('/choose-password')
        ->and($panel->getPasswordResetRoutePrefix())->toBe('/account-recovery');
});

it('normalizes password reset prefixes using `getPasswordResetRoutePrefix()`', function (?string $prefix, string $expected): void {
    $panel = Panel::make();

    if ($prefix !== null) {
        expect($panel->passwordResetRoutePrefix(prefix: $prefix))->toBe($panel);
    }

    expect($panel->getPasswordResetRoutePrefix())->toBe($expected);
})->with([
    'default' => [null, '/password-reset'],
    'no slash' => ['account/recovery', '/account/recovery'],
    'leading slash' => ['/account/recovery', '/account/recovery'],
    'repeated slashes' => ['///account/recovery/', '/account/recovery/'],
    'empty' => ['', '/'],
]);

it('supports password reset setter overrides and protected configuration', function (): void {
    $panel = new class extends Panel
    {
        protected string $requestPasswordResetRouteSlug = 'send-link';

        protected string $resetPasswordRouteSlug = 'choose-password';

        public function requestPasswordResetRouteSlug(string $slug): static
        {
            return parent::requestPasswordResetRouteSlug("request/{$slug}");
        }

        public function resetPasswordRouteSlug(string $slug): static
        {
            return parent::resetPasswordRouteSlug("reset/{$slug}");
        }
    };

    expect($panel->getRequestPasswordResetRouteSlug())->toBe('/send-link')
        ->and($panel->getResetPasswordRouteSlug())->toBe('/choose-password')
        ->and($panel->requestPasswordResetRouteSlug(slug: 'email'))->toBe($panel)
        ->and($panel->resetPasswordRouteSlug(slug: 'password'))->toBe($panel)
        ->and($panel->getRequestPasswordResetRouteSlug())->toBe('/request/email')
        ->and($panel->getResetPasswordRouteSlug())->toBe('/reset/password');
});
