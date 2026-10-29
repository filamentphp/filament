<?php

namespace Filament\Tests\Upgrade\Fixtures\SchemaHooks;

use Filament\Actions\Exports\Exporter;
use Filament\Forms\Components\TextInput;

abstract class ParentExporter extends Exporter
{
    public static function getOptionsFormComponents(): array
    {
        return [TextInput::make('parent_option')];
    }
}
