<?php

namespace Filament\Forms\Components\RichEditor\TipTapExtensions;

use Tiptap\Core\Node;

class RawHtmlMergeTagExtension extends Node
{
    /**
     * @var string
     */
    public static $name = 'rawHtmlMergeTag';

    /**
     * @param  object  $node
     * @return array<mixed>
     */
    public function renderHTML($node): array
    {
        return ['content' => $node->html];
    }
}
