<?php

namespace Filament\Forms\Components;

use Closure;

class RichEditor extends Field implements Contracts\HasFileAttachments
{
    use Concerns\HasExtraAlpineAttributes;
    use Concerns\HasFileAttachments;
    use Concerns\HasPlaceholder;
    use Concerns\InteractsWithToolbarButtons;

    protected Closure | bool $shouldDisableFileAttachmentCaptions = false;

    protected string $view = 'forms::components.rich-editor';

    protected array | Closure $toolbarButtons = [
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

    public function disableFileAttachmentCaptions(bool | Closure $condition = true): static
    {
        $this->shouldDisableFileAttachmentCaptions = $condition;

        return $this;
    }

    public function shouldDisableFileAttachmentCaptions(): bool
    {
        return $this->evaluate($this->shouldDisableFileAttachmentCaptions);
    }
}
