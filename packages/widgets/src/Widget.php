<?php

namespace Filament\Widgets;

use Filament\Support\Components\Attributes\Unexposed;
use Filament\Support\Concerns\CanBeLazy;
use Filament\Widgets\Concerns\CanAuthorizeAccess;
use Illuminate\Contracts\View\View;
use Livewire\Component;

abstract class Widget extends Component
{
    use CanAuthorizeAccess;
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

    #[Unexposed]
    public static function canView(): bool
    {
        return true;
    }

    #[Unexposed]
    public static function getSort(): int
    {
        return static::$sort ?? -1;
    }

    /**
     * @return int | string | array<string, int | null>
     */
    #[Unexposed]
    public function getColumnSpan(): int | string | array
    {
        return $this->columnSpan;
    }

    /**
     * @return int | string | array<string, int | null>
     */
    #[Unexposed]
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

    #[Unexposed]
    public static function isDiscovered(): bool
    {
        return static::$isDiscovered;
    }

    #[Unexposed]
    public function render(): View
    {
        return view($this->view, $this->getViewData());
    }

    /**
     * @param  array<string, mixed>  $properties
     */
    #[Unexposed]
    public static function make(array $properties = []): WidgetConfiguration
    {
        return app(WidgetConfiguration::class, ['widget' => static::class, 'properties' => $properties]);
    }

    /**
     * @return array<string, mixed>
     */
    #[Unexposed]
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
    #[Unexposed]
    public static function getDefaultProperties(): array
    {
        $properties = [];

        if (static::isLazyLoaded()) {
            $properties['lazy'] = true;
        }

        return $properties;
    }
}
