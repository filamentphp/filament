<?php

namespace Filament\Tests\Upgrade\Fixtures\SchemaCrossFileHook;

use Filament\Tests\Upgrade\Fixtures\SchemaHooks\ParentExporter;

abstract class ChildExporter extends ParentExporter
{
    public static function getOptionsFormComponents(): array
    {
        return [...parent::getOptionsFormComponents(), 'child'];
    }
}

function inspect(ChildExporter $exporter): void
{
    $exporter::getOptionsFormComponents();
}
