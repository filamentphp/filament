@php
    use Filament\Support\Enums\FontFamily;
    use Filament\Support\Enums\FontWeight;
    use Filament\Support\RawJs;

    $color = $getColor();
    $content = $getContent();
    $weight = $getWeight();
    $fontFamily = $getFontFamily();
@endphp

<span>
    @if ($isBadge())
        <x-filament::badge
            :color="$color"
            :icon="$getIcon()"
            :icon-position="$getIconPosition()"
            :icon-size="$getIconSize()"
        >
            {{ $content }}
        </x-filament::badge>
    @else
        <span
            @class([
                'fi-sc-text-decoration break-words text-sm',
                match ($color) {
                    'gray' => 'text-gray-600 dark:text-gray-400',
                    default => 'fi-color-custom text-custom-600 dark:text-custom-400',
                },
                is_string($color) ? "fi-color-{$color}" : null,
                match ($weight) {
                    FontWeight::Thin, 'thin' => 'font-thin',
                    FontWeight::ExtraLight, 'extralight' => 'font-extralight',
                    FontWeight::Light, 'light' => 'font-light',
                    FontWeight::Medium, 'medium' => 'font-medium',
                    FontWeight::SemiBold, 'semibold' => 'font-semibold',
                    FontWeight::Bold, 'bold' => 'font-bold',
                    FontWeight::ExtraBold, 'extrabold' => 'font-extrabold',
                    FontWeight::Black, 'black' => 'font-black',
                    default => $weight,
                },
                match ($fontFamily) {
                    FontFamily::Sans, 'sans' => 'font-sans',
                    FontFamily::Serif, 'serif' => 'font-serif',
                    FontFamily::Mono, 'mono' => 'font-mono',
                    default => $fontFamily,
                },
            ])
            @style([
                \Filament\Support\get_color_css_variables(
                    $color,
                    shades: [400, 600],
                    alias: 'schema::components.decorations.text-decoration',
                ),
            ])
        >
            {{ $content }}
        </span>
    @endif
</span>
