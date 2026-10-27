<?php

namespace Filament\Tests\Upgrade\Fixtures\ExposedAttributeTypes;

use Filament\Support\Components\Attributes\ExposedLivewireMethod as LegacyExposed;

class CustomAttribute extends LegacyExposed {}

class Consumer
{
    #[LegacyExposed]
    public function exposed(): void {}

    public function inspect(LegacyExposed $attribute): LegacyExposed
    {
        if ($attribute instanceof LegacyExposed) {
            $name = LegacyExposed::class;
        }

        return new LegacyExposed;
    }
}
