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
        ->and($panel->getResetPasswordRoutePrefix())->toBe('/account-recovery');
})->with([
    ['requestPasswordResetRouteSlug', 'resetPasswordRouteSlug'],
    ['passwordResetRequestRouteSlug', 'passwordResetRouteSlug'],
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
