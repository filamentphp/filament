<div class="pointer-events-none fixed top-4 right-6 z-30 ml-6 w-full max-w-md">
    @foreach($notifications->reverse() as $notification)
        {{ $notification }}
    @endforeach
</div>
