<?php

namespace Filament\Support\Icons;

use BackedEnum;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;

class IconManager
{
    /**
     * @var array<string, string | BackedEnum | Htmlable>
     */
    protected array $icons = [];

    /**
     * @param  array<string, string | BackedEnum | Htmlable>  $icons
     */
    public function register(array $icons): void
    {
        $this->icons = [
            ...$this->icons,
            ...$icons,
        ];
    }

    /**
     * @param  string|array<string>  $alias
     */
    public function resolve(string | array $alias): string | BackedEnum | Htmlable | null
    {
        foreach (Arr::wrap($alias) as $alias) {
            if (isset($this->icons[$alias])) {
                return $this->icons[$alias];
            }
        }

        return null;
    }
}
