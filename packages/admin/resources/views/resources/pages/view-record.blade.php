<div class="space-y-4">
    <header class="space-y-2 items-center justify-between sm:flex sm:space-y-0 sm:space-x-4 sm:py-4">
        <h1 class="text-2xl font-bold tracking-tight md:text-3xl">
            {{ static::getTitle() }}
        </h1>

        <aside class="flex items-center space-x-4">
            <a class="inline-flex items-center justify-center px-4 font-medium tracking-tight text-white transition bg-primary-600 rounded-lg shadow h-9 hover:bg-primary-500 focus:bg-primary-700 focus:outline-none focus:ring-offset-2 focus:ring-offset-primary-700 focus:ring-2 focus:ring-white focus:ring-inset"
               href="#">Edit</a>
        </aside>
    </header>

    <div class="space-y-6">
        {{ $this->form }}
    </div>
</div>
