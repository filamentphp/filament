<span class="flex {{ $classes }}">
    <img src="{{ $user->avatar($size) }}" 
        srcset="{{ $user->avatar($size * 2) }} 2x" 
        alt="{{ $user->name }}" 
        width="{{ $size }}" 
        height="{{ $size }}"
        load="lazy">
</span>