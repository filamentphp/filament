<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedFormFilterGroupMethods;

use Filament\Actions\Action;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

function configure(MarkdownEditor $markdown, RichEditor $rich, Type $type, BaseFilter $base, SelectFilter $select, TernaryFilter $ternary, ?Filter $filter, ?Group $group, array $arguments): void
{
    $markdown->getUploadedAttachmentUrlUsing(callback: fn (string $file): string => '/files/' . $file);
    $markdown->saveUploadedFileAttachmentsUsing(callback: null);
    $rich->getUploadedAttachmentUrlUsing(null);
    $rich->saveUploadedFileAttachmentsUsing(callback: fn ($file) => $file->store('attachments'));
    $rich->getUploadedAttachmentUrlUsing(null)->saveUploadedFileAttachmentsUsing(null);
    $type->titleColumnName(name: null);
    $type->titleColumnName(name: 'headline');
    $base->form(schema: fn (): array => []);
    $base->getFormSchema();
    $filter?->form(schema: null);
    $filter?->getFormSchema();
    $select->form(schema: [])->column(name: fn (): string => 'published_status')->getColumn();
    $ternary->column(name: null);
    $ternary->getColumn();
    $select->queriesRelationships();
    $ternary?->queriesRelationships();
    $select->getRelationshipKey();
    $select->getRelationshipKey(query: null);
    $ternary?->getRelationshipKey(...$arguments);
    $callback = $select->getRelationshipKey(...);
    SelectFilter::make('author')->relationship('author', 'name')->queriesRelationships();
    $group?->getDescriptionUsing(callback: null);
    $group?->getDescriptionUsing(callback: fn ($record): string => $record->description);
    $select->COLUMN(name: 'status');
    $markdown->getUploadedAttachmentUrlUsing(...$arguments);
    $type->titleColumnName(...$arguments);
    $base->form(...$arguments);
    $select->column(...$arguments);
    $group?->getDescriptionUsing(...$arguments);
    $callback = $rich->saveUploadedFileAttachmentsUsing(...);
    $callback = $type->titleColumnName(...);
    $callback = $base->form(...);
    $callback = $select->getColumn(...);
    $callback = $group->getDescriptionUsing(...);
    Action::make('edit')->form(form: []);
    Filter::make('active')->form(schema: []);
    SelectFilter::make('status')->column(name: 'published_status');
    Group::make('status')->getDescriptionUsing(callback: null);
}

class CustomFilter extends SelectFilter
{
    public function configureFilter(): void
    {
        $this->form(schema: []);
        $this->column(name: null);
        $this->getFormSchema();
        $this->getColumn();
    }
}

function inherited(CustomFilter $filter): void
{
    $filter->form(schema: [])->column(name: null)->getColumn();
    $filter?->form(schema: [])?->column(name: null)?->getColumn();
    $filter->getRelationshipKey(query: null);
    $filter->queriesRelationships();
}

class CustomTable extends Table {}

function grouping(Table $table, ?Table $nullableTable, CustomTable $custom, array $arguments): void
{
    $table->groupsInDropdownOnDesktop();
    $table->groupsInDropdownOnDesktop(condition: false);
    $table->groupsInDropdownOnDesktop(condition: fn (): bool => false);
    $nullableTable?->groupsInDropdownOnDesktop(condition: true);
    $table->groupsInDropdownOnDesktop(...$arguments);
    $callback = $table->groupsInDropdownOnDesktop(...);
    $table->GROUPSINDROPDOWNONDESKTOP(false);
    $custom->groupsInDropdownOnDesktop()->groupsInDropdownOnDesktop(false);
    $custom?->groupsInDropdownOnDesktop()?->groupsInDropdownOnDesktop(false);
    $table->groupingSettingsInDropdownOnDesktop(condition: false);
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedFormFilterGroupMethods;

use Filament\Actions\Action;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

function configure(MarkdownEditor $markdown, RichEditor $rich, Type $type, BaseFilter $base, SelectFilter $select, TernaryFilter $ternary, ?Filter $filter, ?Group $group, array $arguments): void
{
    $markdown->getFileAttachmentUrlUsing(callback: fn (string $file): string => '/files/' . $file);
    $markdown->saveUploadedFileAttachmentUsing(callback: null);
    $rich->getFileAttachmentUrlUsing(null);
    $rich->saveUploadedFileAttachmentUsing(callback: fn ($file) => $file->store('attachments'));
    $rich->getFileAttachmentUrlUsing(null)->saveUploadedFileAttachmentUsing(null);
    $type->titleAttribute(name: null);
    $type->titleAttribute(name: 'headline');
    $base->schema(schema: fn (): array => []);
    $base->getSchemaComponents();
    $filter?->schema(schema: null);
    $filter?->getSchemaComponents();
    $select->schema(schema: [])->attribute(name: fn (): string => 'published_status')->getAttribute();
    $ternary->attribute(name: null);
    $ternary->getAttribute();
    $select->hasRelationship();
    $ternary?->hasRelationship();
    $select->getQualifiedRelatedKeyNameForRelationship();
    $select->getQualifiedRelatedKeyNameForRelationship(query: null);
    $ternary?->getQualifiedRelatedKeyNameForRelationship(...$arguments);
    $callback = $select->getQualifiedRelatedKeyNameForRelationship(...);
    SelectFilter::make('author')->relationship('author', 'name')->hasRelationship();
    $group?->getDescriptionFromRecordUsing(callback: null);
    $group?->getDescriptionFromRecordUsing(callback: fn ($record): string => $record->description);
    $select->attribute(name: 'status');
    $markdown->getFileAttachmentUrlUsing(...$arguments);
    $type->titleAttribute(...$arguments);
    $base->schema(...$arguments);
    $select->attribute(...$arguments);
    $group?->getDescriptionFromRecordUsing(...$arguments);
    $callback = $rich->saveUploadedFileAttachmentUsing(...);
    $callback = $type->titleAttribute(...);
    $callback = $base->schema(...);
    $callback = $select->getAttribute(...);
    $callback = $group->getDescriptionFromRecordUsing(...);
    Action::make('edit')->schema(schema: []);
    Filter::make('active')->schema(schema: []);
    SelectFilter::make('status')->attribute(name: 'published_status');
    Group::make('status')->getDescriptionFromRecordUsing(callback: null);
}

class CustomFilter extends SelectFilter
{
    public function configureFilter(): void
    {
        $this->schema(schema: []);
        $this->attribute(name: null);
        $this->getSchemaComponents();
        $this->getAttribute();
    }
}

function inherited(CustomFilter $filter): void
{
    $filter->schema(schema: [])->attribute(name: null)->getAttribute();
    $filter?->schema(schema: [])?->attribute(name: null)?->getAttribute();
    $filter->getQualifiedRelatedKeyNameForRelationship(query: null);
    $filter->hasRelationship();
}

class CustomTable extends Table {}

function grouping(Table $table, ?Table $nullableTable, CustomTable $custom, array $arguments): void
{
    $table->groupingSettingsInDropdownOnDesktop();
    $table->groupingSettingsInDropdownOnDesktop(condition: false);
    $table->groupingSettingsInDropdownOnDesktop(condition: fn (): bool => false);
    $nullableTable?->groupingSettingsInDropdownOnDesktop(condition: true);
    $table->groupingSettingsInDropdownOnDesktop(...$arguments);
    $callback = $table->groupingSettingsInDropdownOnDesktop(...);
    $table->groupingSettingsInDropdownOnDesktop(false);
    $custom->groupingSettingsInDropdownOnDesktop()->groupingSettingsInDropdownOnDesktop(false);
    $custom?->groupingSettingsInDropdownOnDesktop()?->groupingSettingsInDropdownOnDesktop(false);
    $table->groupingSettingsInDropdownOnDesktop(condition: false);
}
