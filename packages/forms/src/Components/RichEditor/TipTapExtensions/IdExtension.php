<?php

namespace Filament\Forms\Components\RichEditor\TipTapExtensions;

use Tiptap\Core\Node;

/**
 * This extension adds support for the `id` attribute on headings and links.
 */
class IdExtension extends Node
{
    /**
     * @var string
     */
    public static $name = 'id';

    /**
     * @return array<array<string, mixed>>
     */
    public function addGlobalAttributes(): array
    {
        return [
            [
                'types' => [
                    'heading',
                    'link',
                ],
                'attributes' => [
                    'id' => [
                        'default' => null,
                        'parseHTML' => function ($DOMNode) {
                            return $DOMNode->hasAttribute('id') ? $DOMNode->getAttribute('id') : null;
                        },
                        'renderHTML' => function ($attributes) {
                            if (! property_exists($attributes, 'id')) {
                                return null;
                            }

                            return [
                                'id' => $attributes->id,
                            ];
                        },
                    ],
                ],
            ],
        ];
    }
}
