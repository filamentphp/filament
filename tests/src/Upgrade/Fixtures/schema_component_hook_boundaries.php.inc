<?php

namespace Filament\Tests\Upgrade\Fixtures\SchemaComponentHookBoundaries;

use Closure;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Imports\Importer;
use Filament\Forms\Components\TextInput;
use Filament\QueryBuilder\Constraints\Operators\Operator;
use Filament\Tests\Upgrade\Fixtures\SchemaHooks\ParentExporter;

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
        return [...parent::getOptionsFormComponents(), TextInput::make('trait_option')];
    }

    public static function parentOptions(): Closure
    {
        return parent::getOptionsFormComponents(...);
    }
}

abstract class TraitImporter extends Importer
{
    use CustomOptions {
        getOptionsFormComponents as options;
        parentOptions as parentOptionsAlias;
    }
}

abstract class ParentTraitExporter extends ParentExporter
{
    use CustomOptions {
        getOptionsFormComponents as options;
    }
}

abstract class DualParentExporter extends Exporter
{
    public static function getOptionsFormComponents(): array
    {
        return [TextInput::make('legacy_parent_option')];
    }

    public static function getOptionsSchemaComponents(): array
    {
        return [TextInput::make('canonical_parent_option')];
    }
}

abstract class DualParentTraitExporter extends DualParentExporter
{
    use CustomOptions {
        getOptionsFormComponents as options;
    }
}

abstract class ChildTraitImporter extends TraitImporter
{
    public static function getOptionsFormComponents(): array
    {
        return [...parent::getOptionsFormComponents(), TextInput::make('child_trait_option')];
    }
}

abstract class DualChildTraitImporter extends TraitImporter
{
    public static function getOptionsFormComponents(): array
    {
        return parent::getOptionsSchemaComponents();
    }

    public static function getOptionsSchemaComponents(): array
    {
        return [TextInput::make('dual_child_option')];
    }
}

class UnrelatedOptionsParent
{
    public static function getOptionsFormComponents(): array
    {
        return [TextInput::make('unrelated_parent_option')];
    }
}

class UnrelatedTraitConsumer extends UnrelatedOptionsParent
{
    use CustomOptions {
        getOptionsFormComponents as options;
        parentOptions as parentOptionsAlias;
    }
}

class UnrelatedGrandchild extends UnrelatedTraitConsumer {}

abstract class CanonicalTraitImporter extends Importer
{
    use CustomOptions;

    public static function getOptionsSchemaComponents(): array
    {
        return [TextInput::make('canonical_option')];
    }
}

abstract class AliasedTraitImporter extends Importer
{
    use CustomOptions {
        getOptionsFormComponents as getOptionsSchemaComponents;
    }
}

trait ForwardingOptions
{
    public static function getOptionsFormComponents(): array
    {
        return static::getOptionsSchemaComponents();
    }
}

abstract class ForwardingTraitImporter extends Importer
{
    use ForwardingOptions;
}

abstract class ForwardingParentImporter extends Importer
{
    public static function getOptionsFormComponents(): array
    {
        return static::getOptionsSchemaComponents();
    }
}

abstract class ForwardingParentTraitImporter extends ForwardingParentImporter
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
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\SchemaComponentHookBoundaries;

use Closure;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Imports\Importer;
use Filament\Forms\Components\TextInput;
use Filament\QueryBuilder\Constraints\Operators\Operator;
use Filament\Tests\Upgrade\Fixtures\SchemaHooks\ParentExporter;

abstract class DualExporter extends Exporter
{
    public static function getOptionsFormComponents(): array
    {
        return parent::getOptionsSchemaComponents();
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
        return [...(\is_a(parent::class, Importer::class, true) || \is_a(parent::class, Exporter::class, true)) && !\method_exists(parent::class, 'getOptionsFormComponents') ? parent::getOptionsSchemaComponents() : parent::{'getOptionsFormComponents'}(), TextInput::make('trait_option')];
    }

    public static function parentOptions(): Closure
    {
        return (\is_a(parent::class, Importer::class, true) || \is_a(parent::class, Exporter::class, true)) && !\method_exists(parent::class, 'getOptionsFormComponents') ? parent::getOptionsSchemaComponents(...) : parent::{'getOptionsFormComponents'}(...);
    }
}

abstract class TraitImporter extends Importer
{
    use CustomOptions {
        getOptionsFormComponents as options;
        parentOptions as parentOptionsAlias;
    }
    public static function getOptionsSchemaComponents(): array
    {
        return self::getOptionsFormComponents();
    }
}

abstract class ParentTraitExporter extends ParentExporter
{
    use CustomOptions {
        getOptionsFormComponents as options;
    }
    public static function getOptionsSchemaComponents(): array
    {
        return self::getOptionsFormComponents();
    }
}

abstract class DualParentExporter extends Exporter
{
    public static function getOptionsFormComponents(): array
    {
        return [TextInput::make('legacy_parent_option')];
    }

    public static function getOptionsSchemaComponents(): array
    {
        return [TextInput::make('canonical_parent_option')];
    }
}

abstract class DualParentTraitExporter extends DualParentExporter
{
    use CustomOptions {
        getOptionsFormComponents as options;
    }
}

abstract class ChildTraitImporter extends TraitImporter
{
    public static function getOptionsSchemaComponents(): array
    {
        return [...parent::getOptionsFormComponents(), TextInput::make('child_trait_option')];
    }
}

abstract class DualChildTraitImporter extends TraitImporter
{
    public static function getOptionsFormComponents(): array
    {
        return parent::getOptionsSchemaComponents();
    }

    public static function getOptionsSchemaComponents(): array
    {
        return [TextInput::make('dual_child_option')];
    }
}

class UnrelatedOptionsParent
{
    public static function getOptionsFormComponents(): array
    {
        return [TextInput::make('unrelated_parent_option')];
    }
}

class UnrelatedTraitConsumer extends UnrelatedOptionsParent
{
    use CustomOptions {
        getOptionsFormComponents as options;
        parentOptions as parentOptionsAlias;
    }
}

class UnrelatedGrandchild extends UnrelatedTraitConsumer {}

abstract class CanonicalTraitImporter extends Importer
{
    use CustomOptions;

    public static function getOptionsSchemaComponents(): array
    {
        return [TextInput::make('canonical_option')];
    }
}

abstract class AliasedTraitImporter extends Importer
{
    use CustomOptions {
        getOptionsFormComponents as getOptionsSchemaComponents;
    }
}

trait ForwardingOptions
{
    public static function getOptionsFormComponents(): array
    {
        return static::getOptionsSchemaComponents();
    }
}

abstract class ForwardingTraitImporter extends Importer
{
    use ForwardingOptions;
}

abstract class ForwardingParentImporter extends Importer
{
    public static function getOptionsFormComponents(): array
    {
        return static::getOptionsSchemaComponents();
    }
}

abstract class ForwardingParentTraitImporter extends ForwardingParentImporter
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
