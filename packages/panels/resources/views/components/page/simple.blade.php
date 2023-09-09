@props([
    'heading' => null,
    'subheading' => null,
])

<div {{ $attributes->class(['fi-simple-page']) }}>
    <section class="grid auto-cols-fr gap-y-6">
        <x-filament-panels::header.simple
            :heading="$heading ??= $this->getHeading()"
            :logo="$this->hasLogo()"
            :subheading="$subheading ??= $this->getSubHeading()"
        />

        {{ $slot }}
    </section>

    @if (! $this instanceof \Filament\Tables\Contracts\HasTable)
        <x-filament-actions::modals />
    @endif
</div>
