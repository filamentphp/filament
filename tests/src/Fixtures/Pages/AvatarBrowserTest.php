<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Pages\Page;

class AvatarBrowserTest extends Page
{
    protected string $view = 'pages.avatar-browser-test';

    protected static bool $shouldRegisterNavigation = false;

    /** @return array<array<string, mixed>> */
    public function getAvatarCases(): array
    {
        $source = 'data:image/svg+xml,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="80" height="80"><rect width="80" height="80" fill="#6366f1"/><circle cx="40" cy="28" r="14" fill="#e0e7ff"/><ellipse cx="40" cy="75" rx="28" ry="27" fill="#e0e7ff"/></svg>');

        return array_map(static fn (array $attributes): array => [
            'src' => $source,
            ...$attributes,
        ], [
            [],
            ['alt' => 'Small portrait', 'size' => 'sm', 'circular' => false],
            ['alt' => 'Large portrait', 'size' => 'lg', 'title' => 'Profile', 'loading' => 'eager', 'data-profile' => '42'],
            ['alt' => 'Custom portrait', 'size' => 'avatar-custom-size', 'class' => 'avatar-custom-theme', 'circular' => false],
        ]);
    }
}
