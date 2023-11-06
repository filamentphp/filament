@props([
    'footer' => null,
    'header' => null,
    'reorderable' => false,
    'hasLoadingOverlay',    
])

<style>
    @keyframes pulse {
        0%, 100% {
            opacity: 0.1;
        }
        25% {
            opacity: 0.2;
        }
        50% {
            opacity: 0.3;
        }
        75% {
            opacity: 0.2;
        }
    }

    td.fi-ta-cell-loading-state {
        position: relative;
        min-height: 1em;
    }    

    td.fi-ta-cell-loading-state::after {
        content: '';
        position: absolute;
        top: 35%;
        left: 1em;
        right: 1em;
        bottom: 35%;        
        background: rgb(249 250 251); /* bg-gray-50 */
        border-radius: 12px;
        animation: pulse 1.5s ease-in-out infinite;
        display: none;
    }

    @media (prefers-color-scheme: dark) {
        td.fi-ta-cell-loading-state::after {
            background: rgb(107 114 128); /* bg-gray-500 */
        }
    }

    .loading-active tr {
        padding-left 20px;
    }

    .loading-active td.fi-ta-cell-loading-state::after {
        display: block;
    }
    
    .loading-active td.fi-ta-cell-loading-state > * {
      visibility: hidden;
    }
</style>


<table
    {{ $attributes->class(['fi-ta-table w-full table-auto divide-y divide-gray-200 text-start dark:divide-white/5']) }}

    @if ($hasLoadingOverlay)
        wire:loading.delay.{{ config('filament.livewire_loading_delay', 'default') }}.class="loading-active"
    @endif
>
    @if ($header)
        <thead class="bg-gray-50 dark:bg-white/5">
            <tr>
                {{ $header }}
            </tr>
        </thead>
    @endif

    <tbody
        @if ($reorderable)
            x-on:end.stop="$wire.reorderTable($event.target.sortable.toArray())"
            x-sortable
        @endif
        class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5"
    >
        {{ $slot }}
    </tbody>

    @if ($footer)
        <tfoot class="bg-gray-50 dark:bg-white/5">
            <tr>
                {{ $footer }}
            </tr>
        </tfoot>
    @endif
</table>