@if (filament()->hasUnsavedChangesAlerts())
    @script
        <script nonce="{{ \Filament\csp_nonce() }}">
            setUpUnsavedActionChangesAlert({
                resolveLivewireComponentUsing: () => @this,
                $wire,
            })
        </script>
    @endscript
@endif
