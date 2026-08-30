@php
    use Filament\Enums\UserMenuPosition;
@endphp

<div>
    <div class="fi-simple-user-menu-ctn">
        <x-filament-panels::user-menu :position="UserMenuPosition::Topbar" />
    </div>

    <x-filament-actions::modals />
</div>
