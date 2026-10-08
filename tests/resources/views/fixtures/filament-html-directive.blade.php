@php
    use Filament\Tests\Fixtures\Livewire\FilamentHtmlDirective;
@endphp

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />

        <title>Filament HTML directive</title>

        @filamentStyles
    </head>

    <body>
        @livewire(FilamentHtmlDirective::class)

        @livewireScripts
        @filamentScripts
    </body>
</html>
