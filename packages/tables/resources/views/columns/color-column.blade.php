@if ($getState())
    @if ($isCopyable())
        <button
            x-data="{
                showCopied: false,
                copiedMessageTimeout: -1,
                copy() {
                    window
                        .navigator
                        .clipboard
                        .writeText('{{ $getState() }}');
                },
            }"
            @click="$tooltip('{{ $getCopyMessage() }}', { timeout: {{ $getCopyMessageShowTimeMs() }} })"
            @click.prevent.stop="copy"
        >
    @endif
            <span
                {{ $attributes->merge($getExtraAttributes())->class([
	                'filament-tables-color-column relative flex w-6 h-6 ml-4 rounded-md'
	            ]) }}
                class="relative flex w-6 h-6 ml-4 rounded-md"
                style="background-color: {{ $getState() }};"
                title="{{ $getState() }}"
            >
            </span>
    @if ($isCopyable())
        </button>
    @endif
@else
    <span>&nbsp;</span>
@endif
