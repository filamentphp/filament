@props([
    'modals' => null,
    'widgetRecord' => null,
])

<div {{ $attributes }}>
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

        <x-tables::modal :id="\Illuminate\Support\Str::of(static::class)->replace('\\', '\\\\') . '-action'" :width="$action?->getModalWidth()" display-classes="block">
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
                        <x-tables::modal.heading>
                            {{ $action->getModalHeading() }}
                        </x-tables::modal.heading>
                    </x-slot>
                @endif

                @if ($action->hasFormSchema())
                    {{ $this->getMountedActionForm() }}
                @endif

                <x-slot name="footer">
                    <x-tables::modal.actions :full-width="$action->isModalCentered()">
                        @foreach ($action->getModalActions() as $modalAction)
                            {{ $modalAction }}
                        @endforeach
                    </x-tables::modal.actions>
                </x-slot>
            @endif
        </x-tables::modal>
    </form>

    {{ $modals }}

    @if ($notification = session()->get('notification'))
        <x-filament::notification :message="$notification['message']" :status="$notification['status']" />
    @endif
</div>
