<?php

namespace Filament\Forms\Components\RichEditor\TipTapExtensions;

use Filament\Forms\Components\RichEditor\TextColor;
use Tiptap\Core\Mark;

class TextColorExtension extends Mark
{
    /**
     * @var string
     */
    public static $name = 'textColor';

    /**
     * @return array<string, mixed>
     */
    public function addOptions(): array
    {
        return [
            'textColors' => [],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function parseHTML(): array
    {
        return [
            [
                'tag' => 'span',
                'getAttrs' => fn ($DOMNode): bool => in_array('color', explode(' ', (string) $DOMNode->getAttribute('class'))),
            ],
        ];
    }

    /**
     * @return array<string, array<mixed>>
     */
    public function addAttributes(): array
    {
        return [
            'data-color' => [
                'parseHTML' => fn ($DOMNode) => $DOMNode->getAttribute('data-color') ?: null,
                'renderHTML' => function ($attributes) {
                    $value = null;

                    if (is_array($attributes)) {
                        $value = $attributes['data-color'] ?? null;
                    } elseif (is_object($attributes)) {
                        $value = $attributes->{'data-color'} ?? ($attributes->dataColor ?? null);
                    }

                    return [
                        'data-color' => $value,
                    ];
                },
            ],
        ];
    }

    /**
     * @param  object  $mark
     * @param  array<string, mixed>  $HTMLAttributes
     * @return array<mixed>
     */
    public function renderHTML($mark, $HTMLAttributes = []): array
    {
        $existingClass = isset($HTMLAttributes['class']) ? (string) $HTMLAttributes['class'] : '';
        $HTMLAttributes['class'] = trim(implode(' ', array_filter(['color', $existingClass])));

        $colorName = $HTMLAttributes['data-color'] ?? null;
        $colors = $this->options['textColors'] ?? [];
        $config = is_string($colorName) ? ($colors[$colorName] ?? null) : null;

        if ($config instanceof TextColor) {
            $cssVars = "--color: {$config->getColor()}; --dark-color: {$config->getDarkColor()}";
        } elseif (filled($colorName)) {
            $cssVars = "--color: {$colorName}; --dark-color: {$colorName}";
        } else {
            $cssVars = null;
        }

        if (filled($cssVars)) {
            $existingStyle = isset($HTMLAttributes['style']) ? (string) $HTMLAttributes['style'] : '';
            $HTMLAttributes['style'] = $existingStyle !== '' ? ($cssVars . '; ' . $existingStyle) : $cssVars;
        }

        return [
            'span',
            $HTMLAttributes,
            0,
        ];
    }
}
