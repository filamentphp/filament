<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedComponentApiNames;

use Filament\Actions\Action;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Imports\Events\ImportChunkProcessed;
use Filament\Actions\Imports\ImportColumn;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor\MentionProvider;
use Filament\Forms\Components\RichEditor\RichContentAttribute;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Infolists\Components\Entry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\FusedGroup;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Table;

function configure(Action $action, ExportAction $exportAction, ExportBulkAction $bulkAction, Exporter $exporter, ImportColumn $importColumn, ImportChunkProcessed $event, Field $field, FusedGroup $group, Builder $builder, Repeater $repeater, MentionProvider $provider, RichContentAttribute $content, SpatieMediaLibraryFileUpload $upload, Entry $entry, Column $column, ExportColumn $exportColumn, Table $table, Summarizer $summarizer, array $arguments): void
{
    $action->hasCustomLivewireClickHandler();
    $exportAction->getFileDisk();
    $bulkAction->getFileDisk();
    $exporter->getFileDisk();
    $exportAction->isEnablingVisibleTableColumnsByDefault();
    $bulkAction->isEnablingVisibleTableColumnsByDefault();
    $importColumn->guess(guesses: ['full_name', 'display_name']);
    $importColumn->guess(static fn (): array => ['reference']);
    $importColumn->guess(...$arguments);
    ImportColumn::make('name')->requiredMapping()->GUESS(guesses: ['display_name']);
    $callback = $importColumn->guess(...);
    $event->getProcessedRows();
    $event->getSuccessfulRows();
    $field->getExtraFieldWrapperAttributesBag();
    $group->getExtraFieldWrapperAttributesBag();
    $builder->getRawItemState(key: 'block-7');
    $repeater->getRawItemState(key: 'row-3');
    $repeater->getRawItemState(...$arguments);
    $provider->hasSearchResultsUsing();
    $content->getCustomBlocksConfig();
    $upload->conversionsDisk(disk: null)->getConversionsDisk();
    $entry->getExtraEntryWrapperAttributesBag();
    $column->getGetStateUsingCallback();
    $exportColumn->getGetStateUsingCallback();
    $table->getInverseRelationship();
    $summarizer->hasQueryModification();
    $callback = $exporter->getFileDisk(...);
    $callback = $repeater->getRawItemState(...);
}

class CustomImportColumn extends ImportColumn {}

function nullable(?ExportAction $action, ?Builder $builder, ?Summarizer $summarizer, CustomImportColumn $column): void
{
    $action?->fileDisk(disk: 'private')?->getFileDisk();
    $builder?->getRawItemState(key: 'block-9');
    $summarizer?->hasQueryModification();
    $column->label('Name')->guess(guesses: ['name']);
}

class CustomUpload extends FileUpload
{
    public function scale(array $arguments): void
    {
        $this->getParentTargetSizes(widthOrHeight: 720);
        $this->getParentTargetSizes(...$arguments);
        $callback = $this->getParentTargetSizes(...);
    }
}

class CustomRepeatableEntry extends RepeatableEntry
{
    public function lookup(array $arguments): void
    {
        $this->getItemState(state: ['row-3' => null], key: 'row-3');
        $this?->getItemState(...$arguments);
        $callback = $this->getItemState(...);
    }
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedComponentApiNames;

use Filament\Actions\Action;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Imports\Events\ImportChunkProcessed;
use Filament\Actions\Imports\ImportColumn;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor\MentionProvider;
use Filament\Forms\Components\RichEditor\RichContentAttribute;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Infolists\Components\Entry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\FusedGroup;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Table;

function configure(Action $action, ExportAction $exportAction, ExportBulkAction $bulkAction, Exporter $exporter, ImportColumn $importColumn, ImportChunkProcessed $event, Field $field, FusedGroup $group, Builder $builder, Repeater $repeater, MentionProvider $provider, RichContentAttribute $content, SpatieMediaLibraryFileUpload $upload, Entry $entry, Column $column, ExportColumn $exportColumn, Table $table, Summarizer $summarizer, array $arguments): void
{
    $action->hasLivewireClickHandlerEnabledConfiguration();
    $exportAction->getFileDiskName();
    $bulkAction->getFileDiskName();
    $exporter->getFileDiskName();
    $exportAction->shouldEnableVisibleTableColumnsByDefault();
    $bulkAction->shouldEnableVisibleTableColumnsByDefault();
    $importColumn->guesses(guesses: ['full_name', 'display_name']);
    $importColumn->guesses(static fn (): array => ['reference']);
    $importColumn->guesses(...$arguments);
    ImportColumn::make('name')->requiredMapping()->guesses(guesses: ['display_name']);
    $callback = $importColumn->guesses(...);
    $event->getProcessedRowsCount();
    $event->getSuccessfulRowsCount();
    $field->getExtraFieldWrapperAttributeBag();
    $group->getExtraFieldWrapperAttributeBag();
    $builder->getItemStateSnapshot(key: 'block-7');
    $repeater->getItemStateSnapshot(key: 'row-3');
    $repeater->getItemStateSnapshot(...$arguments);
    $provider->hasSearchResultsUsingCallback();
    $content->getCustomBlocksConfiguration();
    $upload->conversionsDisk(disk: null)->getConversionsDiskName();
    $entry->getExtraEntryWrapperAttributeBag();
    $column->getStateUsingCallback();
    $exportColumn->getStateUsingCallback();
    $table->getInverseRelationshipName();
    $summarizer->hasQueryModificationCallback();
    $callback = $exporter->getFileDiskName(...);
    $callback = $repeater->getItemStateSnapshot(...);
}

class CustomImportColumn extends ImportColumn {}

function nullable(?ExportAction $action, ?Builder $builder, ?Summarizer $summarizer, CustomImportColumn $column): void
{
    $action?->fileDisk(disk: 'private')?->getFileDiskName();
    $builder?->getItemStateSnapshot(key: 'block-9');
    $summarizer?->hasQueryModificationCallback();
    $column->label('Name')->guesses(guesses: ['name']);
}

class CustomUpload extends FileUpload
{
    public function scale(array $arguments): void
    {
        $this->getImageEditorViewportScaleFactor(widthOrHeight: 720);
        $this->getImageEditorViewportScaleFactor(...$arguments);
        $callback = $this->getImageEditorViewportScaleFactor(...);
    }
}

class CustomRepeatableEntry extends RepeatableEntry
{
    public function lookup(array $arguments): void
    {
        $this->getItemStateWithExistence(state: ['row-3' => null], key: 'row-3');
        $this?->getItemStateWithExistence(...$arguments);
        $callback = $this->getItemStateWithExistence(...);
    }
}
