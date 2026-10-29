<?php

namespace Filament\Tests\Upgrade\Fixtures\SchemaCrossFileHook;

use Filament\Forms\Components\TextInput;
use Filament\Tests\Upgrade\Fixtures\SchemaHooks\ParentExporter;

abstract class ChildExporter extends ParentExporter
{
    public static function getOptionsFormComponents(): array
    {
        return [...parent::getOptionsFormComponents(), TextInput::make('child_option')];
    }
}

function inspect(ChildExporter $exporter): void
{
    $exporter::getOptionsFormComponents();
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\SchemaCrossFileHook;

use Filament\Forms\Components\TextInput;
use Filament\Tests\Upgrade\Fixtures\SchemaHooks\ParentExporter;

abstract class ChildExporter extends ParentExporter
{
    public static function getOptionsSchemaComponents(): array
    {
        return [...parent::getOptionsFormComponents(), TextInput::make('child_option')];
    }
}

function inspect(ChildExporter $exporter): void
{
    $exporter::getOptionsSchemaComponents();
}
