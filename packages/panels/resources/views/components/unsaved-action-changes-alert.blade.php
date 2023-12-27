@if (filament()->hasUnsavedChangesAlerts())
    @script
        <script>
            window.addEventListener('beforeunload', (event) => {
                for (let actionData of [
                    ...(@js($this instanceof \Filament\Actions\Contracts\HasActions) ? $wire.mountedActionsData ?? [] : []),
                    ...(@js($this instanceof \Filament\Forms\Contracts\HasForms)
                        ? $wire.mountedFormComponentActionsData ?? []
                        : []),
                    ...(@js($this instanceof \Filament\Infolists\Contracts\HasInfolists) ? $wire.mountedInfolistActionsData ?? [] : []),
                    ...(@js($this instanceof \Filament\Tables\Contracts\HasTable)
                        ? [
                              ...($wire.mountedTableActionsData ?? []),
                              $wire.mountedTableBulkActionData ?? {},
                          ]
                        : []),
                ]) {
                    if (
                        ![JSON.stringify([]), JSON.stringify({})].includes(
                            JSON.stringify(actionData),
                        )
                    ) {
                        event.preventDefault()
                        event.returnValue = true

                        return
                    }
                }
            })
        </script>
    @endscript
@endif
