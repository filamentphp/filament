@props([
    'modals' => null,
    'widgetRecord' => null,
])

<div {{ $attributes->class(['filament-page']) }}>
    <div class="space-y-6">
        @if ($header = $this->getHeader())
            {{ $header }}
        @elseif ($heading = $this->getHeading())
            <x-filament::header :actions="$this->getCachedActions()">
                <x-slot name="heading">
                    {{ $heading }}
                </x-slot>
            </x-filament::header>
        @endif

        @if ($headerWidgets = $this->getHeaderWidgets())
            <x-filament::widgets :widgets="$headerWidgets" :data="['record' => $widgetRecord]" />
        @endif

        {{ $slot }}

        @if ($footerWidgets = $this->getFooterWidgets())
            <x-filament::widgets :widgets="$footerWidgets" :data="['record' => $widgetRecord]" />
        @endif

        @if ($footer = $this->getFooter())
            {{ $footer }}
        @endif
    </div>

    <form wire:submit.prevent="callMountedAction">
        @php
            $action = $this->getMountedAction();
        @endphp

        <x-filament::modal id="page-action" :width="$action?->getModalWidth()" display-classes="block">
            @if ($action)
                @if ($action->isModalCentered())
                    <x-slot name="heading">
                        {{ $action->getModalHeading() }}
                    </x-slot>

                    @if ($subheading = $action->getModalSubheading())
                        <x-slot name="subheading">
                            {{ $subheading }}
                        </x-slot>
                    @endif
                @else
                    <x-slot name="header">
                        <x-filament::modal.heading>
                            {{ $action->getModalHeading() }}
                        </x-filament::modal.heading>
                    </x-slot>
                @endif

                @if ($action->hasFormSchema())
                    {{ $this->getMountedActionForm() }}
                @endif

                <x-slot name="footer">
                    <x-filament::modal.actions :full-width="$action->isModalCentered()">
                        @foreach ($action->getModalActions() as $modalAction)
                            {{ $modalAction }}
                        @endforeach
                    </x-filament::modal.actions>
                </x-slot>
            @endif
        </x-filament::modal>
    </form>

    {{ $modals }}

    @stack('modals')

    <x-filament::notification-manager />
</div>
