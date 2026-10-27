<?php

namespace Filament\Tests\Upgrade\Fixtures\SchemaHookUnions;

use Filament\Actions\Exports\Exporter;
use Filament\QueryBuilder\Constraints\Operators\Operator;

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

class DualOperator extends Operator
{
    public function getFormSchema(): array
    {
        return ['old'];
    }

    public function getSchemaComponents(): array
    {
        return ['new'];
    }
}

function normalized(Exporter | DualExporter $exporter, Operator | DualOperator $operator): void
{
    $exporter::getOptionsFormComponents();
    $operator->getFormSchema();
}
