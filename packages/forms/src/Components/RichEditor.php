<?php

namespace Filament\Forms\Components;

class RichEditor extends Field
{
    use Concerns\CanBeAutofocused;
    use Concerns\CanBeCompared;
    use Concerns\CanBeUnique;
    use Concerns\HasPlaceholder;

    public $attachmentDirectory = 'attachments';

    public $attachmentDisk;

    public $attachmentUploadUrl;

    public $toolbarButtons = [
        'bold',
        'bullet',
        'code',
        'heading',
        'italic',
        'link',
        'number',
        'quote',
        'redo',
        'strike',
        'subheading',
        'undo',
    ];

    public function __construct($name)
    {
        parent::__construct($name);

        $this->attachmentDisk(config('forms.default_filesystem_disk'));

        $attachmentUploadUrl = config('forms.rich_editor.default_attachment_upload_url');
        if ($attachmentUploadUrl) $this->enableAttachments($attachmentUploadUrl);
    }

    public function attachmentDirectory($directory)
    {
        $this->attachmentDirectory = $directory;

        return $this;
    }

    public function attachmentDisk($disk)
    {
        $this->attachmentDisk = $disk;

        return $this;
    }

    public function disableToolbarButtons($buttonsToDisable)
    {
        if (! is_array($buttonsToDisable)) $buttonsToDisable = [$buttonsToDisable];

        $this->toolbarButtons = array_merge($this->toolbarButtons, $buttonsToDisable);

        $this->toolbarButtons = collect($this->toolbarButtons)
            ->filter(fn ($button) => ! in_array($button, $buttonsToDisable))
            ->toArray();

        return $this;
    }

    public function enableAttachments($url)
    {
        $this->attachmentUploadUrl = $url;

        $this->enableToolbarButtons(['attachFiles']);

        return $this;
    }

    public function enableToolbarButtons($buttonsToEnable)
    {
        if (! is_array($buttonsToEnable)) $buttonsToEnable = [$buttonsToEnable];

        $this->toolbarButtons = array_merge($this->toolbarButtons, $buttonsToEnable);

        return $this;
    }

    public function hasToolbarButton($button)
    {
        if (is_array($button)) {
            $buttons = $button;

            return (bool) count(array_intersect($buttons, $this->toolbarButtons));
        }

        return in_array($button, $this->toolbarButtons);
    }

    public function toolbarButtons($buttons)
    {
        $this->toolbarButtons = $buttons;

        return $this;
    }
}
