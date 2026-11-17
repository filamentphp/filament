<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Tables\Enums\PaginationMode;
use Filament\Tables\Table;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Arr;
use Illuminate\View\ComponentAttributeBag;

trait CanPaginateRecords
{
    protected int | string | Closure | null $defaultPaginationPageOption = null;

    protected bool | Closure $isPaginated = true;

    protected bool | Closure $isPaginatedWhileReordering = false;

    protected bool | Closure $persistsRecordsPerPageInSession = true;

    protected bool | Closure $enforcesPaginationPageOptions = true;

    /**
     * @var array<int | string> | Closure | null
     */
    protected array | Closure | null $paginationPageOptions = null;

    protected bool | Closure $hasExtremePaginationLinks = false;

    protected PaginationMode | Closure | null $paginationMode = null;

    protected bool | Closure $shouldScrollToTopOnPageChange = false;

    public function scrollToTopOnPageChange(bool | Closure $condition = true): static
    {
        $this->shouldScrollToTopOnPageChange = $condition;

        return $this;
    }

    public function shouldScrollToTopOnPageChange(): bool
    {
        return (bool) $this->evaluate($this->shouldScrollToTopOnPageChange);
    }

    public function paginationMode(PaginationMode | Closure | null $mode): static
    {
        $this->paginationMode = $mode;

        return $this;
    }

    public function getPaginationMode(): ?PaginationMode
    {
        return $this->evaluate($this->paginationMode) ?? PaginationMode::Default;
    }

    public function defaultPaginationPageOption(int | string | Closure | null $option): static
    {
        $this->defaultPaginationPageOption = $option;

        return $this;
    }

    /**
     * @param  bool | array<int | string> | Closure  $condition
     */
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

    public function persistRecordsPerPageInSession(bool | Closure $condition = true): static
    {
        $this->persistsRecordsPerPageInSession = $condition;

        return $this;
    }

    /**
     * @param  array<int | string> | Closure | null  $options
     */
    public function paginationPageOptions(array | Closure | null $options): static
    {
        $this->paginationPageOptions = $options;

        return $this;
    }

    public function enforcePaginationPageOptions(bool | Closure $condition = true): static
    {
        $this->enforcesPaginationPageOptions = $condition;

        return $this;
    }

    public function enforcesPaginationPageOptions(): bool
    {
        return (bool) $this->evaluate($this->enforcesPaginationPageOptions);
    }

    public function extremePaginationLinks(bool | Closure $condition = true): static
    {
        $this->hasExtremePaginationLinks = $condition;

        return $this;
    }

    public function getDefaultPaginationPageOption(): int | string | null
    {
        $option = $this->evaluate($this->defaultPaginationPageOption);

        if ($option) {
            return $option;
        }

        $options = $this->getPaginationPageOptions();

        if (in_array(10, $options)) {
            return 10;
        }

        return Arr::first($options);
    }

    /**
     * @return array<int | string>
     */
    public function getPaginationPageOptions(): array
    {
        return $this->evaluate($this->paginationPageOptions) ?? [5, 10, 25, 50];
    }

    public function isPaginated(): bool
    {
        return $this->evaluate($this->isPaginated) && (! $this->isGroupsOnly());
    }

    public function isPaginatedWhileReordering(): bool
    {
        return (bool) $this->evaluate($this->isPaginatedWhileReordering);
    }

    public function persistsRecordsPerPageInSession(): bool
    {
        return (bool) $this->evaluate($this->persistsRecordsPerPageInSession);
    }

    public function hasExtremePaginationLinks(): bool
    {
        return (bool) $this->evaluate($this->hasExtremePaginationLinks);
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function hasRenderPagination(): bool
    {
        $records = $this->isLoaded() ? $this->getRecords() : null;

        return (($records instanceof Paginator) || ($records instanceof \Illuminate\Contracts\Pagination\CursorPaginator))
            && (($records instanceof LengthAwarePaginator) ? (bool) $records->total() : $records->isNotEmpty());
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generatePaginationContainerHtml(): string
    {
        $records = $this->isLoaded() ? $this->getRecords() : null;

        ob_start(); ?>
        <?= $this->generateMorphMarkerHtml(); ?>
        <?php if ($this->hasRenderPagination()) {
            $hasExtremePaginationLinks = $this->hasExtremePaginationLinks();
            $paginationPageOptions = $this->getPaginationPageOptions();
            ?>

            <?= ($records instanceof AbstractPaginator) || ($records instanceof CursorPaginator)
                ? $this->generatePaginationHtml(
                    paginator: $records,
                    livewireId: $this->getLivewire()->getId(),
                    extremeLinks: $hasExtremePaginationLinks,
                    pageOptions: $paginationPageOptions,
                    loadingTargets: Table::LOADING_TARGETS,
                    isRtl: __('filament-panels::layout.direction') === 'rtl',
                )
                : view('filament::components.pagination.index', [
                    'paginator' => $records,
                    'extremeLinks' => $hasExtremePaginationLinks,
                    'pageOptions' => $paginationPageOptions,
                    'loadingTargets' => Table::LOADING_TARGETS,
                    'attributes' => new ComponentAttributeBag,
                ])->render(); ?>
        <?php } ?>
        <?= $this->generateMorphMarkerHtml(isEnd: true); ?>
        <?php return ob_get_clean();
    }
}
