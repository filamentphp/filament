<?php

namespace Filament\Tests\Upgrade\Fixtures\GeneratorAndDashboardMethods;

use Filament\Commands\FileGenerators\Resources\Pages\ResourceManageRelatedRecordsPageClassGenerator;
use Filament\Commands\FileGenerators\Resources\RelationManagerClassGenerator;
use Filament\Commands\FileGenerators\Resources\ResourceClassGenerator;
use Filament\Commands\FileGenerators\Resources\Schemas\ResourceTableClassGenerator;
use Filament\Forms\Commands\FileGenerators\RichContentCustomBlockClassGenerator;
use Filament\Pages\Dashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Nette\PhpGenerator\Method;

function migrate(ResourceClassGenerator $resource, ResourceTableClassGenerator $table, RelationManagerClassGenerator $manager, ResourceManageRelatedRecordsPageClassGenerator $page, ?ResourceTableClassGenerator $nullable): void
{
    $resource->getTableActions();
    $resource->outputTableActions();
    $resource->outputTableMethodBulkActions();
    $table->getTableActions();
    $table->outputTableActions();
    $table->outputTableMethodBulkActions();
    $manager->getTableActions();
    $manager->outputTableActions();
    $manager->outputTableMethodBulkActions();
    $page->getTableActions();
    $page->outputTableActions();
    $page->outputTableMethodBulkActions();
    $nullable?->outputTableActions();
    $callback = $table->getTableActions(...);
    $table->outputTableRecordActions();
}

class PreviewGenerator extends RichContentCustomBlockClassGenerator
{
    public function inspect(Method $method, array $arguments): void
    {
        $this->configureToPreviewMethod(method: $method);
        $this->configureToPreviewMethod(...$arguments);
        $callback = $this->configureToPreviewMethod(...);
    }
}

class FilteredDashboard extends Dashboard
{
    use HasFiltersForm;

    public function normalize(array &$data, array $arguments): void
    {
        $this->normalizeTableFilterValuesFromQueryString(data: $data);
        $this->normalizeTableFilterValuesFromQueryString(...$arguments);
        $callback = $this->normalizeTableFilterValuesFromQueryString(...);
        $callback(data: $data);
    }
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\GeneratorAndDashboardMethods;

use Filament\Commands\FileGenerators\Resources\Pages\ResourceManageRelatedRecordsPageClassGenerator;
use Filament\Commands\FileGenerators\Resources\RelationManagerClassGenerator;
use Filament\Commands\FileGenerators\Resources\ResourceClassGenerator;
use Filament\Commands\FileGenerators\Resources\Schemas\ResourceTableClassGenerator;
use Filament\Forms\Commands\FileGenerators\RichContentCustomBlockClassGenerator;
use Filament\Pages\Dashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Nette\PhpGenerator\Method;

function migrate(ResourceClassGenerator $resource, ResourceTableClassGenerator $table, RelationManagerClassGenerator $manager, ResourceManageRelatedRecordsPageClassGenerator $page, ?ResourceTableClassGenerator $nullable): void
{
    $resource->getTableRecordActions();
    $resource->outputTableRecordActions();
    $resource->outputTableBulkActions();
    $table->getTableRecordActions();
    $table->outputTableRecordActions();
    $table->outputTableBulkActions();
    $manager->getTableRecordActions();
    $manager->outputTableRecordActions();
    $manager->outputTableBulkActions();
    $page->getTableRecordActions();
    $page->outputTableRecordActions();
    $page->outputTableBulkActions();
    $nullable?->outputTableRecordActions();
    $callback = $table->getTableRecordActions(...);
    $table->outputTableRecordActions();
}

class PreviewGenerator extends RichContentCustomBlockClassGenerator
{
    public function inspect(Method $method, array $arguments): void
    {
        $this->configureToPreviewHtmlMethod(method: $method);
        $this->configureToPreviewHtmlMethod(...$arguments);
        $callback = $this->configureToPreviewHtmlMethod(...);
    }
}

class FilteredDashboard extends Dashboard
{
    use HasFiltersForm;

    public function normalize(array &$data, array $arguments): void
    {
        $this->normalizeFilterValuesFromQueryString(data: $data);
        $this->normalizeFilterValuesFromQueryString(...$arguments);
        $callback = $this->normalizeFilterValuesFromQueryString(...);
        $callback(data: $data);
    }
}
