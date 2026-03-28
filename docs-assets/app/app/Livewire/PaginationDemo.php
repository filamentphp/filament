<?php

namespace App\Livewire;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Livewire\Component;

class PaginationDemo extends Component
{
    public int | string $perPage = 10;

    public function render()
    {
        $items = collect(range(1, 50));

        $paginator = new LengthAwarePaginator(
            $items->forPage(3, 10),
            $items->count(),
            10,
            3,
            ['path' => request()->url()],
        );

        // Pass perPage + 1 items so Paginator knows there are more pages
        $simplePaginator = new Paginator(
            $items->forPage(2, 10)->push(21),
            10,
            2,
            ['path' => request()->url()],
        );

        return view('livewire.pagination-demo', [
            'paginator' => $paginator,
            'simplePaginator' => $simplePaginator,
        ]);
    }
}
