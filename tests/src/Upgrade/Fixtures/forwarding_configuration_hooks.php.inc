<?php

namespace Filament\Tests\Upgrade\Fixtures\ForwardingConfigurationHooks;

use Filament\Actions\Action;
use Filament\Actions\ExportAction;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Commands\FileGenerators\Resources\Pages\ResourceManageRelatedRecordsPageClassGenerator;
use Filament\Commands\FileGenerators\Resources\RelationManagerClassGenerator;
use Filament\Commands\FileGenerators\Resources\ResourceClassGenerator;
use Filament\Commands\FileGenerators\Resources\Schemas\ResourceTableClassGenerator;

class ExporterDefaults extends Exporter
{
    public static function getColumns(): array
    {
        return [];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return '';
    }
}

class DiskExporter extends ExporterDefaults
{
    public function getFileDisk(): string
    {
        return 'root:' . parent::getFileDisk();
    }

    public function applicationDisk(): string
    {
        return $this->getFileDisk();
    }

    public function defaultDisk(): string
    {
        return parent::getFileDisk();
    }
}

class ChildExporter extends DiskExporter
{
    public function getFileDisk(): string
    {
        return 'child:' . parent::getFileDisk();
    }
}

trait DiskHook
{
    public function getFileDisk(): string
    {
        return 'trait:' . parent::getFileDisk();
    }
}

trait NestedDiskHook
{
    use DiskHook;
}

class TraitExporter extends ExporterDefaults
{
    use NestedDiskHook;
}

class ChildTraitExporter extends TraitExporter
{
    public function getFileDisk(): string
    {
        return 'child:' . parent::getFileDisk();
    }
}

class UnrelatedDiskParent
{
    public function getFileDisk(): string
    {
        return 'unrelated';
    }
}

class UnrelatedDiskConsumer extends UnrelatedDiskParent
{
    use DiskHook;
}

trait TableHooks
{
    public function getTableActions(): array
    {
        return [...parent::getTableActions(), Action::class];
    }

    public function outputTableActions(): string
    {
        return '// record-hook' . PHP_EOL . parent::outputTableActions();
    }

    public function outputTableMethodBulkActions(): string
    {
        return '// bulk-hook' . PHP_EOL . parent::outputTableMethodBulkActions();
    }
}

class TableGenerator extends ResourceTableClassGenerator
{
    use TableHooks;
}

class ResourceGenerator extends ResourceClassGenerator
{
    use TableHooks;
}

class RelationGenerator extends RelationManagerClassGenerator
{
    use TableHooks;
}

class RelatedRecordsGenerator extends ResourceManageRelatedRecordsPageClassGenerator
{
    use TableHooks;
}

class ChildTableGenerator extends TableGenerator
{
    public function getTableActions(): array
    {
        return [...parent::getTableActions(), ExportAction::class];
    }

    public function outputTableActions(): string
    {
        return '// child-record' . PHP_EOL . parent::outputTableActions();
    }

    public function outputTableMethodBulkActions(): string
    {
        return '// child-bulk' . PHP_EOL . parent::outputTableMethodBulkActions();
    }
}

function disk(Exporter $exporter): string
{
    return $exporter->getFileDisk();
}

function nullableDisk(?Exporter $exporter): ?string
{
    return $exporter?->getFileDisk();
}

function customCallback(DiskExporter $exporter): callable
{
    return [$exporter, 'getFileDisk'];
}

abstract class DirectGeneratorBase extends \Filament\Support\Commands\FileGenerators\ClassGenerator
{
    public function getNamespace(): string
    {
        return 'App\\Generated';
    }

    public function getBasename(): string
    {
        return 'DirectTable';
    }

    public function hasViewOperation(): bool
    {
        return false;
    }

    public function isGenerated(): bool
    {
        return false;
    }

    public function isSoftDeletable(): bool
    {
        return false;
    }

    public function isSimple(): bool
    {
        return false;
    }

    public function getRecordTitleAttribute(): ?string
    {
        return null;
    }
}

class DirectGenerator extends DirectGeneratorBase
{
    use \Filament\Commands\FileGenerators\Resources\Concerns\CanGenerateResourceTables;

    public function getTableActions(): array
    {
        return [Action::class];
    }

    public function outputTableActions(): string
    {
        return '// direct-record';
    }

    public function outputTableMethodBulkActions(): string
    {
        return '// direct-bulk';
    }
}

class ChildDirectGenerator extends DirectGenerator
{
    public function getTableActions(): array
    {
        return [...parent::getTableActions(), ExportAction::class];
    }

    public function outputTableActions(): string
    {
        return '// child' . PHP_EOL . parent::outputTableActions();
    }

    public function outputTableMethodBulkActions(): string
    {
        return '// child' . PHP_EOL . parent::outputTableMethodBulkActions();
    }
}

class DirectDefaults extends DirectGeneratorBase
{
    use \Filament\Commands\FileGenerators\Resources\Concerns\CanGenerateResourceTables;
}

function directOutputs(DirectDefaults $generator): array
{
    return [$generator->getTableActions(), $generator->outputTableActions(), $generator->outputTableMethodBulkActions()];
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\ForwardingConfigurationHooks;

use Filament\Support\Commands\FileGenerators\ClassGenerator;
use Filament\Commands\FileGenerators\Resources\Concerns\CanGenerateResourceTables;
use Filament\Actions\Action;
use Filament\Actions\ExportAction;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Commands\FileGenerators\Resources\Pages\ResourceManageRelatedRecordsPageClassGenerator;
use Filament\Commands\FileGenerators\Resources\RelationManagerClassGenerator;
use Filament\Commands\FileGenerators\Resources\ResourceClassGenerator;
use Filament\Commands\FileGenerators\Resources\Schemas\ResourceTableClassGenerator;

class ExporterDefaults extends Exporter
{
    public static function getColumns(): array
    {
        return [];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return '';
    }
}

class DiskExporter extends ExporterDefaults
{
    public function getFileDisk(): string
    {
        return 'root:' . parent::getFileDiskName();
    }

    public function applicationDisk(): string
    {
        return $this->getFileDisk();
    }

    public function defaultDisk(): string
    {
        return parent::getFileDiskName();
    }
    public function getFileDiskName(): string
    {
        return $this->getFileDisk();
    }
}

class ChildExporter extends DiskExporter
{
    public function getFileDisk(): string
    {
        return 'child:' . parent::getFileDisk();
    }
}

trait DiskHook
{
    public function getFileDisk(): string
    {
        return 'trait:' . parent::getFileDisk();
    }
}

trait NestedDiskHook
{
    use DiskHook;
}

class TraitExporter extends ExporterDefaults
{
    use NestedDiskHook;
    public function getFileDisk(): string
    {
        return 'trait:' . parent::getFileDiskName();
    }
    public function getFileDiskName(): string
    {
        return $this->getFileDisk();
    }
}

class ChildTraitExporter extends TraitExporter
{
    public function getFileDisk(): string
    {
        return 'child:' . parent::getFileDisk();
    }
}

class UnrelatedDiskParent
{
    public function getFileDisk(): string
    {
        return 'unrelated';
    }
}

class UnrelatedDiskConsumer extends UnrelatedDiskParent
{
    use DiskHook;
}

trait TableHooks
{
    public function getTableActions(): array
    {
        return [...parent::getTableActions(), Action::class];
    }

    public function outputTableActions(): string
    {
        return '// record-hook' . PHP_EOL . parent::outputTableActions();
    }

    public function outputTableMethodBulkActions(): string
    {
        return '// bulk-hook' . PHP_EOL . parent::outputTableMethodBulkActions();
    }
}

class TableGenerator extends ResourceTableClassGenerator
{
    use TableHooks;
    public function getTableActions(): array
    {
        return [...parent::getTableRecordActions(), Action::class];
    }
    public function getTableRecordActions(): array
    {
        return $this->getTableActions();
    }
    public function outputTableActions(): string
    {
        return '// record-hook' . \PHP_EOL . parent::outputTableRecordActions();
    }
    public function outputTableRecordActions(): string
    {
        return $this->outputTableActions();
    }
    public function outputTableMethodBulkActions(): string
    {
        return '// bulk-hook' . \PHP_EOL . parent::outputTableBulkActions();
    }
    public function outputTableBulkActions(): string
    {
        return $this->outputTableMethodBulkActions();
    }
}

class ResourceGenerator extends ResourceClassGenerator
{
    use TableHooks;
    public function getTableActions(): array
    {
        return [...parent::getTableRecordActions(), Action::class];
    }
    public function getTableRecordActions(): array
    {
        return $this->getTableActions();
    }
    public function outputTableActions(): string
    {
        return '// record-hook' . \PHP_EOL . parent::outputTableRecordActions();
    }
    public function outputTableRecordActions(): string
    {
        return $this->outputTableActions();
    }
    public function outputTableMethodBulkActions(): string
    {
        return '// bulk-hook' . \PHP_EOL . parent::outputTableBulkActions();
    }
    public function outputTableBulkActions(): string
    {
        return $this->outputTableMethodBulkActions();
    }
}

class RelationGenerator extends RelationManagerClassGenerator
{
    use TableHooks;
    public function getTableActions(): array
    {
        return [...parent::getTableRecordActions(), Action::class];
    }
    public function getTableRecordActions(): array
    {
        return $this->getTableActions();
    }
    public function outputTableActions(): string
    {
        return '// record-hook' . \PHP_EOL . parent::outputTableRecordActions();
    }
    public function outputTableRecordActions(): string
    {
        return $this->outputTableActions();
    }
    public function outputTableMethodBulkActions(): string
    {
        return '// bulk-hook' . \PHP_EOL . parent::outputTableBulkActions();
    }
    public function outputTableBulkActions(): string
    {
        return $this->outputTableMethodBulkActions();
    }
}

class RelatedRecordsGenerator extends ResourceManageRelatedRecordsPageClassGenerator
{
    use TableHooks;
    public function getTableActions(): array
    {
        return [...parent::getTableRecordActions(), Action::class];
    }
    public function getTableRecordActions(): array
    {
        return $this->getTableActions();
    }
    public function outputTableActions(): string
    {
        return '// record-hook' . \PHP_EOL . parent::outputTableRecordActions();
    }
    public function outputTableRecordActions(): string
    {
        return $this->outputTableActions();
    }
    public function outputTableMethodBulkActions(): string
    {
        return '// bulk-hook' . \PHP_EOL . parent::outputTableBulkActions();
    }
    public function outputTableBulkActions(): string
    {
        return $this->outputTableMethodBulkActions();
    }
}

class ChildTableGenerator extends TableGenerator
{
    public function getTableActions(): array
    {
        return [...parent::getTableActions(), ExportAction::class];
    }

    public function outputTableActions(): string
    {
        return '// child-record' . PHP_EOL . parent::outputTableActions();
    }

    public function outputTableMethodBulkActions(): string
    {
        return '// child-bulk' . PHP_EOL . parent::outputTableMethodBulkActions();
    }
}

function disk(Exporter $exporter): string
{
    return $exporter->getFileDiskName();
}

function nullableDisk(?Exporter $exporter): ?string
{
    return $exporter?->getFileDiskName();
}

function customCallback(DiskExporter $exporter): callable
{
    return [$exporter, 'getFileDisk'];
}

abstract class DirectGeneratorBase extends ClassGenerator
{
    public function getNamespace(): string
    {
        return 'App\\Generated';
    }

    public function getBasename(): string
    {
        return 'DirectTable';
    }

    public function hasViewOperation(): bool
    {
        return false;
    }

    public function isGenerated(): bool
    {
        return false;
    }

    public function isSoftDeletable(): bool
    {
        return false;
    }

    public function isSimple(): bool
    {
        return false;
    }

    public function getRecordTitleAttribute(): ?string
    {
        return null;
    }
}

class DirectGenerator extends DirectGeneratorBase
{
    use CanGenerateResourceTables;

    public function getTableActions(): array
    {
        return [Action::class];
    }

    public function outputTableActions(): string
    {
        return '// direct-record';
    }

    public function outputTableMethodBulkActions(): string
    {
        return '// direct-bulk';
    }
    public function getTableRecordActions(): array
    {
        return $this->getTableActions();
    }
    public function outputTableRecordActions(): string
    {
        return $this->outputTableActions();
    }
    public function outputTableBulkActions(): string
    {
        return $this->outputTableMethodBulkActions();
    }
}

class ChildDirectGenerator extends DirectGenerator
{
    public function getTableActions(): array
    {
        return [...parent::getTableActions(), ExportAction::class];
    }

    public function outputTableActions(): string
    {
        return '// child' . PHP_EOL . parent::outputTableActions();
    }

    public function outputTableMethodBulkActions(): string
    {
        return '// child' . PHP_EOL . parent::outputTableMethodBulkActions();
    }
}

class DirectDefaults extends DirectGeneratorBase
{
    use CanGenerateResourceTables;
}

function directOutputs(DirectDefaults $generator): array
{
    return [$generator->getTableRecordActions(), $generator->outputTableRecordActions(), $generator->outputTableBulkActions()];
}
