<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedFormFilterGroupManualBoundaries;

use Closure;
use Filament\Forms\Components\Concerns\HasFileAttachments;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Filters\Concerns\HasSchema;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Filament\Tables\Table\Concerns\CanGroupRecords;
use Illuminate\Database\Eloquent\Builder;

class AliasOverride extends SelectFilter
{
    public function column(string | Closure | null $customName): static
    {
        return parent::column($customName);
    }

    public function queriesRelationships(): bool
    {
        return parent::hasRelationship();
    }

    public function getRelationshipKey(?Builder $customQuery = null): ?string
    {
        return parent::getQualifiedRelatedKeyNameForRelationship($customQuery);
    }
}

class InheritedAliasOverride extends AliasOverride {}

class ReplacementOverride extends SelectFilter
{
    public function attribute(string | Closure | null $customName): static
    {
        return $this;
    }

    public function getAttribute(): string
    {
        return 'custom';
    }

    public function hasRelationship(): bool
    {
        return false;
    }

    public function getQualifiedRelatedKeyNameForRelationship(?Builder $customQuery = null): ?string
    {
        return null;
    }
}

class SchemaOverride extends Filter
{
    public function schema(array | Closure | null $customSchema): static
    {
        return $this;
    }

    public function getSchemaComponents(): array
    {
        return [];
    }
}

class EditorOverride extends RichEditor
{
    public function getFileAttachmentUrlUsing(?Closure $customCallback): static
    {
        return parent::getUploadedAttachmentUrlUsing($customCallback);
    }
}

class TypeOverride extends Type
{
    public function titleAttribute(?string $customName): static
    {
        return parent::titleColumnName($customName);
    }
}

trait DescriptionOverride
{
    public function getDescriptionFromRecordUsing(?Closure $customCallback): static
    {
        return $this;
    }
}

class TraitOverride extends Group
{
    use DescriptionOverride;
}

class AdaptedVisibility extends Filter
{
    use HasSchema {
        schema as protected;
    }
}

class StandaloneConcerns
{
    use HasFileAttachments;
    use HasSchema;
}

function overrides(InheritedAliasOverride $alias, ReplacementOverride $replacement, SchemaOverride $schema, EditorOverride $editor, TypeOverride $type, TraitOverride $group, AdaptedVisibility $visibility, StandaloneConcerns $standalone, SelectFilter | ReplacementOverride $union): void
{
    $alias->column(customName: null);
    $alias->label('Status')->column(customName: null);
    $replacement->column(name: null);
    $replacement->getColumn();
    $schema->form(schema: null);
    $schema->getFormSchema();
    $editor->getUploadedAttachmentUrlUsing(callback: null);
    $type->titleColumnName(name: null);
    $group->getDescriptionUsing(callback: null);
    $visibility->form(schema: []);
    $standalone->form(schema: []);
    $standalone->getUploadedAttachmentUrlUsing(null);
    $union->column(name: null);
    $alias->queriesRelationships();
    $alias->getRelationshipKey(customQuery: null);
    $replacement->queriesRelationships();
    $replacement->getRelationshipKey(query: null);
    $union->queriesRelationships();
    $union->getRelationshipKey();
}

function unrelated(Table $table, Group $group, Filter $filter, BaseFilter $base, SelectFilter | Group $union, $unknown): void
{
    $table->getColumn('status');
    $group->column(column: 'status')->getColumn();
    $filter->column('status');
    $base->getColumn();
    $unknown->form([])->column('status');
    $union->column('status');
    $method = 'form';
    $filter->{$method}([]);
    $filter->{'form'}([]);
    $callback = [$filter, 'getFormSchema'];
    SelectFilter::column('status');
    $base->queriesRelationships();
    $unknown->getRelationshipKey();
    $union->queriesRelationships();
    $method = 'getRelationshipKey';
    $filter->{$method}();
    $callback = [$filter, 'queriesRelationships'];
    SelectFilter::getRelationshipKey();
}

function upgraded(RichEditor $editor, Type $type, SelectFilter $filter, Group $group): void
{
    $editor->getFileAttachmentUrlUsing(callback: null)->saveUploadedFileAttachmentUsing(callback: null);
    $type->titleAttribute(name: null);
    $filter->schema(schema: null)->attribute(name: null)->getAttribute();
    $filter->getSchemaComponents();
    $group->getDescriptionFromRecordUsing(callback: null);
}

class GroupingAliasOverride extends Table
{
    public function groupsInDropdownOnDesktop(bool | Closure $customCondition = true): static
    {
        return parent::groupsInDropdownOnDesktop($customCondition);
    }
}

class InheritedGroupingAliasOverride extends GroupingAliasOverride {}

class GroupingReplacementOverride extends Table
{
    public function groupingSettingsInDropdownOnDesktop(bool | Closure $customCondition = true): static
    {
        return parent::groupsInDropdownOnDesktop($customCondition);
    }
}

class GroupingVisibility extends Table
{
    use CanGroupRecords {
        groupingSettingsInDropdownOnDesktop as protected;
    }
}

class StandaloneGrouping
{
    use CanGroupRecords;
}

function groupingBoundaries(InheritedGroupingAliasOverride $alias, GroupingReplacementOverride $replacement, GroupingVisibility $visibility, StandaloneGrouping $standalone, Table | GroupingReplacementOverride $union, $unknown): void
{
    $alias->groupsInDropdownOnDesktop(customCondition: false);
    $replacement->groupsInDropdownOnDesktop(condition: false);
    $replacement->columns(2)->groupsInDropdownOnDesktop(condition: false);
    $visibility->groupsInDropdownOnDesktop();
    $standalone->groupsInDropdownOnDesktop();
    $union->groupsInDropdownOnDesktop();
    $unknown->groupsInDropdownOnDesktop();
    $method = 'groupsInDropdownOnDesktop';
    $replacement->{$method}();
    $replacement->{'groupsInDropdownOnDesktop'}();
    $callback = [$replacement, 'groupsInDropdownOnDesktop'];
    Table::groupsInDropdownOnDesktop();
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedFormFilterGroupManualBoundaries;

use Closure;
use Filament\Forms\Components\Concerns\HasFileAttachments;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Filters\Concerns\HasSchema;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Filament\Tables\Table\Concerns\CanGroupRecords;
use Illuminate\Database\Eloquent\Builder;

class AliasOverride extends SelectFilter
{
    public function column(string | Closure | null $customName): static
    {
        return parent::attribute($customName);
    }

    public function queriesRelationships(): bool
    {
        return parent::hasRelationship();
    }

    public function getRelationshipKey(?Builder $customQuery = null): ?string
    {
        return parent::getQualifiedRelatedKeyNameForRelationship($customQuery);
    }
}

class InheritedAliasOverride extends AliasOverride {}

class ReplacementOverride extends SelectFilter
{
    public function attribute(string | Closure | null $customName): static
    {
        return $this;
    }

    public function getAttribute(): string
    {
        return 'custom';
    }

    public function hasRelationship(): bool
    {
        return false;
    }

    public function getQualifiedRelatedKeyNameForRelationship(?Builder $customQuery = null): ?string
    {
        return null;
    }
}

class SchemaOverride extends Filter
{
    public function schema(array | Closure | null $customSchema): static
    {
        return $this;
    }

    public function getSchemaComponents(): array
    {
        return [];
    }
}

class EditorOverride extends RichEditor
{
    public function getFileAttachmentUrlUsing(?Closure $customCallback): static
    {
        return parent::getFileAttachmentUrlUsing($customCallback);
    }
}

class TypeOverride extends Type
{
    public function titleAttribute(?string $customName): static
    {
        return parent::titleAttribute($customName);
    }
}

trait DescriptionOverride
{
    public function getDescriptionFromRecordUsing(?Closure $customCallback): static
    {
        return $this;
    }
}

class TraitOverride extends Group
{
    use DescriptionOverride;
}

class AdaptedVisibility extends Filter
{
    use HasSchema {
        schema as protected;
    }
}

class StandaloneConcerns
{
    use HasFileAttachments;
    use HasSchema;
}

function overrides(InheritedAliasOverride $alias, ReplacementOverride $replacement, SchemaOverride $schema, EditorOverride $editor, TypeOverride $type, TraitOverride $group, AdaptedVisibility $visibility, StandaloneConcerns $standalone, SelectFilter | ReplacementOverride $union): void
{
    $alias->column(customName: null);
    $alias->label('Status')->column(customName: null);
    $replacement->column(name: null);
    $replacement->getColumn();
    $schema->form(schema: null);
    $schema->getFormSchema();
    $editor->getUploadedAttachmentUrlUsing(callback: null);
    $type->titleColumnName(name: null);
    $group->getDescriptionUsing(callback: null);
    $visibility->form(schema: []);
    $standalone->form(schema: []);
    $standalone->getUploadedAttachmentUrlUsing(null);
    $union->attribute(name: null);
    $alias->queriesRelationships();
    $alias->getRelationshipKey(customQuery: null);
    $replacement->queriesRelationships();
    $replacement->getRelationshipKey(query: null);
    $union->hasRelationship();
    $union->getQualifiedRelatedKeyNameForRelationship();
}

function unrelated(Table $table, Group $group, Filter $filter, BaseFilter $base, SelectFilter | Group $union, $unknown): void
{
    $table->getColumn('status');
    $group->column(column: 'status')->getColumn();
    $filter->column('status');
    $base->getColumn();
    $unknown->form([])->column('status');
    $union->column('status');
    $method = 'form';
    $filter->{$method}([]);
    $filter->{'form'}([]);
    $callback = [$filter, 'getFormSchema'];
    SelectFilter::column('status');
    $base->queriesRelationships();
    $unknown->getRelationshipKey();
    $union->queriesRelationships();
    $method = 'getRelationshipKey';
    $filter->{$method}();
    $callback = [$filter, 'queriesRelationships'];
    SelectFilter::getRelationshipKey();
}

function upgraded(RichEditor $editor, Type $type, SelectFilter $filter, Group $group): void
{
    $editor->getFileAttachmentUrlUsing(callback: null)->saveUploadedFileAttachmentUsing(callback: null);
    $type->titleAttribute(name: null);
    $filter->schema(schema: null)->attribute(name: null)->getAttribute();
    $filter->getSchemaComponents();
    $group->getDescriptionFromRecordUsing(callback: null);
}

class GroupingAliasOverride extends Table
{
    public function groupsInDropdownOnDesktop(bool | Closure $customCondition = true): static
    {
        return parent::groupingSettingsInDropdownOnDesktop($customCondition);
    }
}

class InheritedGroupingAliasOverride extends GroupingAliasOverride {}

class GroupingReplacementOverride extends Table
{
    public function groupingSettingsInDropdownOnDesktop(bool | Closure $customCondition = true): static
    {
        return parent::groupingSettingsInDropdownOnDesktop($customCondition);
    }
}

class GroupingVisibility extends Table
{
    use CanGroupRecords {
        groupingSettingsInDropdownOnDesktop as protected;
    }
}

class StandaloneGrouping
{
    use CanGroupRecords;
}

function groupingBoundaries(InheritedGroupingAliasOverride $alias, GroupingReplacementOverride $replacement, GroupingVisibility $visibility, StandaloneGrouping $standalone, Table | GroupingReplacementOverride $union, $unknown): void
{
    $alias->groupsInDropdownOnDesktop(customCondition: false);
    $replacement->groupsInDropdownOnDesktop(condition: false);
    $replacement->columns(2)->groupsInDropdownOnDesktop(condition: false);
    $visibility->groupsInDropdownOnDesktop();
    $standalone->groupsInDropdownOnDesktop();
    $union->groupingSettingsInDropdownOnDesktop();
    $unknown->groupsInDropdownOnDesktop();
    $method = 'groupsInDropdownOnDesktop';
    $replacement->{$method}();
    $replacement->{'groupsInDropdownOnDesktop'}();
    $callback = [$replacement, 'groupsInDropdownOnDesktop'];
    Table::groupsInDropdownOnDesktop();
}
