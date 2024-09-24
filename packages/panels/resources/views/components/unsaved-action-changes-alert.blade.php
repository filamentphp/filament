@if (filament()->hasUnsavedChangesAlerts())
    @script
        <script>
            window.addEventListener('beforeunload', (event) => {
                if (typeof @this === 'undefined') {
                    return
                }

                if (
                    (@js($this instanceof \Filament\Actions\Contracts\HasActions) ? ($wire.mountedActions?.length ?? 0) : 0) &&
                    !$wire?.__instance?.effects?.redirect
                ) {
                    event.preventDefault()
                    event.returnValue = true

                    return
                }
            })
        </script>
    @endscript
@endif
