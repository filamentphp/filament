<div x-data="{ message: @entangle('message') }" x-show.transition="message" x-cloak {{ $attributes->merge(['class' => 'transition-all duration-300 rounded overflow-hidden text-center text-sm font-semibold leading-tight text-blue']) }}>
    <div x-text="message"></div>
</div>