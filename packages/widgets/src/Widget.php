<?php

namespace Filament\Widgets;

use Illuminate\Contracts\View\View;
use Livewire\Component;

abstract class Widget extends Component
{
    protected static bool $isDiscovered = true;

    protected static ?int $sort = null;

    protected static string $view;

    protected int | string | array $columnSpan = 1;

    public static function canView(): bool
    {
        return true;
    }

    public static function getSort(): int
    {
        return static::$sort ?? -1;
    }

    public function getColumnSpan(): int | string | array
    {
        return $this->columnSpan;
    }

    protected function getViewData(): array
    {
        return [];
    }

    public static function isDiscovered(): bool
    {
        return static::$isDiscovered;
    }

    public function render(): View
    {
        return view(static::$view, $this->getViewData());
    }
}
