<?php

namespace Filament\Tests\Upgrade\Fixtures\ApiClasses;

use Filament\Actions\Exports\Jobs\ExportCompletion;
use Filament\Schemas\Components\StateCasts\OptionsArrayStateCast;
use Filament\Support\Contracts\HasColor as EnumColor;
use Filament\Support\View\Components\Contracts\HasColor;

abstract class Palette implements HasColor {}

class SelectedOptions extends OptionsArrayStateCast {}

function job(ExportCompletion $job, EnumColor $color): ExportCompletion
{
    return $job;
}

function bind(): void
{
    app()->bind(ExportCompletion::class, ExportCompletion::class);
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\ApiClasses;

use Filament\Support\View\Components\Contracts\HasColorMap;
use Filament\Schemas\Components\StateCasts\OptionArrayStateCast;
use Filament\Actions\Exports\Jobs\CompleteExport;
use Filament\Support\Contracts\HasColor as EnumColor;

abstract class Palette implements HasColorMap {}

class SelectedOptions extends OptionArrayStateCast {}

function job(CompleteExport $job, EnumColor $color): CompleteExport
{
    return $job;
}

function bind(): void
{
    app()->bind(CompleteExport::class, CompleteExport::class);
}
