<?php

namespace Filament\Tables;

use Closure;
use Exception;
use Filament\Forms\Form;
use Filament\Support\Components\ViewComponent;
use function Filament\Support\get_model_label;
use function Filament\Support\locale_has_pluralization;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\Position;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\Layout\Component;
use Filament\Tables\Columns\Layout\Component as ColumnLayoutComponent;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Filters\Layout;
use Filament\Tables\Grouping\Group;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use function Livewire\invade;

class Table extends ViewComponent
{
    use Concerns\BelongsToLivewire;

    protected string $view = 'filament-tables::index';

    protected string $viewIdentifier = 'table';

    protected string $evaluationIdentifier = 'table';

    public const LOADING_TARGETS = ['previousPage', 'nextPage', 'gotoPage', 'sortTable', 'tableFilters', 'resetTableFiltersForm', 'tableSearch', 'tableColumnSearches', 'tableRecordsPerPage', '$set'];

    protected array $actions = [];

    protected string | Closure | null $actionsColumnLabel = null;

    protected string | Closure | null $actionsAlignment = null;

    protected string | Closure | null $actionsPosition = null;

    protected bool | Closure $allowsDuplicates = false;

    protected array $bulkActions = [];

    protected ?ColumnLayoutComponent $collapsibleColumnsLayout = null;

    protected array $columnActions = [];

    protected array $columns = [];

    protected array $columnsLayout = [];

    protected int | array | Closure $columnToggleFormColumns = 1;

    protected string | Closure | null $columnToggleFormWidth = null;

    protected View | Htmlable | Closure | null $content = null;

    protected View | Htmlable | Closure | null $contentFooter = null;

    protected array | Closure | null $contentGrid = null;

    protected int | string | Closure | null $defaultPaginationPageOption = 10;

    protected string | Group | null $defaultGroup = null;

    protected ?string $defaultSortColumn = null;

    protected ?string $defaultSortDirection = null;

    protected ?Closure $defaultSortQuery = null;

    protected string | Htmlable | Closure | null $description = null;

    protected View | Htmlable | Closure | null $emptyState = null;

    protected string | Htmlable | Closure | null $emptyStateDescription = null;

    protected string | Htmlable | Closure | null $emptyStateHeading = null;

    protected string | Closure | null $emptyStateIcon = null;

    protected array $emptyStateActions = [];

    protected array $filters = [];

    protected int | array | Closure $filtersFormColumns = 1;

    protected string | Closure | null $filtersFormWidth = null;

    protected string | Closure | null $filtersLayout = null;

    protected array $groups = [];

    protected array $groupedBulkActions = [];

    protected bool $hasColumnsLayout = false;

    protected bool $hasSummary = false;

    protected View | Htmlable | Closure | null $header = null;

    protected string | Htmlable | Closure | null $heading = null;

    protected array $headerActions = [];

    protected string | Closure | null $inverseRelationship = null;

    protected bool | Closure $isGroupsOnly = false;

    protected bool | Closure $isPaginated = true;

    protected bool | Closure $isPaginatedWhileReordering = true;

    protected bool | Closure $isReorderable = true;

    protected bool | Closure $isStriped = false;

    protected string | Closure | null $modelLabel = null;

    protected array | Closure | null $paginationPageOptions = null;

    protected bool | Closure | null $persistsFiltersInSession = false;

    protected bool | Closure | null $persistsSearchInSession = false;

    protected bool | Closure | null $persistsColumnSearchInSession = false;

    protected string | Closure | null $pluralModelLabel = null;

    protected string | Closure | null $pollingInterval = null;

    protected Builder | Closure | null $query = null;

    protected string | Closure | null $queryStringIdentifier = null;

    protected string | Closure | null $recordAction = null;

    protected array | string | Closure | null $recordClasses = null;

    protected string | Closure | null $recordTitle = null;

    protected string | Closure | null $recordTitleAttribute = null;

    protected string | Closure | null $recordUrl = null;

    protected Relation | Closure | null $relationship = null;

    protected string | Closure | null $reorderColumn = null;

    protected bool | Closure | null $selectsCurrentPageOnly = false;

    public static string $defaultDateDisplayFormat = 'M j, Y';

    public static string $defaultDateTimeDisplayFormat = 'M j, Y H:i:s';

    public static string $defaultTimeDisplayFormat = 'H:i:s';

    final public function __construct(HasTable $livewire)
    {
        $this->livewire($livewire);
    }

    public static function make(HasTable $livewire): static
    {
        return app(static::class, ['livewire' => $livewire]);
    }

    public function actions(array | ActionGroup | Closure $actions, string | Closure | null $position = null): static
    {
        foreach (Arr::wrap($actions) as $index => $action) {
            if ($action instanceof ActionGroup) {
                foreach ($action->getActions() as $groupedAction) {
                    $groupedAction->table($this);
                }

                $this->actions[$index] = $action;

                continue;
            }

            $action->table($this);

            $this->actions[$action->getName()] = $action;
        }

        $this->actionsPosition($position);

        return $this;
    }

    public function actionsColumnLabel(string | Closure | null $label): static
    {
        $this->actionsColumnLabel = $label;

        return $this;
    }

    public function actionsAlignment(string | Closure | null $alignment = null): static
    {
        $this->actionsAlignment = $alignment;

        return $this;
    }

    public function actionsPosition(string | Closure | null $position = null): static
    {
        $this->actionsPosition = $position;

        return $this;
    }

    public function allowDuplicates(bool | Closure $condition = true): static
    {
        $this->allowsDuplicates = $condition;

        return $this;
    }

    public function bulkActions(array $actions): static
    {
        foreach ($actions as $action) {
            $action->table($this);
            $this->registerBulkAction($action);
            $this->groupedBulkActions[$action->getName()] = $action;
        }

        return $this;
    }

    public function registerBulkAction(BulkAction $action): static
    {
        $this->bulkActions[$action->getName()] = $action;

        return $this;
    }

    public function columns(array $components): static
    {
        foreach ($components as $component) {
            $component->table($this);

            if ($component instanceof Component && $component->isCollapsible()) {
                $this->collapsibleColumnsLayout = $component;
            } else {
                $this->columnsLayout[] = $component;
            }

            if ($component instanceof Column) {
                $this->columns[$component->getName()] = $component;

                continue;
            }

            $this->hasColumnsLayout = true;
            $this->columns = array_merge($this->columns, $component->getColumns());
        }

        foreach ($this->columns as $column) {
            if ($column->hasSummary()) {
                $this->hasSummary = true;
            }

            $action = $column->getAction();

            if (! ($action instanceof Action)) {
                continue;
            }

            $actionName = $action->getName();

            if (array_key_exists($actionName, $this->columnActions)) {
                continue;
            }

            $action->table($this);

            $this->columnActions[$actionName] = $action;
        }

        return $this;
    }

    public function columnToggleFormColumns(int | array | Closure $columns): static
    {
        $this->columnToggleFormColumns = $columns;

        return $this;
    }

    public function columnToggleFormWidth(string | Closure | null $width): static
    {
        $this->columnToggleFormWidth = $width;

        return $this;
    }

    public function content(View | Htmlable | Closure | null $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function contentFooter(View | Htmlable | Closure | null $footer): static
    {
        $this->contentFooter = $footer;

        return $this;
    }

    public function contentGrid(array | Closure | null $grid): static
    {
        $this->contentGrid = $grid;

        return $this;
    }

    public function defaultPaginationPageOption(int | string | Closure | null $option): static
    {
        $this->defaultPaginationPageOption = $option;

        return $this;
    }

    public function defaultGroup(string | Group | null $group): static
    {
        $this->defaultGroup = $group;

        return $this;
    }

    public function defaultSort(string | Closure | null $column, string | Closure | null $direction = 'asc'): static
    {
        if ($column instanceof Closure) {
            $this->defaultSortQuery = $column;
        } else {
            $this->defaultSortColumn = $column;
        }

        $this->defaultSortDirection = strtolower($direction);

        return $this;
    }

    public function description(string | Htmlable | Closure | null $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function emptyStateDescription(string | Htmlable | Closure | null $description): static
    {
        $this->emptyStateDescription = $description;

        return $this;
    }

    public function emptyState(View | Htmlable | Closure | null $emptyState): static
    {
        $this->emptyState = $emptyState;

        return $this;
    }

    public function emptyStateActions(array | ActionGroup | Closure $actions): static
    {
        foreach ($actions as $action) {
            $action->table($this);

            $this->emptyStateActions[$action->getName()] = $action;
        }

        return $this;
    }

    public function emptyStateHeading(string | Htmlable | Closure | null $heading): static
    {
        $this->emptyStateHeading = $heading;

        return $this;
    }

    public function emptyStateIcon(string | Closure | null $icon): static
    {
        $this->emptyStateIcon = $icon;

        return $this;
    }

    public function filters(array $filters, string | Closure | null $layout = null): static
    {
        foreach ($filters as $filter) {
            $filter->table($this);

            $this->filters[$filter->getName()] = $filter;
        }

        $this->filtersLayout($layout);

        return $this;
    }

    public function filtersFormColumns(int | array | Closure $columns): static
    {
        $this->filtersFormColumns = $columns;

        return $this;
    }

    public function filtersFormWidth(string | Closure | null $width): static
    {
        $this->filtersFormWidth = $width;

        return $this;
    }

    public function filtersLayout(string | Closure | null $filtersLayout): static
    {
        $this->filtersLayout = $filtersLayout;

        return $this;
    }

    public function groups(array $groups): static
    {
        foreach ($groups as $group) {
            if (! $group instanceof Group) {
                $group = Group::make($group);
            }

            $this->groups[$group->getId()] = $group;
        }

        return $this;
    }

    public function header(View | Htmlable | Closure | null $header): static
    {
        $this->header = $header;

        return $this;
    }

    public function headerActions(array | ActionGroup | Closure $actions): static
    {
        foreach (Arr::wrap($actions) as $index => $action) {
            if ($action instanceof ActionGroup) {
                foreach ($action->getActions() as $groupedAction) {
                    $groupedAction->table($this);

                    if ($groupedAction instanceof BulkAction) {
                        $this->registerBulkAction($groupedAction);
                    }
                }

                $this->headerActions[$index] = $action;

                continue;
            }

            $action->table($this);

            if ($action instanceof BulkAction) {
                $this->registerBulkAction($action);
            }

            $this->headerActions[$action->getName()] = $action;
        }

        return $this;
    }

    public function heading(string | Htmlable | Closure | null $heading): static
    {
        $this->heading = $heading;

        return $this;
    }

    public function groupsOnly(bool | Closure $condition = true): static
    {
        $this->isGroupsOnly = $condition;

        return $this;
    }

    public function inverseRelationship(string | Closure | null $name): static
    {
        $this->inverseRelationship = $name;

        return $this;
    }

    public function modelLabel(string | Closure | null $label): static
    {
        $this->modelLabel = $label;

        return $this;
    }

    public function paginated(bool | array | Closure $condition = true): static
    {
        if (is_array($condition)) {
            $this->paginationPageOptions($condition);
            $condition = true;
        }

        $this->isPaginated = $condition;

        return $this;
    }

    public function paginatedWhileReordering(bool | Closure $condition = true): static
    {
        $this->isPaginatedWhileReordering = $condition;

        return $this;
    }

    public function paginationPageOptions(array | Closure | null $options): static
    {
        $this->paginationPageOptions = $options;

        return $this;
    }

    public function persistFiltersInSession(bool | Closure $condition = true): static
    {
        $this->persistsFiltersInSession = $condition;

        return $this;
    }

    public function persistSearchInSession(bool | Closure $condition = true): static
    {
        $this->persistsSearchInSession = $condition;

        return $this;
    }

    public function persistColumnSearchInSession(bool | Closure $condition = true): static
    {
        $this->persistsColumnSearchInSession = $condition;

        return $this;
    }

    public function pluralModelLabel(string | Closure | null $label): static
    {
        $this->pluralModelLabel = $label;

        return $this;
    }

    public function poll(string | Closure | null $interval = '10s'): static
    {
        $this->pollingInterval = $interval;

        return $this;
    }

    public function query(Builder | Closure | null $query): static
    {
        $this->query = $query;

        return $this;
    }

    public function queryStringIdentifier(string | Closure | null $identifier): static
    {
        $this->queryStringIdentifier = $identifier;

        return $this;
    }

    public function recordAction(string | Closure | null $action): static
    {
        $this->recordAction = $action;

        return $this;
    }

    public function recordClasses(array | string | Closure | null $classes): static
    {
        $this->recordClasses = $classes;

        return $this;
    }

    public function recordTitle(string | Closure | null $title): static
    {
        $this->recordTitle = $title;

        return $this;
    }

    public function recordTitleAttribute(string | Closure | null $attribute): static
    {
        $this->recordTitleAttribute = $attribute;

        return $this;
    }

    public function recordUrl(string | Closure | null $url): static
    {
        $this->recordUrl = $url;

        return $this;
    }

    public function relationship(Relation | Closure | null $relationship): static
    {
        $this->relationship = $relationship;

        return $this;
    }

    public function reorderable(string | Closure | null $column = null, bool | Closure | null $condition = null): static
    {
        $this->reorderColumn = $column;

        if ($condition !== null) {
            $this->isReorderable = $condition;
        }

        return $this;
    }

    public function selectCurrentPageOnly(bool | Closure $condition = true): static
    {
        $this->selectsCurrentPageOnly = $condition;

        return $this;
    }

    public function striped(bool | Closure $condition = true): static
    {
        $this->isStriped = $condition;

        return $this;
    }

    public function getActions(): array
    {
        return $this->actions;
    }

    public function getAction(string $name): ?Action
    {
        $mountedRecord = $this->getLivewire()->getMountedTableActionRecord();

        $actions = $this->getActions();

        $action = $actions[$name] ?? null;

        if ($action) {
            return $action->record($mountedRecord);
        }

        foreach ($actions as $action) {
            if (! $action instanceof ActionGroup) {
                continue;
            }

            $groupedAction = $action->getActions()[$name] ?? null;

            if (! $groupedAction) {
                continue;
            }

            return $groupedAction->record($mountedRecord);
        }

        $actions = $this->columnActions;

        $action = $actions[$name] ?? null;

        if ($action) {
            return $action->record($mountedRecord);
        }

        return null;
    }

    public function getActionsPosition(): string
    {
        $position = $this->evaluate($this->actionsPosition);

        if (filled($position)) {
            return $position;
        }

        if (! ($this->getContentGrid() || $this->hasColumnsLayout())) {
            return Position::AfterCells;
        }

        $actions = $this->getActions();

        $firstAction = Arr::first($actions);

        if ($firstAction instanceof ActionGroup) {
            $firstAction->size('sm md:md');

            return Position::BottomCorner;
        }

        return Position::AfterContent;
    }

    public function getActionsAlignment(): ?string
    {
        return $this->evaluate($this->actionsAlignment);
    }

    public function getActionsColumnLabel(): ?string
    {
        return $this->evaluate($this->actionsColumnLabel);
    }

    public function getAllRecordsCount(): int
    {
        return $this->getLivewire()->getAllTableRecordsCount();
    }

    public function getGroupedBulkActions(): array
    {
        return $this->groupedBulkActions;
    }

    public function getBulkActions(): array
    {
        return $this->bulkActions;
    }

    public function getBulkAction(string $name): ?BulkAction
    {
        $action = $this->getBulkActions()[$name] ?? null;
        $action?->records($this->getLivewire()->getSelectedTableRecords());

        return $action;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getVisibleColumns(): array
    {
        return array_filter(
            $this->getColumns(),
            fn (Column $column): bool => (! $column->isHidden()) && (! $column->isToggledHidden()),
        );
    }

    public function getColumn(string $name): ?Column
    {
        return $this->getColumns()[$name] ?? null;
    }

    public function getColumnActions(): array
    {
        return $this->columnActions;
    }

    public function getColumnsLayout(): array
    {
        return $this->columnsLayout;
    }

    public function getCollapsibleColumnsLayout(): ?ColumnLayoutComponent
    {
        return $this->collapsibleColumnsLayout;
    }

    public function hasColumnsLayout(): bool
    {
        return $this->hasColumnsLayout;
    }

    public function hasSummary(): bool
    {
        return $this->hasSummary;
    }

    public function getContent(): View | Htmlable | null
    {
        return $this->evaluate($this->content);
    }

    public function getContentGrid(): ?array
    {
        return $this->evaluate($this->contentGrid);
    }

    public function getContentFooter(): View | Htmlable | null
    {
        return $this->evaluate($this->contentFooter);
    }

    public function getDefaultPaginationPageOption(): int | string | null
    {
        return $this->evaluate($this->defaultPaginationPageOption) ?? Arr::first($this->getPaginationPageOptions());
    }

    public function getDefaultGroup(): ?Group
    {
        if ($this->defaultGroup === null) {
            return null;
        }

        if ($this->defaultGroup instanceof Group) {
            return $this->defaultGroup;
        }

        $group = $this->getGroup($this->defaultGroup);

        if ($group) {
            return $group;
        }

        return Group::make($this->defaultGroup);
    }

    public function getDefaultSortColumn(): ?string
    {
        return $this->defaultSortColumn;
    }

    public function getDefaultSortDirection(): ?string
    {
        return $this->evaluate($this->defaultSortDirection);
    }

    public function getDefaultSortQuery(): ?Closure
    {
        return $this->defaultSortQuery;
    }

    public function getDescription(): string | Htmlable | null
    {
        return $this->evaluate($this->description);
    }

    public function getEmptyState(): View | Htmlable | null
    {
        return $this->evaluate($this->emptyState);
    }

    public function getEmptyStateActions(): array
    {
        return $this->emptyStateActions;
    }

    public function getEmptyStateAction(string $name): ?Action
    {
        return $this->getEmptyStateActions()[$name] ?? null;
    }

    public function getEmptyStateDescription(): string | Htmlable | null
    {
        return $this->evaluate($this->emptyStateDescription);
    }

    public function getEmptyStateHeading(): string | Htmlable
    {
        return $this->evaluate($this->emptyStateHeading) ?? __('filament-tables::table.empty.heading');
    }

    public function getEmptyStateIcon(): string
    {
        return $this->evaluate($this->emptyStateIcon) ?? 'heroicon-o-x-mark';
    }

    public function getFilters(): array
    {
        return array_filter(
            $this->filters,
            fn (BaseFilter $filter): bool => ! $filter->isHidden(),
        );
    }

    public function getFilter(string $name): ?BaseFilter
    {
        return $this->getFilters()[$name] ?? null;
    }

    public function getFiltersForm(): Form
    {
        return $this->getLivewire()->getTableFiltersForm();
    }

    public function getFiltersFormColumns(): int | array
    {
        return $this->evaluate($this->filtersFormColumns) ?? match ($this->getFiltersLayout()) {
            Layout::AboveContent, Layout::BelowContent => [
                'sm' => 2,
                'lg' => 3,
                'xl' => 4,
                '2xl' => 5,
            ],
            default => 1,
        };
    }

    public function getFiltersFormWidth(): ?string
    {
        return $this->evaluate($this->filtersFormWidth) ?? match ($this->getFiltersFormColumns()) {
            2 => '2xl',
            3 => '4xl',
            4 => '6xl',
            default => null,
        };
    }

    public function getFiltersLayout(): string
    {
        return $this->evaluate($this->filtersLayout) ?? Layout::Dropdown;
    }

    public function getGroups(): array
    {
        return $this->groups;
    }

    public function getGroup(string $id): ?Group
    {
        return $this->getGroups()[$id] ?? null;
    }

    public function getGrouping(): ?Group
    {
        return $this->getLivewire()->getTableGrouping();
    }

    public function getColumnToggleForm(): Form
    {
        return $this->getLivewire()->getTableColumnToggleForm();
    }

    public function getColumnToggleFormColumns(): int | array
    {
        return $this->evaluate($this->columnToggleFormColumns) ?? 1;
    }

    public function getColumnToggleFormWidth(): ?string
    {
        return $this->evaluate($this->columnToggleFormWidth) ?? match ($this->getColumnToggleFormColumns()) {
            2 => '2xl',
            3 => '4xl',
            4 => '6xl',
            default => null,
        };
    }

    public function getHeader(): View | Htmlable | null
    {
        return $this->evaluate($this->header);
    }

    public function getHeaderActions(): array
    {
        return $this->headerActions;
    }

    public function getHeaderAction(string $name): ?Action
    {
        $actions = $this->getHeaderActions();

        $action = $actions[$name] ?? null;

        if ($action) {
            return $action;
        }

        foreach ($actions as $action) {
            if (! $action instanceof ActionGroup) {
                continue;
            }

            $groupedAction = $action->getActions()[$name] ?? null;

            if (! $groupedAction) {
                continue;
            }

            return $groupedAction;
        }

        return null;
    }

    public function getHeading(): string | Htmlable | null
    {
        return $this->evaluate($this->heading);
    }

    public function getMountedAction(): ?Action
    {
        return $this->getLivewire()->getMountedTableAction();
    }

    public function getMountedActionRecordKey()
    {
        return $this->getLivewire()->getMountedTableActionRecordKey();
    }

    public function getMountedActionForm(): ?Form
    {
        return $this->getLivewire()->getMountedTableActionForm();
    }

    public function getMountedBulkAction(): ?BulkAction
    {
        return $this->getLivewire()->getMountedTableBulkAction();
    }

    public function getMountedBulkActionForm(): ?Form
    {
        return $this->getLivewire()->getMountedTableBulkActionForm();
    }

    public function getRecords(): Collection | Paginator
    {
        return $this->getLivewire()->getTableRecords();
    }

    public function getPaginationPageOptions(): array
    {
        return $this->evaluate($this->paginationPageOptions) ?? [5, 10, 25, 50, 'all'];
    }

    public function getRecordAction(Model $record): ?string
    {
        return $this->evaluate($this->recordAction, [
            'record' => $record,
        ]);
    }

    public function getRecordClasses(Model $record): array
    {
        return Arr::wrap($this->evaluate($this->recordClasses, [
            'record' => $record,
        ]) ?? []);
    }

    public function getRecordUrl(Model $record): ?string
    {
        return $this->evaluate($this->recordUrl, [
            'record' => $record,
        ]);
    }

    public function getReorderColumn(): ?string
    {
        return $this->evaluate($this->reorderColumn);
    }

    public function isReorderable(): bool
    {
        return filled($this->getReorderColumn()) && $this->evaluate($this->isReorderable);
    }

    public function isReordering(): bool
    {
        return $this->getLivewire()->isTableReordering();
    }

    public function getSortColumn(): ?string
    {
        return $this->getLivewire()->getTableSortColumn();
    }

    public function getSortDirection(): ?string
    {
        return $this->getLivewire()->getTableSortDirection();
    }

    public function isGroupsOnly(): bool
    {
        return $this->isGroupsOnly;
    }

    public function isFilterable(): bool
    {
        return (bool) count($this->getFilters());
    }

    public function isPaginated(): bool
    {
        return $this->evaluate($this->isPaginated) && (! $this->isGroupsOnly());
    }

    public function isPaginatedWhileReordering(): bool
    {
        return (bool) $this->evaluate($this->isPaginatedWhileReordering);
    }

    public function isSelectionEnabled(): bool
    {
        return (bool) count(array_filter(
            $this->getBulkActions(),
            fn (BulkAction $action): bool => ! $action->isHidden(),
        ));
    }

    public function isSearchable(): bool
    {
        foreach ($this->getColumns() as $column) {
            if (! $column->isGloballySearchable()) {
                continue;
            }

            return true;
        }

        return false;
    }

    public function isSearchableByColumn(): bool
    {
        foreach ($this->getColumns() as $column) {
            if (! $column->isIndividuallySearchable()) {
                continue;
            }

            return true;
        }

        return false;
    }

    public function hasToggleableColumns(): bool
    {
        foreach ($this->getColumns() as $column) {
            if (! $column->isToggleable()) {
                continue;
            }

            return true;
        }

        return false;
    }

    public function getRecordKey(Model $record): string
    {
        return $this->getLivewire()->getTableRecordKey($record);
    }

    public function getPollingInterval(): ?string
    {
        return $this->evaluate($this->pollingInterval);
    }

    public function isStriped(): bool
    {
        return (bool) $this->evaluate($this->isStriped);
    }

    public function getModel(): string
    {
        return $this->getQuery()->getModel()::class;
    }

    public function getRelationship(): Relation | Builder | null
    {
        return $this->evaluate($this->relationship);
    }

    public function getInverseRelationship(): ?string
    {
        $relationship = $this->getRelationship();

        if (! $relationship) {
            return null;
        }

        return $this->evaluate($this->inverseRelationship) ?? (string) str(class_basename($relationship->getParent()::class))
            ->plural()
            ->camel();
    }

    public function getInverseRelationshipFor(Model $record): Relation | Builder
    {
        return $record->{$this->getInverseRelationship()}();
    }

    public function getQuery(): Builder | Relation
    {
        if ($query = $this->evaluate($this->query)) {
            return $query->clone();
        }

        if ($query = $this->getRelationshipQuery()) {
            return $query->clone();
        }

        $livewireClass = $this->getLivewire()::class;

        throw new Exception("Table [{$livewireClass}] must have a [query()].");
    }

    public function getRelationshipQuery(): ?Builder
    {
        $relationship = $this->getRelationship();

        if (! $relationship) {
            return null;
        }

        $query = $relationship->getQuery();

        if ($relationship instanceof HasManyThrough) {
            // https://github.com/laravel/framework/issues/4962
            $query->select($query->getModel()->getTable() . '.*');

            return $query;
        }

        if ($relationship instanceof BelongsToMany) {
            // https://github.com/laravel/framework/issues/4962
            if (! $this->allowsDuplicates()) {
                $this->selectPivotDataInQuery($query);
            }

            // https://github.com/filamentphp/filament/issues/2079
            $query->withCasts(
                app($relationship->getPivotClass())->getCasts(),
            );
        }

        return $query;
    }

    public function selectPivotDataInQuery(Builder | Relation $query): Builder | Relation
    {
        /** @var BelongsToMany $relationship */
        $relationship = $this->getRelationship();

        $columns = [
            $relationship->getTable() . '.*',
            $query->getModel()->getTable() . '.*',
        ];

        if (! $this->allowsDuplicates()) {
            $columns = array_merge(invade($relationship)->aliasedPivotColumns(), $columns);
        }

        $query->select($columns);

        return $query;
    }

    public function allowsDuplicates(): bool
    {
        return (bool) $this->evaluate($this->allowsDuplicates);
    }

    public function getModelLabel(): string
    {
        return $this->evaluate($this->modelLabel) ?? get_model_label($this->getModel());
    }

    public function getPluralModelLabel(): string
    {
        $label = $this->evaluate($this->pluralModelLabel);

        if (filled($label)) {
            return $label;
        }

        if (locale_has_pluralization()) {
            return Str::plural($this->getModelLabel());
        }

        return $this->getModelLabel();
    }

    public function getRecordTitle(Model $record): string
    {
        $title = $this->evaluate($this->recordTitle, [
            'record' => $record,
        ]);

        if (filled($title)) {
            return $title;
        }

        $titleAttribute = $this->evaluate($this->recordTitleAttribute, [
            'record' => $record,
        ]);

        return $record->getAttributeValue($titleAttribute) ?? $this->getModelLabel();
    }

    public function getQueryStringIdentifier(): ?string
    {
        return $this->evaluate($this->queryStringIdentifier);
    }

    public function persistsFiltersInSession(): bool
    {
        return (bool) $this->evaluate($this->persistsFiltersInSession);
    }

    public function persistsSearchInSession(): bool
    {
        return (bool) $this->evaluate($this->persistsSearchInSession);
    }

    public function persistsColumnSearchInSession(): bool
    {
        return (bool) $this->evaluate($this->persistsColumnSearchInSession);
    }

    public function selectsCurrentPageOnly(): bool
    {
        return (bool) $this->evaluate($this->selectsCurrentPageOnly);
    }
}
