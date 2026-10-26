<?php

namespace Filament\Tests\Upgrade\Fixtures\LegacyLazyLoading;

use Filament\Widgets\Widget;

class LegacyWidget extends Widget
{
    protected static bool $isLazy = false;

    public static function isLazy(): bool
    {
        return parent::isLazy() || static::$isLazy;
    }
}

Widget::isLazy();
LegacyWidget::isLazy();
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\LegacyLazyLoading;

use Filament\Widgets\Widget;

class LegacyWidget extends Widget
{
    protected static ?bool $isLazyLoaded = false;

    public static function isLazyLoaded(): bool
    {
        return parent::isLazyLoaded() || (static::$isLazyLoaded ?? true);
    }
}

Widget::isLazyLoaded();
LegacyWidget::isLazyLoaded();
