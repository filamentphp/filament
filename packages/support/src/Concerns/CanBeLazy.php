<?php

namespace Filament\Support\Concerns;

use Filament\Support\Attributes\ServerOnly;
use Illuminate\Contracts\View\View;

trait CanBeLazy
{
    protected static bool $isLazy = true;

    protected ?string $placeholderHeight = null;

    #[ServerOnly]
    public static function isLazy(): bool
    {
        return static::$isLazy;
    }

    #[ServerOnly]
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
    #[ServerOnly]
    public function getPlaceholderData(): array
    {
        return [
            //
        ];
    }

    #[ServerOnly]
    public function getPlaceholderHeight(): ?string
    {
        return $this->placeholderHeight;
    }
}
