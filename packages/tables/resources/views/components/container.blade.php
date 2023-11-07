{{-- TODO: Extract to css file --}}
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

    .fi-ta-cell-content {
        position: relative;
        min-height: 1em;
    }    

    .fi-ta-cell-content::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;        
        background: rgb(249 250 251); /* bg-gray-50 */
        border-radius: 12px;
        animation: pulse 1.5s ease-in-out infinite;
        display: none;
    }

    @media (prefers-color-scheme: dark) {
        .fi-ta-cell-content::after {
            background: rgb(107 114 128); /* bg-gray-500 */
        }
    }

    .fi-loading .fi-ta-cell-content::after {
        display: block;
    }
    
    .fi-loading .fi-ta-cell-content > * {
      visibility: hidden;
    }
</style>

<div
    {{
        $attributes->class([
            'fi-ta-ctn divide-y divide-gray-200 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-900 dark:ring-white/10',
        ])
    }}
>
    {{ $slot }}
</div>
