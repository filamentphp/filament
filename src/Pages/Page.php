<?php

namespace Filament\Pages;

use Filament\Filament;
use Filament\NavigationItem;
use Illuminate\Support\Str;
use Livewire\Component;

class Page extends Component
{
    public static $icon = 'heroicon-o-document-text';

    public static $layout = 'filament::components.layouts.app';

    public static $navigationLabel;

    public static $navigationSort = 0;

    public static $slug;

    public static $view;

    public static function authorization()
    {
        return [];
    }

    public static function generateUrl($parameters = [], $absolute = true)
    {
        return route('filament.pages.' . static::route()->name, $parameters, $absolute);
    }

    public static function getIcon()
    {
        return static::$icon;
    }

    public static function getNavigationLabel()
    {
        if (static::$navigationLabel) {
            return static::$navigationLabel;
        }

        return (string) Str::of(class_basename(static::class))
            ->kebab()
            ->replace('-', ' ');
    }

    public static function getNavigationSort()
    {
        return static::$navigationSort;
    }

    public static function getPageTitle()
    {
        if (property_exists(static::class, 'pageTitle')) {
            return static::$pageTitle;
        }

        return static::getTitle();
    }

    public static function getSlug()
    {
        if (static::$slug) {
            return static::$slug;
        }

        return (string) Str::of(class_basename(static::class))->kebab();
    }

    public static function getTitle()
    {
        if (property_exists(static::class, 'title')) {
            return static::$title;
        }

        return (string) Str::of(class_basename(static::class))
            ->kebab()
            ->replace('-', ' ')
            ->title();
    }

    public function isAuthorized()
    {
        return Filament::can('view', static::class);
    }

    public static function navigationItems()
    {
        return [
            NavigationItem::make(Str::title(static::getNavigationLabel()), static::generateUrl())
                ->activeRule(
                    (string) Str::of(parse_url(static::generateUrl(), PHP_URL_PATH))
                        ->after('/')
                        ->append('*'),
                )
                ->icon(static::getIcon())
                ->sort(static::getNavigationSort()),
        ];
    }

    public function notify($message)
    {
        $this->dispatchBrowserEvent('notify', $message);
    }

    public function render()
    {
        return view(static::$view, $this->getViewData())
            ->layout(static::$layout, $this->getLayoutData());
    }

    public static function route()
    {
        return Route::make(static::getSlug(), static::getSlug());
    }

    protected function abortIfForbidden()
    {
        abort_unless($this->isAuthorized(), 403);
    }

    protected function getLayoutData()
    {
        return array_merge($this->layoutData(), [
            'title' => static::getPageTitle(),
        ]);
    }

    protected function getViewData()
    {
        return array_merge($this->viewData(), [
            'title' => static::getTitle(),
        ]);
    }

    protected function layoutData()
    {
        return [];
    }

    protected function viewData()
    {
        return [];
    }
}
