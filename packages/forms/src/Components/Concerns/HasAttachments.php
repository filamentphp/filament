<?php

namespace Filament\Forms\Components\Concerns;

trait HasAttachments
{
    protected $attachmentDirectory = 'attachments';

    protected $attachmentDiskName;

    protected $attachmentUploadUrl;

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

    public function enableAttachments($url)
    {
        $this->configure(function () use ($url) {
            $this->attachmentUploadUrl = $url;

            if (method_exists($this, 'enableToolbarButtons')) {
                $this->enableToolbarButtons(['attachFiles']);
            }
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
}
