<?php

namespace Filament\Tests\Upgrade\Fixtures\UnresolvedTypedChartTrait;

class Sales extends \Filament\Widgets\BarChartWidget
{
    use MissingTrait;
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\UnresolvedTypedChartTrait;

use Filament\Widgets\BarChartWidget;

class Sales extends BarChartWidget
{
    use MissingTrait;
}
