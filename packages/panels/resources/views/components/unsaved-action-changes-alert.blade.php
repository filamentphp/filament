@if (filament()->hasUnsavedChangesAlerts())
    @script
        <script>
            window.addEventListener('beforeunload', (event) => {
                if (typeof @this === 'undefined') {
                    return
                }

                if (
                    [
                        ...(@js($this instanceof \Filament\Actions\Contracts\HasActions) ? ($wire.mountedActions ?? []) : []),
                        ...(@js($this instanceof \Filament\Forms\Contracts\HasForms)
                            ? ($wire.mountedFormComponentActions ?? [])
                            : []),
                        ...(@js($this instanceof \Filament\Infolists\Contracts\HasInfolists)
                            ? ($wire.mountedInfolistActions ?? [])
                            : []),
                        ...(@js($this instanceof \Filament\Tables\Contracts\HasTable)
                            ? [
                                  ...($wire.mountedTableActions ?? []),
                                  ...($wire.mountedTableBulkAction
                                      ? [$wire.mountedTableBulkAction]
                                      : []),
                              ]
                            : []),
                    ].length &&
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
