<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedEnumConstants;

use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\IconSize as Size;

enum CustomIconSize: string
{
    case ExtraExtraLarge = 'custom';
}

return [
    IconSize::ExtraExtraLarge,
    Size::ExtraExtraLarge->value,
    \Filament\Support\Enums\IconSize::ExtraExtraLarge,
    IconSize::TwoExtraLarge,
    IconSize::ExtraLarge,
    CustomIconSize::ExtraExtraLarge,
    'Filament\Support\Enums\IconSize::ExtraExtraLarge',
    '2xl',
];
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedEnumConstants;

use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\IconSize as Size;

enum CustomIconSize: string
{
    case ExtraExtraLarge = 'custom';
}

return [
    Size::TwoExtraLarge,
    Size::TwoExtraLarge->value,
    Size::TwoExtraLarge,
    Size::TwoExtraLarge,
    Size::ExtraLarge,
    CustomIconSize::ExtraExtraLarge,
    'Filament\Support\Enums\IconSize::ExtraExtraLarge',
    '2xl',
];
