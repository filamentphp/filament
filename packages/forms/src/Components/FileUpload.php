<?php

namespace Filament\Forms\Components;

use Illuminate\Support\Str;

class FileUpload extends Field
{
    use Concerns\HasPlaceholder;

    protected $acceptedFileTypes = [];

    protected $directory;

    protected $diskName;

    protected $imageCropAspectRatio;

    protected $imagePreviewHeight;

    protected $imageResizeTargetHeight;

    protected $imageResizeTargetWidth;

    protected $loadingIndicatorPosition = 'right';

    protected $maxSize;

    protected $minSize;

    protected $panelAspectRatio = null;

    protected $panelLayout = null;

    protected $removeUploadButtonPosition = 'left';

    protected $uploadButtonPosition = 'right';

    protected $uploadProgressIndicatorPosition = 'right';

    protected $visibility = 'public';

    protected function setUp()
    {
        $this->addRules([$this->getTemporaryUploadedFilePropertyName() => ['nullable', 'file']]);
    }

    public function acceptedFileTypes($types)
    {
        $this->configure(function () use ($types) {
            if (! is_array($types)) $types = explode(',', $types);

            $this->acceptedFileTypes = $types;

            $types = implode(',', $types);

            $this->addRules([$this->getTemporaryUploadedFilePropertyName() => ["mimetypes:{$types}"]]);
        });

        return $this;
    }

    public function avatar()
    {
        $this->configure(function () {
            $this->extraAttributes(['class' => 'w-28']);
            $this->image();
            $this->imageCropAspectRatio('1:1');
            $this->imagePreviewHeight(110);
            $this->imageResizeTargetHeight(500);
            $this->imageResizeTargetWidth(500);
            $this->loadingIndicatorPosition('center bottom');
            $this->panelLayout('compact circle');
            $this->removeUploadButtonPosition('center bottom');
            $this->uploadButtonPosition('center bottom');
            $this->uploadProgressIndicatorPosition('center bottom');
        });

        return $this;
    }

    public function disk($name)
    {
        $this->configure(function () use ($name) {
            $this->diskName = $name;
        });

        return $this;
    }

    public function directory($directory)
    {
        $this->configure(function () use ($directory) {
            $this->directory = $directory;
        });

        return $this;
    }

    public function getAcceptedFileTypes()
    {
        return $this->acceptedFileTypes;
    }

    public function getDirectory()
    {
        return $this->directory;
    }

    public function getDiskName()
    {
        return $this->diskName ?? config('forms.default_filesystem_disk');
    }

    public function getImageCropAspectRatio()
    {
        return $this->imageCropAspectRatio;
    }

    public function getImagePreviewHeight()
    {
        return $this->imagePreviewHeight;
    }

    public function getImageResizeTargetHeight()
    {
        return $this->imageResizeTargetHeight;
    }

    public function getImageResizeTargetWidth()
    {
        return $this->imageResizeTargetWidth;
    }

    public function getLoadingIndicatorPosition()
    {
        return $this->loadingIndicatorPosition;
    }

    public function getMaxSize()
    {
        return $this->maxSize;
    }

    public function getMinSize()
    {
        return $this->minSize;
    }

    public function getPanelAspectRatio()
    {
        return $this->panelAspectRatio;
    }

    public function getPanelLayout()
    {
        return $this->panelLayout;
    }

    public function getRemoveUploadButtonPosition()
    {
        return $this->removeUploadButtonPosition;
    }

    public function getTemporaryUploadedFilePropertyName()
    {
        return "temporaryUploadedFiles.{$this->getName()}";
    }

    public function getUploadButtonPosition()
    {
        return $this->uploadButtonPosition;
    }

    public function getUploadProgressIndicatorPosition()
    {
        return $this->uploadProgressIndicatorPosition;
    }

    public function getValidationAttributes()
    {
        $attributes = parent::getValidationAttributes();

        $attributes = array_merge(
            $attributes,
            [$this->getTemporaryUploadedFilePropertyName() => Str::lower($this->getLabel())],
        );

        return $attributes;
    }

    public function getVisibility()
    {
        return $this->visibility;
    }

    public function image()
    {
        $this->acceptedFileTypes([
            'image/*',
        ]);

        return $this;
    }

    public function imageCropAspectRatio($ratio)
    {
        $this->configure(function () use ($ratio) {
            $this->imageCropAspectRatio = $ratio;
        });

        return $this;
    }

    public function imagePreviewHeight($height)
    {
        $this->configure(function () use ($height) {
            $this->imagePreviewHeight = $height;
        });

        return $this;
    }

    public function imageResizeTargetHeight($height)
    {
        $this->configure(function () use ($height) {
            $this->imageResizeTargetHeight = $height;
        });

        return $this;
    }

    public function imageResizeTargetWidth($width)
    {
        $this->configure(function () use ($width) {
            $this->imageResizeTargetWidth = $width;
        });

        return $this;
    }

    public function loadingIndicatorPosition($position)
    {
        $this->configure(function () use ($position) {
            $this->loadingIndicatorPosition = $position;
        });

        return $this;
    }

    public function maxSize($size)
    {
        $this->configure(function () use ($size) {
            $this->maxSize = $size;

            $this->addRules([$this->getTemporaryUploadedFilePropertyName() => ["max:{$this->getMaxSize()}"]]);
        });

        return $this;
    }

    public function minSize($size)
    {
        $this->configure(function () use ($size) {
            $this->minSize = $size;

            $this->addRules([$this->getTemporaryUploadedFilePropertyName() => ["min:{$this->getMinSize()}"]]);
        });

        return $this;
    }

    public function panelAspectRatio($ratio)
    {
        $this->configure(function () use ($ratio) {
            $this->panelAspectRatio = $ratio;
        });

        return $this;
    }

    public function panelLayout($layout)
    {
        $this->configure(function () use ($layout) {
            $this->panelLayout = $layout;
        });

        return $this;
    }

    public function removeUploadButtonPosition($position)
    {
        $this->configure(function () use ($position) {
            $this->removeUploadButtonPosition = $position;
        });

        return $this;
    }

    public function uploadButtonPosition($position)
    {
        $this->configure(function () use ($position) {
            $this->uploadButtonPosition = $position;
        });

        return $this;
    }

    public function uploadProgressIndicatorPosition($position)
    {
        $this->configure(function () use ($position) {
            $this->uploadProgressIndicatorPosition = $position;
        });

        return $this;
    }

    public function visibility($visibility)
    {
        $this->configure(function () use ($visibility) {
            $this->visibility = $visibility;
        });

        return $this;
    }
}
