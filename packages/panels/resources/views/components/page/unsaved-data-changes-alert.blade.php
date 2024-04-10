@php
    use Filament\Support\Facades\FilamentView;
@endphp

@if ($this->hasUnsavedDataChangesAlert())
    @script
        <script>
            window.shouldPreventNavigation = () => {
                return window.jsMd5(
                    JSON.stringify($wire.data).replace(/\\/g, ''),
                ) !== $wire.savedDataHash ||
                $wire?.__instance?.effects?.redirect;
            };

            window.addEventListener('beforeunload', (event) => {
                if (! shouldPreventNavigation()) {
                    return;
                }

                event.preventDefault()
                event.returnValue = true
            })
        </script>
    @endscript

    @if (FilamentView::hasSpaMode())
        @script
            <script>
                const showUnsavedChangesAlert = () => {
                    return confirm(@js(__('filament-panels::unsaved-changes.wire_navigate_alert')));
                };

                document.addEventListener('livewire:navigate', (event) => {
                    if (! window.shouldPreventNavigation()) {
                        return
                    }

                    if (! showUnsavedChangesAlert()) {
                        event.preventDefault()
                    }
                }, { once: true })
            </script>
        @endscript
    @endif
@endif
