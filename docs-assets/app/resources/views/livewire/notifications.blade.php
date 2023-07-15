<div class="min-h-screen">
    @if (request()->has('method'))
        <div wire:init="call('{{ request()->get('method') }}')"></div>
    @endif

    @livewire('database-notifications')
</div>
