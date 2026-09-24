<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Pages\Page;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\WithPagination;

class PaginationBrowserTest extends Page
{
    use WithPagination;

    protected string $view = 'pages.pagination-browser-test';

    protected static bool $shouldRegisterNavigation = false;

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $items = collect(range(1, 3));
        $currentPage = $this->getPage();

        return [
            'paginator' => new LengthAwarePaginator(
                $items->forPage($currentPage, 1),
                $items->count(),
                1,
                $currentPage,
                ['path' => request()->url()],
            ),
        ];
    }
}
