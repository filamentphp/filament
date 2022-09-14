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
                        .writeText('{{ $getState() }}')
                        .then(() => {
                            this.showCopied = true;
                            window.clearTimeout(this.copiedMessageTimeout);
                            this.copiedMessageTimeout = window.setTimeout(() => {
                                this.showCopied = false;
                            }, {{ $getCopyMessageShowTimeMs() }});
                        });
                },
            }"
            @click.prevent.stop="copy"
        >
            @endif
            <span
                class="relative flex"
                style="background-color: {{ $getState() }}; width: 1.5rem; height: 1.5rem; margin-left: 1rem; border-radius: 6px;"
                title="{{ $getState() }}"
            >
            </span>
            @if ($isCopyable())
                <span
                    x-cloak
                    x-transition
                    x-show="showCopied"
                    class="z-20 bg-gray-300 pointer-events-none top-0"
                    style="left: 2rem"
                >
                {{ $getCopyMessage() }}
            </span>
        </button>
    @endif
@else
    <span>&nbsp;</span>
@endif
