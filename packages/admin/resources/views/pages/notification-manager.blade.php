<div
    x-init="Livewire.hook('message.processed', (_, component) => {
        if (component.name !== 'filament.core.pages.notification-manager') {
            $wire.$refresh()
        }
    })"
    class="fixed inset-x-0 top-0 z-10 p-3 pointer-events-none filament-notifications space-y-4"
>
    @foreach($this->notifications as ['message' => $message, 'status' => $status])
        <x-filament::notification :message="$message" :status="$status" />
    @endforeach
</div>
