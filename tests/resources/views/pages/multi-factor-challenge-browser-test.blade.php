<x-filament-panels::page>
    <form wire:submit="verify">
        {{ $this->multiFactorChallengeForm }}

        <x-filament::button type="submit">Verify</x-filament::button>
    </form>

    @if ($isVerified)
        <p>Multi-factor authentication verified.</p>
    @endif
</x-filament-panels::page>
