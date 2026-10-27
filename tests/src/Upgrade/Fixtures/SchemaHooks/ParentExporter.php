<?php

namespace Filament\Tests\Upgrade\Fixtures\SchemaHooks;

use Filament\Actions\Exports\Exporter;

abstract class ParentExporter extends Exporter
{
    public static function getOptionsFormComponents(): array
    {
        return ['parent'];
    }
}
