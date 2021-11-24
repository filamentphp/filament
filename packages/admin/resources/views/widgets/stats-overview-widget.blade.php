<x-filament::stats :columns="$this->getColumns()" class="col-span-full">
    @foreach ($this->getStats() as $card)
        {{ $card }}
    @endforeach
</x-filament::stats>
