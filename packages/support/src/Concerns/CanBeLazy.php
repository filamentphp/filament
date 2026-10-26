<?php

namespace Filament\Support\Concerns;

use Filament\Support\Components\Attributes\Unexposed;
use Illuminate\Contracts\View\View;

trait CanBeLazy
{
    protected static ?bool $isLazyLoaded = null;

    protected ?string $placeholderHeight = null;

    #[Unexposed]
    public static function isLazyLoaded(): bool
    {
        return static::$isLazyLoaded ?? true;
    }

    #[Unexposed]
    public function placeholder(): View
    {
        return view(
            'filament::components.loading-section',
            [
                'height' => $this->getPlaceholderHeight(),
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
}
