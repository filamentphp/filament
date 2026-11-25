@if ($getRecord()->is($getLivewire()->getTableRecords()->first()))
    @livewire(\Filament\Tests\Fixtures\Livewire\NestedSelectionTable::class, key('nested-selection'))
@endif
