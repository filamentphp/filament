@php
    use Filament\Support\Facades\FilamentView;
@endphp

@if ($this->hasUnsavedChangesAlert() && (! FilamentView::hasSpaMode()))
    @script
        <script>
            window.addEventListener('beforeunload', (event) => {
                if (
                    JSON.stringify($wire.data) ===
                    JSON.stringify($wire.savedData)
                ) {
                    return
                }

                event.preventDefault()
                event.returnValue = true
            })
        </script>
    @endscript
@endif
