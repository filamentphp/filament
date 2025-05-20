@php
    use Filament\Support\Facades\FilamentView;
@endphp

@if ($this->hasUnsavedDataChangesAlert())
    @if (FilamentView::hasSpaMode())
        @script
            <script>
                shouldPreventNavigation = () => {
                    if ($wire?.__instance?.effects?.redirect) {
                        return
                    }

                    return (
                        window.jsMd5(
                            JSON.stringify($wire.data).replace(/\\/g, ''),
                        ) !== $wire.savedDataHash
                    )
                }

                const showUnsavedChangesAlert = () => {
                    return confirm(@js(__('filament-panels::unsaved-changes-alert.body')))
                }

                document.addEventListener('livewire:navigate', (event) => {
                    if (typeof @this !== 'undefined') {
                        if (!shouldPreventNavigation()) {
                            return
                        }

                        if (showUnsavedChangesAlert()) {
                            return
                        }

                        event.preventDefault()
                    }
                })

                window.addEventListener('beforeunload', (event) => {
                    if (!shouldPreventNavigation()) {
                        return
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
