<?php

namespace Filament\Resources\Pages;

use Filament\Pages\Page as BasePage;

class Page extends BasePage
{
    protected static ?string $breadcrumb = null;

    protected static string $resource;

    public static function routeTo(string $path, string $name): array
    {
        return [
            'name' => $name,
            'page' => static::class,
            'path' => $path,
        ];
    }

    public function getBreadcrumb(): string
    {
        return static::$breadcrumb ?? static::getTitle();
    }

    protected function getBreadcrumbs(): array
    {
        $resource = static::getResource();

        return [
            $resource::getUrl() => $resource::getBreadcrumb(),
            $this->getBreadcrumb(),
        ];
    }

    public static function getModel(): string
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
