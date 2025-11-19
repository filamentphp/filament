@if (method_exists($this, 'hasUnsavedDataChangesAlert') && $this->hasUnsavedDataChangesAlert())
    @if (\Filament\Support\Facades\FilamentView::hasSpaMode())
        @script
        <script>
            setUpSpaModeUnsavedDataChangesAlert({
                body: @js(__('filament-panels::unsaved-changes-alert.body')),
                resolveLivewireComponentUsing: () => @this,
                $wire
            })
        </script>
        @endscript
    @else
        @script
        <script>
            setUpUnsavedDataChangesAlert({ $wire })
        </script>
        @endscript
    @endif
@endif
