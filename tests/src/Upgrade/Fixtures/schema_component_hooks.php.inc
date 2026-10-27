<?php

namespace Filament\Tests\Upgrade\Fixtures\SchemaComponentHooks;

use Filament\Actions\Exports\Exporter;
use Filament\Actions\Imports\Importer;
use Filament\QueryBuilder\Constraints\Operators\Operator;
use Filament\QueryBuilder\Constraints\TextConstraint\Operators\ContainsOperator;

abstract class CustomImporter extends Importer
{
    public static function getOptionsFormComponents(): array
    {
        return [...parent::getOptionsFormComponents(), 'import'];
    }

    public static function inspect(): array
    {
        return [...self::getOptionsFormComponents(), ...static::getOptionsFormComponents()];
    }
}

abstract class CustomExporter extends Exporter
{
    public static function getOptionsFormComponents(): array
    {
        return [...parent::getOptionsFormComponents(), 'export'];
    }
}

class CustomOperator extends ContainsOperator
{
    public function getFormSchema(): array
    {
        return [...parent::getFormSchema(), ...ContainsOperator::getFormSchema(), 'operator'];
    }

    public function inspect(): array
    {
        return [...$this->getFormSchema(), ...self::getFormSchema(), ...static::getFormSchema()];
    }
}

/** @param class-string<Importer> $importerClass */
function inspect(Importer | Exporter $handler, ?Operator $operator, CustomOperator $custom, string $importerClass): void
{
    $importerClass::getOptionsFormComponents();
    $handler::getOptionsFormComponents();
    $handler->getOptionsFormComponents();
    Importer::getOptionsFormComponents();
    CustomExporter::getOptionsFormComponents();
    $operator?->getFormSchema();
    $custom->getFormSchema();
    $callback = $custom->getFormSchema(...);
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\SchemaComponentHooks;

use Filament\Actions\Exports\Exporter;
use Filament\Actions\Imports\Importer;
use Filament\QueryBuilder\Constraints\Operators\Operator;
use Filament\QueryBuilder\Constraints\TextConstraint\Operators\ContainsOperator;

abstract class CustomImporter extends Importer
{
    public static function getOptionsSchemaComponents(): array
    {
        return [...parent::getOptionsSchemaComponents(), 'import'];
    }

    public static function inspect(): array
    {
        return [...self::getOptionsSchemaComponents(), ...static::getOptionsSchemaComponents()];
    }
}

abstract class CustomExporter extends Exporter
{
    public static function getOptionsSchemaComponents(): array
    {
        return [...parent::getOptionsSchemaComponents(), 'export'];
    }
}

class CustomOperator extends ContainsOperator
{
    public function getSchemaComponents(): array
    {
        return [...parent::getSchemaComponents(), ...ContainsOperator::getSchemaComponents(), 'operator'];
    }

    public function inspect(): array
    {
        return [...$this->getSchemaComponents(), ...self::getSchemaComponents(), ...static::getSchemaComponents()];
    }
}

/** @param class-string<Importer> $importerClass */
function inspect(Importer | Exporter $handler, ?Operator $operator, CustomOperator $custom, string $importerClass): void
{
    $importerClass::getOptionsSchemaComponents();
    $handler::getOptionsSchemaComponents();
    $handler->getOptionsSchemaComponents();
    Importer::getOptionsSchemaComponents();
    CustomExporter::getOptionsSchemaComponents();
    $operator?->getSchemaComponents();
    $custom->getSchemaComponents();
    $callback = $custom->getSchemaComponents(...);
}
