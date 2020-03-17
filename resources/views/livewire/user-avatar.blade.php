<img class="{{ $classes }}" 
        src="{{ $user->avatar($size) }}" 
        srcset="{{ $user->avatar($size * 2) }} 2x, {{ $user->avatar($size * 3) }} 3x" 
        alt="{{ $user->name }}" 
        width="{{ $size }}" 
        height="{{ $size }}"
        load="lazy">