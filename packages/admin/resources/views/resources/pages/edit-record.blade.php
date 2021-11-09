<div class="space-y-4">
    <header class="space-y-2 items-center justify-between sm:flex sm:space-y-0 sm:space-x-4 sm:py-4">
        <h1 class="text-2xl font-bold tracking-tight md:text-3xl">
            {{ static::getTitle() }}
        </h1>

        <aside class="flex items-center">
            <x-filament::actions :actions="$this->getActions()" />
        </aside>
    </header>

    <div class="space-y-6">
        {{ $this->form }}
    </div>
</div>
