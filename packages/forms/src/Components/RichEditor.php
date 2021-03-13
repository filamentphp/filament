<?php

namespace Filament\Forms\Components;

class RichEditor extends Field
{
    use Concerns\CanBeAutofocused;
    use Concerns\CanBeCompared;
    use Concerns\CanBeUnique;
    use Concerns\HasPlaceholder;

    protected $attachmentDirectory = 'attachments';

    protected $attachmentDiskName;

    protected $attachmentUploadUrl;

    protected $toolbarButtons = [
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

    protected function setUp()
    {
        $this->attachmentDisk(config('forms.default_filesystem_disk'));

        $attachmentUploadUrl = config('forms.rich_editor.default_attachment_upload_url');
        if ($attachmentUploadUrl) {
            $this->enableAttachments($attachmentUploadUrl);
        }
    }

    public function attachmentDirectory($directory)
    {
        $this->configure(function () use ($directory) {
            $this->attachmentDirectory = $directory;
        });

        return $this;
    }

    public function attachmentDisk($disk)
    {
        $this->configure(function () use ($disk) {
            $this->attachmentDiskName = $disk;
        });

        return $this;
    }

    public function disableAllToolbarButtons()
    {
        $this->configure(function () {
            $this->toolbarButtons = [];
        });

        return $this;
    }

    public function disableToolbarButtons($buttonsToDisable)
    {
        $this->configure(function () use ($buttonsToDisable) {
            if (! is_array($buttonsToDisable)) {
                $buttonsToDisable = [$buttonsToDisable];
            }

            $this->toolbarButtons = collect($this->getToolbarButtons())
                ->filter(fn ($button) => ! in_array($button, $buttonsToDisable))
                ->toArray();
        });

        return $this;
    }

    public function enableAttachments($url)
    {
        $this->configure(function () use ($url) {
            $this->attachmentUploadUrl = $url;

            $this->enableToolbarButtons(['attachFiles']);
        });

        return $this;
    }

    public function enableToolbarButtons($buttonsToEnable)
    {
        $this->configure(function () use ($buttonsToEnable) {
            if (! is_array($buttonsToEnable)) {
                $buttonsToEnable = [$buttonsToEnable];
            }

            $this->toolbarButtons = array_merge($this->getToolbarButtons(), $buttonsToEnable);
        });

        return $this;
    }

    public function getAttachmentDirectory()
    {
        return $this->attachmentDirectory;
    }

    public function getAttachmentDiskName()
    {
        return $this->attachmentDiskName;
    }

    public function getAttachmentUploadUrl()
    {
        return $this->attachmentUploadUrl;
    }

    public function getToolbarButtons()
    {
        return $this->toolbarButtons;
    }

    public function hasToolbarButton($button)
    {
        if (is_array($button)) {
            $buttons = $button;

            return (bool) count(array_intersect($buttons, $this->getToolbarButtons()));
        }

        return in_array($button, $this->getToolbarButtons());
    }

    public function toolbarButtons($buttons)
    {
        $this->configure(function () use ($buttons) {
            $this->toolbarButtons = $buttons;
        });

        return $this;
    }
}
