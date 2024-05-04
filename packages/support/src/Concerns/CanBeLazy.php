<?php

namespace Filament\Support\Concerns;

use Illuminate\Contracts\View\View;

trait CanBeLazy
{
    protected static bool $isLazy = true;

    protected ?string $placeholderHeight = null;

    public static function isLazy(): bool
    {
        return static::$isLazy;
    }

    public function placeholder(): View
    {
        return view('filament::components.loading-section', $this->getPlaceholderData());
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

    /**
     * @return array<string, mixed>
     */
    public function getPlaceholderData(): array
    {
        return [
            //
        ];
    }

    public function getPlaceholderHeight(): ?string
    {
        return $this->placeholderHeight;
    }
}
