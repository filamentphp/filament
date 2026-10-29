<?php

namespace Filament\Tests\Upgrade\Fixtures\LazyLoadingReferenceTarget;

use Filament\Widgets\Widget;

class ReferenceTargetWidget extends Widget
{
    protected static bool $isLazy = false;

    public static function isLazy(): bool
    {
        return static::$isLazy;
    }

    public function configure(bool $configuration): void
    {
        static::$isLazy =& $configuration;
    }
}

ReferenceTargetWidget::isLazy();
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\LazyLoadingReferenceTarget;

use Filament\Widgets\Widget;

class ReferenceTargetWidget extends Widget
{
    protected static ?bool $isLazyLoaded = false;

    public static function isLazyLoaded(): bool
    {
        return static::$isLazyLoaded ?? true;
    }

    public function configure(bool $configuration): void
    {
        static::$isLazyLoaded =& $configuration;
    }
}

ReferenceTargetWidget::isLazyLoaded();
