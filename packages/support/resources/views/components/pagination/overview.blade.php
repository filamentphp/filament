@php
    use Illuminate\Pagination\LengthAwarePaginator;
    use Illuminate\Support\Number;

    $isSimple = ! $paginator instanceof LengthAwarePaginator;
@endphp

@if (! $isSimple)
    <span class="fi-pagination-overview">
        {{
            trans_choice(
                'filament::components/pagination.overview',
                $paginator->total(),
                [
                    'first' => Number::format($paginator->firstItem() ?? 0),
                    'last' => Number::format($paginator->lastItem() ?? 0),
                    'total' => Number::format($paginator->total()),
                ],
            )
        }}
    </span>
@endif
