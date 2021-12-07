<x-filament::stats :columns="$this->getColumns()" class="col-span-full">
    @foreach ($this->getCards() as $card)
        {{ $card }}
    @endforeach
</x-filament::stats>
