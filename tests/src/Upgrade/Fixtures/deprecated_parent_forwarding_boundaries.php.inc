<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedParentForwardingBoundaries;

use Closure;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Contracts\Support\Arrayable;

class CustomParent extends IconColumn
{
    public function options(array | Arrayable | Closure $customOptions): static
    {
        return $this;
    }
}

class CustomChild extends CustomParent
{
    public function options(array | Arrayable | Closure $customOptions): static
    {
        return parent::options($customOptions);
    }
}

class UnsupportedDispatch extends IconColumn
{
    public function configureExamples(): void
    {
        self::options([]);
        static::options([]);
    }

    public static function configureStatically(): void
    {
        parent::options([]);
    }
}

class UnrelatedParent
{
    public function options(array $options): static
    {
        return $this;
    }
}

class UnrelatedChild extends UnrelatedParent
{
    public function configureOptions(): static
    {
        return parent::options(options: []);
    }
}
