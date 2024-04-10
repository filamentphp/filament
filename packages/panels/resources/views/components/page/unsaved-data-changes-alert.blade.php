@php
    use Filament\Support\Facades\FilamentView;
@endphp

@if ($this->hasUnsavedDataChangesAlert())
    @if (FilamentView::hasSpaMode())
        @script
            <script>
                shouldPreventNavigation = () => {
                    return window.jsMd5(
                        JSON.stringify($wire.data).replace(/\\/g, ''),
                    ) !== $wire.savedDataHash ||
                    $wire?.__instance?.effects?.redirect;
                };

                const showUnsavedChangesAlert = () => {
                    return confirm(@js(__('filament-panels::unsaved-changes.wire_navigate_alert')));
                };

                document.addEventListener('livewire:navigate', (event) => {
                    if (typeof @this !== 'undefined') {
                        if (! window.shouldPreventNavigation()) {
                            return
                        }

                        if (! showUnsavedChangesAlert()) {
                            event.preventDefault()
                        }
                    }
                })

                window.addEventListener('beforeunload', (event) => {
                    if (! window.shouldPreventNavigation()) {
                        return;
                    }

                    event.preventDefault()
                    event.returnValue = true
                })                
            </script>
        @endscript
    @else
        @script
            <script>
                window.addEventListener('beforeunload', (event) => {
                    if (
                        window.jsMd5(
                            JSON.stringify($wire.data).replace(/\\/g, ''),
                        ) === $wire.savedDataHash ||
                        $wire?.__instance?.effects?.redirect
                    ) {
                        return
                    }

                    event.preventDefault()
                    event.returnValue = true
                })
            </script>
        @endscript
    @endif
@endif
