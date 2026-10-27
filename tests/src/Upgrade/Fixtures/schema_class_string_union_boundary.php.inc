<?php

namespace Filament\Tests\Upgrade\Fixtures\SchemaClassStringUnionBoundary;

use Filament\Actions\Exports\Exporter;

abstract class DualExporter extends Exporter
{
    public static function getOptionsFormComponents(): array
    {
        return ['old'];
    }

    public static function getOptionsSchemaComponents(): array
    {
        return ['new'];
    }
}

/**
 * @param class-string<Exporter> | class-string<DualExporter> $exporterClass
 */
function inspect(string $exporterClass): array
{
    return $exporterClass::getOptionsFormComponents();
}
