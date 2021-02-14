<img {{ $attributes->merge([
    'src' => $src(),
    'srcset' => $srcSet(),
    'alt' => $user->name,
    'width' => $size,
    'height' => $size,
    'loading' => 'lazy'
]) }} />
