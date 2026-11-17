<?php

use Filament\Panel;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('configures distinct password reset endpoint slugs with either setter family', function (string $requestMethod, string $resetMethod): void {
    $panel = Panel::make()->passwordResetRoutePrefix(prefix: 'account-recovery');

    expect($panel->{$requestMethod}(slug: 'send-link'))->toBe($panel)
        ->and($panel->{$resetMethod}(slug: '/choose-password'))->toBe($panel)
        ->and($panel->getRequestPasswordResetRouteSlug())->toBe('/send-link')
        ->and($panel->getResetPasswordRouteSlug())->toBe('/choose-password')
        ->and($panel->getPasswordResetRoutePrefix())->toBe('/account-recovery')
        ->and($panel->getResetPasswordRoutePrefix())->toBe('/account-recovery');
})->with([
    ['requestPasswordResetRouteSlug', 'resetPasswordRouteSlug'],
    ['passwordResetRequestRouteSlug', 'passwordResetRouteSlug'],
]);

it('normalizes password reset prefixes through both getters', function (?string $prefix, string $expected): void {
    $panel = Panel::make();

    if ($prefix !== null) {
        expect($panel->passwordResetRoutePrefix(prefix: $prefix))->toBe($panel);
    }

    expect($panel->getPasswordResetRoutePrefix())->toBe($expected)
        ->and($panel->getResetPasswordRoutePrefix())->toBe($expected);
})->with([
    'default' => [null, '/password-reset'],
    'no slash' => ['account/recovery', '/account/recovery'],
    'leading slash' => ['/account/recovery', '/account/recovery'],
    'repeated slashes' => ['///account/recovery/', '/account/recovery/'],
    'empty' => ['', '/'],
]);

it('delegates legacy password reset setters to canonical setters while retaining protected configuration', function (): void {
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
        ->and($panel->passwordResetRequestRouteSlug(slug: 'email'))->toBe($panel)
        ->and($panel->passwordResetRouteSlug(slug: 'password'))->toBe($panel)
        ->and($panel->getRequestPasswordResetRouteSlug())->toBe('/request/email')
        ->and($panel->getResetPasswordRouteSlug())->toBe('/reset/password');
});
