<?php

namespace Filament\Tests\Upgrade\Fixtures\ExposedAttributeReflection;

use Filament\Support\Components\Attributes\ExposedLivewireMethod as LegacyExposed;
use ReflectionMethod;

class Counter
{
    #[LegacyExposed]
    public function increment(): void {}
}

function inspect(): array
{
    return (new ReflectionMethod(Counter::class, 'increment'))->getAttributes('Filament\\Support\\Components\\Attributes\\ExposedLivewireMethod');
}
