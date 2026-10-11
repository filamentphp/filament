@if (filament()->hasUnsavedChangesAlerts())
    @script
        <script>
            setUpFilamentUnsavedActionChangesAlert({
                resolveLivewireComponentUsing: () => @this,
                $wire,
            })
        </script>
    @endscript
@endif
