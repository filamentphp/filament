<?php

namespace Filament\Tables;

use Filament\Support\Components\ViewComponent;
use Filament\Tables\Contracts\HasTable;

class Table extends ViewComponent
{
    use Table\Concerns\BelongsToLivewire;
    use Table\Concerns\CanBeStriped;
    use Table\Concerns\CanDeferLoading;
    use Table\Concerns\CanGroupRecords;
    use Table\Concerns\CanPaginateRecords;
    use Table\Concerns\CanPollRecords;
    use Table\Concerns\CanReorderRecords;
    use Table\Concerns\CanSearchRecords;
    use Table\Concerns\CanSortRecords;
    use Table\Concerns\CanSummarizeRecords;
    use Table\Concerns\CanToggleColumns;
    use Table\Concerns\HasActions;
    use Table\Concerns\HasBulkActions;
    use Table\Concerns\HasColumns;
    use Table\Concerns\HasContent;
    use Table\Concerns\HasEmptyState;
    use Table\Concerns\HasFilterIndicators;
    use Table\Concerns\HasFilters;
    use Table\Concerns\HasHeader;
    use Table\Concerns\HasHeaderActions;
    use Table\Concerns\HasQuery;
    use Table\Concerns\HasQueryStringIdentifier;
    use Table\Concerns\HasRecordAction;
    use Table\Concerns\HasRecordClasses;
    use Table\Concerns\HasRecords;
    use Table\Concerns\HasRecordUrl;

    /**
     * @var view-string
     */
    protected string $view = 'filament-tables::index';

    protected string $viewIdentifier = 'table';

    protected string $evaluationIdentifier = 'table';

    public const LOADING_TARGETS = [
        'gotoPage',
        'nextPage',
        'previousPage',
        'removeTableFilter',
        'removeTableFilters',
        'reorderTable',
        'resetTableFiltersForm',
        'sortTable',
        'tableColumnSearches',
        'tableFilters',
        'tableRecordsPerPage',
        'tableSearch',
    ];

    public static string $defaultCurrency = 'usd';

    public static string $defaultDateDisplayFormat = 'M j, Y';

    public static string $defaultDateTimeDisplayFormat = 'M j, Y H:i:s';

    public static ?string $defaultNumberLocale = null;

    public static string $defaultTimeDisplayFormat = 'H:i:s';

    final public function __construct(HasTable $livewire)
    {
        $this->livewire($livewire);
    }

    public static function make(HasTable $livewire): static
    {
        $static = app(static::class, ['livewire' => $livewire]);
        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->emptyStateDescription(function (Table $table): ?string {
            if (! $table->hasAction('create')) {
                return null;
            }

            return __('filament-tables::table.empty.description', [
                'model' => $table->getModelLabel(),
            ]);
        });
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'livewire' => [$this->getLivewire()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }
}
