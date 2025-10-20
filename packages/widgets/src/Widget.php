<?php

namespace Filament\Widgets;

use Filament\Support\Concerns\CanBeLazy;
use Illuminate\Contracts\View\View;
use Livewire\Component;

abstract class Widget extends Component
{
    use CanBeLazy;

    protected static bool $isDiscovered = true;

    protected static ?int $sort = null;

    /**
     * @var view-string
     */
    protected string $view;

    /**
     * @var int | string | array<string, int | null>
     */
    protected int | string | array $columnSpan = 1;

    /**
     * @var int | string | array<string, int | null>
     */
    protected int | string | array $columnStart = [];

    public static function canView(): bool
    {
        return true;
    }

    public static function getSort(): int
    {
        return static::$sort ?? -1;
    }

    /**
     * @return int | string | array<string, int | null>
     */
    public function getColumnSpan(): int | string | array
    {
        return $this->columnSpan;
    }

    /**
     * @return int | string | array<string, int | null>
     */
    public function getColumnStart(): int | string | array
    {
        return $this->columnStart;
    }

    /**
     * @return array<string, mixed>
     */
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
        return view($this->view, $this->getViewData());
    }

    /**
     * @param  array<string, mixed>  $properties
     */
    public static function make(array $properties = []): WidgetConfiguration
    {
        return app(WidgetConfiguration::class, ['widget' => static::class, 'properties' => $properties]);
    }

    /**
     * @return array<string, mixed>
     */
    public function getPlaceholderData(): array
    {
        return [
            'columnSpan' => $this->getColumnSpan(),
            'columnStart' => $this->getColumnStart(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function getDefaultProperties(): array
    {
        $properties = [];

        if (static::isLazy()) {
            $properties['lazy'] = true;
        }

        return $properties;
    }
}
