<?php

namespace Filament\Tests\Upgrade\Fixtures\ForwardingContracts;

use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use stdClass;

function configure(Action $action, ?Action $nullableAction, Schema $schema, FileUpload $upload, TextColumn $column, IconColumn $icon, KeyValueEntry $entry, Filter $filter, ?stdClass $loop): void
{
    $action->form(form: [])->modalSubheading(subheading: 'Details')->successNotificationMessage(message: 'Saved');
    $action->infolist(infolist: []);
    $action->withAttributes(attributes: ['data-current' => 'value']);
    $nullableAction?->form(form: [])->modalSubheading(subheading: null);
    $schema->name(name: 'orders')->context(context: 'edit')->reactive();
    $schema->getName();
    $upload->idleLabel(label: 'Upload')->enableDownload()->moveFile();
    $column->rowLoop(loop: $loop);
    $column->rowLoop(...['loop' => null]);
    $icon->options(options: ['heroicon-o-check' => 'published']);
    $entry->emptyMessage(message: 'No entries');
    $filter->form(schema: []);
    $action->form(...['form' => []]);
    $action->modalSubheading(...['subheading' => 'Unpacked']);
    $callback = $action->successNotificationMessage(...);
    $getter = $schema->getName(...);
}

class CustomColumn extends TextColumn
{
    public function rowLoop(?stdClass $loop): static
    {
        return parent::rowLoop(loop: $loop);
    }
}

class CustomAction extends Action
{
    public function form(array | Closure | null $customSchema): static
    {
        return $this;
    }
}

class Unrelated
{
    public function form(array $form): static
    {
        return $this;
    }

    public function rowLoop(?stdClass $loop): static
    {
        return $this;
    }
}

function manual(CustomAction $action, Unrelated $unrelated, Action $builtIn, array $arguments, ?stdClass $loop): void
{
    $action->form(customSchema: []);
    $unrelated->form(form: []);
    $unrelated->rowLoop(loop: $loop);
    $builtIn->form(...$arguments);
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\ForwardingContracts;

use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use stdClass;

function configure(Action $action, ?Action $nullableAction, Schema $schema, FileUpload $upload, TextColumn $column, IconColumn $icon, KeyValueEntry $entry, Filter $filter, ?stdClass $loop): void
{
    $action->schema(schema: [])->modalDescription(description: 'Details')->successNotificationTitle(title: 'Saved');
    $action->schema(schema: []);
    $action->extraAttributes(attributes: ['data-current' => 'value']);
    $nullableAction?->schema(schema: [])->modalDescription(description: null);
    $schema->key(key: 'orders')->operation(operation: 'edit')->live();
    $schema->getKey();
    $upload->placeholder(placeholder: 'Upload')->downloadable()->moveFiles();
    $column->rowLoop(rowLoop: $loop);
    $column->rowLoop(...['rowLoop' => null]);
    $icon->icons(icons: ['heroicon-o-check' => 'published']);
    $entry->placeholder(placeholder: 'No entries');
    $filter->schema(schema: []);
    $action->schema(...['schema' => []]);
    $action->modalDescription(...['description' => 'Unpacked']);
    $callback = $action->successNotificationTitle(...);
    $getter = $schema->getKey(...);
}

class CustomColumn extends TextColumn
{
    public function rowLoop(?stdClass $loop): static
    {
        return parent::rowLoop(rowLoop: $loop);
    }
}

class CustomAction extends Action
{
    public function form(array | Closure | null $customSchema): static
    {
        return $this;
    }
}

class Unrelated
{
    public function form(array $form): static
    {
        return $this;
    }

    public function rowLoop(?stdClass $loop): static
    {
        return $this;
    }
}

function manual(CustomAction $action, Unrelated $unrelated, Action $builtIn, array $arguments, ?stdClass $loop): void
{
    $action->form(customSchema: []);
    $unrelated->form(form: []);
    $unrelated->rowLoop(loop: $loop);
    $builtIn->form(...$arguments);
}
