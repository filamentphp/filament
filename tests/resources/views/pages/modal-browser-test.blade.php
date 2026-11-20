@php
    use Filament\Support\View\ComponentAttributeBag;
    use Filament\Support\View\Concerns\CanGenerateButtonHtml;
    use Filament\Support\View\Concerns\CanGenerateModalHtml;
    use Illuminate\Support\HtmlString;
    use Illuminate\View\ComponentSlot;
@endphp

<x-filament-panels::page>
    <x-filament::button
        wire:click="activateLoadingButton"
        data-testid="loading-button"
        data-activations="{{ $loadingButtonActivations }}"
        icon="heroicon-o-arrow-path"
        tooltip="Simulate a pending request"
        :key-bindings="['ctrl+shift+l']"
    >
        Simulate loading
    </x-filament::button>

    <span data-testid="action-after-child-result">
        {{ $didRunActionAfterClosingChild ? 'ran' : 'not-ran' }}
    </span>

    <button
        type="button"
        wire:click="mountAction('modalLessParentWithChild')"
        data-testid="modal-less-parent-trigger"
    >
        Open child from action without modal
    </button>

    <button
        type="button"
        wire:click="mountAction('runAfterClosingChild')"
        data-testid="action-after-child-trigger"
    >
        Run after closing child
    </button>

    <button
        type="button"
        data-testid="behind-button"
        style="position: fixed; bottom: 2rem; right: 2rem; z-index: 1"
        x-data="{ label: 'Behind: not clicked' }"
        x-text="label"
        x-on:click="label = 'Behind: clicked'"
    ></button>

    <x-filament::modal
        id="standalone-browser-test-modal"
        aria-label="Standalone modal"
        :extra-modal-window-attribute-bag="new ComponentAttributeBag(['data-testid' => 'standalone-modal'])"
    >
        <x-slot name="trigger">
            <x-filament::button data-testid="standalone-trigger">
                Standalone modal
            </x-filament::button>
        </x-slot>

        <p>Standalone modal content.</p>

        <x-filament::button
            data-testid="standalone-close"
            x-on:click="$dispatch('close-modal', { id: 'standalone-browser-test-modal' })"
        >
            Close
        </x-filament::button>
    </x-filament::modal>

    <x-filament::modal
        id="standalone-browser-test-no-tabbable-content-modal"
        :close-button="false"
        :extra-modal-window-attribute-bag="new ComponentAttributeBag(['data-testid' => 'no-tabbable-content-modal'])"
        heading="No tabbable content modal"
    >
        <x-slot name="trigger">
            <x-filament::button data-testid="no-tabbable-content-trigger">
                No tabbable content modal
            </x-filament::button>
        </x-slot>

        <p>This modal contains no tabbable element.</p>
    </x-filament::modal>

    <x-filament::modal
        aria-label="Modal without an ID"
        :extra-modal-window-attribute-bag="new ComponentAttributeBag(['data-testid' => 'no-id-modal'])"
    >
        <x-slot name="trigger">
            <x-filament::button data-testid="no-id-trigger">
                Open modal without an ID
            </x-filament::button>
        </x-slot>

        <p>Standalone modal content.</p>
    </x-filament::modal>

    <x-filament::modal
        id="standalone-browser-test-long-sticky-modal"
        :close-button="false"
        :extra-modal-window-attribute-bag="new ComponentAttributeBag(['data-testid' => 'long-sticky-modal'])"
        heading="Reviewing an annual service agreement"
        sticky-header
    >
        <x-slot name="trigger">
            <x-filament::button data-testid="long-sticky-trigger">
                Open long sticky modal
            </x-filament::button>
        </x-slot>

        @foreach (range(1, 18) as $section)
            <section>
                <h3>Agreement section {{ $section }}</h3>

                <p>
                    Review the service scope, response times, renewal terms, and
                    account responsibilities before accepting this agreement.
                </p>
            </section>
        @endforeach
    </x-filament::modal>

    <x-filament::modal
        id="standalone-browser-test-long-slide-over-modal"
        :close-button="false"
        :extra-modal-window-attribute-bag="new ComponentAttributeBag(['data-testid' => 'long-slide-over-modal'])"
        heading="Reviewing an annual service agreement"
        slide-over
    >
        <x-slot name="trigger">
            <x-filament::button data-testid="long-slide-over-trigger">
                Open long slide-over
            </x-filament::button>
        </x-slot>

        @foreach (range(1, 18) as $section)
            <section>
                <h3>Agreement section {{ $section }}</h3>

                <p>
                    Review the service scope, response times, renewal terms, and
                    account responsibilities before accepting this agreement.
                </p>
            </section>
        @endforeach
    </x-filament::modal>

    @php
        $nativeModalGenerator = new class
        {
            use CanGenerateButtonHtml;
            use CanGenerateModalHtml;
        };

        $longNativeModalContent = new HtmlString(implode('', array_map(
            static fn (int $section): string => '<section><h3>Agreement section ' . $section . '</h3><p>Review the service scope, response times, renewal terms, and account responsibilities before accepting this agreement.</p></section>',
            range(1, 18),
        )));
    @endphp

    @foreach (['sticky', 'slide-over'] as $nativeModalType)
        {!!
            $nativeModalGenerator->generateModalHtml(
                slot: $longNativeModalContent,
                closeButton: false,
                extraModalWindowAttributeBag: new ComponentAttributeBag(['data-testid' => "long-native-{$nativeModalType}-modal"]),
                heading: 'Reviewing an annual service agreement',
                id: "standalone-browser-test-long-native-{$nativeModalType}-modal",
                slideOver: $nativeModalType === 'slide-over',
                stickyHeader: $nativeModalType === 'sticky',
                trigger: new ComponentSlot($nativeModalGenerator->generateButtonHtml(
                    attributes: new ComponentAttributeBag(['data-testid' => "long-native-{$nativeModalType}-trigger"]),
                    label: "Open long native {$nativeModalType} modal",
                )),
                livewireId: $this->getId(),
            )
        !!}
    @endforeach

    <x-filament::modal
        id="standalone-browser-test-no-focus-restore-modal"
        :restores-focus="false"
        :extra-modal-window-attribute-bag="new ComponentAttributeBag(['data-testid' => 'no-focus-restore-modal'])"
    >
        <x-slot name="trigger">
            <x-filament::button data-testid="no-focus-restore-trigger">
                No focus restore modal
            </x-filament::button>
        </x-slot>

        <p>Standalone modal content.</p>

        <x-filament::button
            data-testid="no-focus-restore-close"
            x-on:click="$dispatch('close-modal', { id: 'standalone-browser-test-no-focus-restore-modal' })"
        >
            Close
        </x-filament::button>
    </x-filament::modal>

    @if ($hasScrollableContent)
        <article>
            @foreach (range(1, 24) as $section)
                <section>
                    <h3>Service agreement section {{ $section }}</h3>
                    <p>
                        Review the service scope, response times, renewal terms,
                        and account responsibilities before accepting this
                        agreement.
                    </p>
                </section>
            @endforeach
        </article>
    @endif
</x-filament-panels::page>
