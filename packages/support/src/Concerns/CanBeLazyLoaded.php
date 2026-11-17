<?php

namespace Filament\Support\Concerns;

use Filament\Support\Components\Attributes\Unexposed;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\HtmlString;

trait CanBeLazyLoaded
{
    protected static ?bool $isLazyLoaded = null;

    /**
     * @deprecated Use `$isLazyLoaded` instead.
     */
    protected static bool $isLazy = true;

    protected ?string $placeholderHeight = null;

    #[Unexposed]
    public static function isLazyLoaded(): bool
    {
        return static::$isLazyLoaded ?? static::isLazy();
    }

    /**
     * @deprecated Use `isLazyLoaded()` instead.
     */
    #[Unexposed]
    public static function isLazy(): bool
    {
        return static::$isLazyLoaded ?? static::$isLazy;
    }

    #[Unexposed]
    public function placeholder(): View
    {
        $loadingPlaceholder = $this->getLoadingPlaceholder();

        // `View::gatherData()` would render a nested view to a string that Blade escapes.
        return view(
            'filament::components.loading-section',
            [
                'height' => $this->getPlaceholderHeight(),
                'loadingPlaceholder' => ($loadingPlaceholder instanceof View)
                    ? new HtmlString($loadingPlaceholder->render())
                    : $loadingPlaceholder,
                ...$this->getPlaceholderData(),
            ],
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Unexposed]
    public function getPlaceholderData(): array
    {
        return [
            //
        ];
    }

    #[Unexposed]
    public function getPlaceholderHeight(): ?string
    {
        return $this->placeholderHeight;
    }

    #[Unexposed]
    public function getLoadingPlaceholder(): View | Htmlable | null
    {
        return null;
    }
}
