<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedComponentApiNamesManual;

use Closure;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Actions\Imports\ImportColumn;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Resources\ParentResourceRegistration;
use Filament\Tables\Table;

abstract class CustomExporter extends Exporter
{
    public function getFileDisk(): string
    {
        return parent::getFileDisk();
    }
}

abstract class DescendantExporter extends CustomExporter {}

abstract class CanonicalExporter extends Exporter
{
    public function getFileDiskName(): string
    {
        return 'private';
    }
}

trait CustomGuesses
{
    public function guess(array | Closure $guesses): static
    {
        return $this;
    }
}

class CustomImportColumn extends ImportColumn
{
    use CustomGuesses;
}

class CustomTable extends Table
{
    public function getInverseRelationshipName(): ?string
    {
        return 'custom';
    }
}

class CustomBuilder extends Builder
{
    public function getRawItemState(string $key): array
    {
        return parent::getRawItemState($key);
    }
}

class CustomUpload extends FileUpload
{
    protected function getImageEditorViewportScaleFactor(int $widthOrHeight): int | float
    {
        return $this->getParentTargetSizes($widthOrHeight);
    }
}

class CustomEntry extends RepeatableEntry
{
    protected function getItemState(mixed $state, string | int $key): array
    {
        return parent::getItemState($state, $key);
    }
}

function manual(CustomExporter $exporter, DescendantExporter $descendant, CanonicalExporter $canonical, CustomImportColumn $column, CustomBuilder $builder, CustomTable $table, Export $export, ParentResourceRegistration $parent, Repeater $repeater, FileUpload $upload, RepeatableEntry $entry, object $unknown, string $method): void
{
    $exporter->getFileDisk();
    $descendant->getFileDisk();
    $canonical->getFileDisk();
    $column->guess(guesses: ['custom']);
    $column->label('Name')->guess(['custom']);
    $builder->getRawItemState(key: 'row-3');
    $table->getInverseRelationship();
    $table->heading('Posts')->getInverseRelationship();
    $export->getFileDisk();
    $parent->getInverseRelationship();
    $repeater->getItemState(key: 'row-3');
    $upload->getParentTargetSizes(widthOrHeight: 720);
    $entry->getItemState(state: [], key: 'row-3');
    $unknown->getCustomBlocksConfig();
    $column->{$method}(['name']);
    $callback = [$exporter, 'getFileDisk'];
    $callback = $canonical->getFileDisk(...);
    $column->guesses(guesses: ['canonical']);
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedComponentApiNamesManual;

use Closure;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Actions\Imports\ImportColumn;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Resources\ParentResourceRegistration;
use Filament\Tables\Table;

abstract class CustomExporter extends Exporter
{
    public function getFileDisk(): string
    {
        return parent::getFileDiskName();
    }
}

abstract class DescendantExporter extends CustomExporter {}

abstract class CanonicalExporter extends Exporter
{
    public function getFileDiskName(): string
    {
        return 'private';
    }
}

trait CustomGuesses
{
    public function guess(array | Closure $guesses): static
    {
        return $this;
    }
}

class CustomImportColumn extends ImportColumn
{
    use CustomGuesses;
}

class CustomTable extends Table
{
    public function getInverseRelationshipName(): ?string
    {
        return 'custom';
    }
}

class CustomBuilder extends Builder
{
    public function getRawItemState(string $key): array
    {
        return parent::getItemStateSnapshot($key);
    }
}

class CustomUpload extends FileUpload
{
    protected function getImageEditorViewportScaleFactor(int $widthOrHeight): int | float
    {
        return $this->getParentTargetSizes($widthOrHeight);
    }
}

class CustomEntry extends RepeatableEntry
{
    protected function getItemState(mixed $state, string | int $key): array
    {
        return parent::getItemState($state, $key);
    }
}

function manual(CustomExporter $exporter, DescendantExporter $descendant, CanonicalExporter $canonical, CustomImportColumn $column, CustomBuilder $builder, CustomTable $table, Export $export, ParentResourceRegistration $parent, Repeater $repeater, FileUpload $upload, RepeatableEntry $entry, object $unknown, string $method): void
{
    $exporter->getFileDisk();
    $descendant->getFileDisk();
    $canonical->getFileDisk();
    $column->guess(guesses: ['custom']);
    $column->label('Name')->guess(['custom']);
    $builder->getRawItemState(key: 'row-3');
    $table->getInverseRelationship();
    $table->heading('Posts')->getInverseRelationship();
    $export->getFileDisk();
    $parent->getInverseRelationship();
    $repeater->getItemState(key: 'row-3');
    $upload->getParentTargetSizes(widthOrHeight: 720);
    $entry->getItemState(state: [], key: 'row-3');
    $unknown->getCustomBlocksConfig();
    $column->{$method}(['name']);
    $callback = [$exporter, 'getFileDisk'];
    $callback = $canonical->getFileDisk(...);
    $column->guesses(guesses: ['canonical']);
}
