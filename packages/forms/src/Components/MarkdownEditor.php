<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Support\Concerns\CanConfigureCommonMark;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use LogicException;

class MarkdownEditor extends Field implements Contracts\CanBeLengthConstrained
{
    use CanConfigureCommonMark;
    use Concerns\CanBeLengthConstrained;
    use Concerns\HasFileAttachments;
    use Concerns\HasMaxHeight;
    use Concerns\HasMinHeight;
    use Concerns\HasPlaceholder;
    use Concerns\InteractsWithToolbarButtons;
    use HasExtraAlpineAttributes;

    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.markdown-editor';

    /**
     * @return array<string | array<string>>
     */
    public function getDefaultToolbarButtons(): array
    {
        return [
            ['bold', 'italic', 'strike', 'link'],
            ['heading'],
            ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
            [
                'table',
                ...($this->hasFileAttachments(default: true) ? ['attachFiles'] : []),
            ],
            ['undo', 'redo'],
        ];
    }

    public function getFileAttachmentsDiskName(): string
    {
        $name = $this->evaluate($this->fileAttachmentsDiskName);

        if (filled($name)) {
            return $name;
        }

        $defaultName = config('filament.default_filesystem_disk');

        return ($defaultName === 'local') ? 'public' : $defaultName;
    }

    public function fileAttachmentsVisibility(string | Closure | null $visibility): static
    {
        throw new LogicException('The visibility of file attachments for markdown content is always `public`, since generating temporary file upload URLs is not supported in static content.');
    }

    public function getFileAttachmentsVisibility(): string
    {
        return 'public';
    }

    public function hasFileAttachmentsByDefault(): bool
    {
        return $this->hasToolbarButton('attachFiles');
    }
}
