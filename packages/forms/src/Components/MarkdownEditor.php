<?php

namespace Filament\Forms\Components;

use Closure;

class MarkdownEditor extends Field implements Contracts\HasFileAttachments
{
    use Concerns\HasExtraAlpineAttributes;
    use Concerns\HasFileAttachments;
    use Concerns\HasPlaceholder;
    use Concerns\InteractsWithToolbarButtons;

    protected string $view = 'forms::components.markdown-editor';

    protected array | Closure $toolbarButtons = [
        'attachFiles',
        'bold',
        'bulletList',
        'codeBlock',
        'edit',
        'italic',
        'link',
        'orderedList',
        'preview',
        'strike',
    ];
}
