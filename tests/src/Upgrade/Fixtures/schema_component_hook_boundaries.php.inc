<?php

namespace Filament\Tests\Upgrade\Fixtures\SchemaComponentHookBoundaries;

use Filament\Actions\Exports\Exporter;
use Filament\Actions\Imports\Importer;
use Filament\QueryBuilder\Constraints\Operators\Operator;

abstract class DualExporter extends Exporter
{
    public static function getOptionsFormComponents(): array
    {
        return parent::getOptionsFormComponents();
    }

    public static function getOptionsSchemaComponents(): array
    {
        return static::getOptionsFormComponents();
    }
}

trait CustomOptions
{
    public static function getOptionsFormComponents(): array
    {
        return [];
    }
}

abstract class TraitImporter extends Importer
{
    use CustomOptions {
        getOptionsFormComponents as options;
    }
}

class ForwardingOperator extends Operator
{
    public function getFormSchema(): array
    {
        return $this->getSchemaComponents();
    }
}

class Unrelated
{
    public static function getOptionsFormComponents(): array
    {
        return [];
    }

    public function getFormSchema(): array
    {
        return [];
    }
}

function inspect(DualExporter $dual, TraitImporter $trait, ForwardingOperator $forwarding, Importer | Unrelated $mixed, mixed $unknown, string $method): void
{
    $dual::getOptionsFormComponents();
    $trait::getOptionsFormComponents();
    $forwarding->getFormSchema();
    $mixed::getOptionsFormComponents();
    $unknown->getFormSchema();
    $dual::$method();
    Operator::getFormSchema();
    $reference = [Importer::class, 'getOptionsFormComponents'];
}
