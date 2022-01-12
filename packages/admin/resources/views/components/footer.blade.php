@if (config('filament.layout.footer.should_show_logo'))
    <div class="flex items-center justify-center text-gray-300 hover:text-primary-500 transition">
        {{ config('app.name') }}
    </div>
@endif
