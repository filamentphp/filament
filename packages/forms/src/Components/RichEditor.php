<?php

namespace Filament\Forms\Components;

class RichEditor extends Field implements Contracts\HasFileAttachments
{
    use Concerns\HasExtraAlpineAttributes;
    use Concerns\HasFileAttachments;
    use Concerns\HasPlaceholder;
    use Concerns\InteractsWithToolbarButtons;

    protected string $view = 'forms::components.rich-editor';

    protected $toolbarButtons = [
        'attachFiles',
        'blockquote',
        'bold',
        'bulletList',
        'codeBlock',
        'h2',
        'h3',
        'italic',
        'link',
        'orderedList',
        'redo',
        'strike',
        'undo',
    ];
}
