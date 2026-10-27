<?php

namespace Filament\Tests\Upgrade\Fixtures\GeneratorAndDashboardManualBoundaries;

use Filament\Commands\FileGenerators\Resources\Schemas\ResourceTableClassGenerator;
use Filament\Forms\Commands\FileGenerators\RichContentCustomBlockClassGenerator;
use Filament\Pages\Dashboard;
use Filament\Pages\Dashboard\Concerns\HasFilters;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Livewire\Component;
use Nette\PhpGenerator\Method;

class CustomGetter extends ResourceTableClassGenerator
{
    public function getTableActions(): array
    {
        return parent::getTableActions();
    }
}

class CustomOutput extends ResourceTableClassGenerator
{
    public function outputTableRecordActions(): string
    {
        return 'custom';
    }
}

class InheritedOutput extends CustomOutput {}

trait CustomBulkOutput
{
    public function outputTableMethodBulkActions(): string
    {
        return 'custom';
    }
}

class CustomBulkGenerator extends ResourceTableClassGenerator
{
    use CustomBulkOutput;
}

function manual(CustomGetter $getter, InheritedOutput $output, CustomBulkGenerator $bulk, ResourceTableClassGenerator $base, object $unrelated, ResourceTableClassGenerator | CustomGetter $union): void
{
    $getter->getTableActions();
    $getter->outputTableActions();
    $output->outputTableActions();
    $bulk->outputTableMethodBulkActions();
    $base->getTableActions();
    $union->outputTableActions();
    $unrelated->getTableActions();
    $unrelated->outputTableActions();
    $unrelated->outputTableMethodBulkActions();
    $base->{'outputTableActions'}();
    $callback = [$base, 'outputTableActions'];
}

class CustomPreview extends RichContentCustomBlockClassGenerator
{
    protected function configureToPreviewMethod(Method $method): void
    {
        parent::configureToPreviewMethod(method: $method);
        $this->configureToPreviewMethod(method: $method);
    }

    protected function configureToPreviewHtmlMethod(Method $method): void {}
}

class CustomDashboard extends Dashboard
{
    use HasFilters;

    protected function normalizeTableFilterValuesFromQueryString(array &$data): void
    {
        parent::normalizeTableFilterValuesFromQueryString(data: $data);
    }

    public function normalize(array &$data): void
    {
        $this->normalizeTableFilterValuesFromQueryString(data: $data);
    }
}

abstract class TableComponent extends Component implements HasTable
{
    use InteractsWithTable;

    public function normalize(array &$data): void
    {
        $this->normalizeTableFilterValuesFromQueryString(data: $data);
    }
}

class DirectFilterConsumer
{
    use HasFilters;

    public function normalize(array &$data): void
    {
        $this->normalizeTableFilterValuesFromQueryString(data: $data);
    }
}

function inaccessible(RichContentCustomBlockClassGenerator $generator, Dashboard $dashboard, Method $method, array &$data): void
{
    $generator->configureToPreviewMethod(method: $method);
    $dashboard->normalizeTableFilterValuesFromQueryString(data: $data);
}
