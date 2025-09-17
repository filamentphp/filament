@if (filament()->hasUnsavedChangesAlerts())
    @script
        <script>
            setUpUnsavedActionChangesAlert({
                resolveLivewireComponentUsing: () => @this,
                $wire,
            })
        </script>
    @endscript
@endif
