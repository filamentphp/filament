<?php

namespace Filament\Resources\Pages;

use Filament\Pages\Page as BasePage;

class Page extends BasePage
{
    protected static ?string $breadcrumb = null;

    protected static string $resource;

    public static function route(string $path): array
    {
        return [
            'class' => static::class,
            'route' => $path,
        ];
    }

    public function getBreadcrumb(): ?string
    {
        return static::$breadcrumb ?? static::getTitle();
    }

    protected function getBreadcrumbs(): array
    {
        $resource = static::getResource();

        $breadcrumb = $this->getBreadcrumb();

        return array_merge(
            [$resource::getUrl() => $resource::getBreadcrumb()],
            (filled($breadcrumb) ? [$breadcrumb] : []),
        );
    }

    public static function authorizeResourceAccess(): void
    {
        abort_unless(static::getResource()::canViewAny(), 403);
    }

    public function getModel(): string
    {
        return static::getResource()::getModel();
    }

    public static function getResource(): string
    {
        return static::$resource;
    }

    protected function callHook(string $hook): void
    {
        if (! method_exists($this, $hook)) {
            return;
        }

        $this->{$hook}();
    }
}
