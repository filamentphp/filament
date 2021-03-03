<?php

namespace Filament\Forms\Components;

use Illuminate\Support\Str;

class FileUpload extends Field
{
    use Concerns\HasPlaceholder;

    public $acceptedFileTypes = [];

    public $directory;

    public $disk;

    public $imageCropAspectRatio;

    public $imagePreviewHeight;

    public $imageResizeTargetHeight;

    public $imageResizeTargetWidth;

    public $loadingIndicatorPosition = 'right';

    public $maxSize;

    public $minSize;

    public $panelAspectRatio = null;

    public $panelLayout = null;

    public $removeUploadButtonPosition = 'left';

    public $uploadButtonPosition = 'right';

    public $uploadProgressIndicatorPosition = 'right';

    public $visibility = 'public';

    protected function setup()
    {
        $this->addRules([$this->getTemporaryUploadedFilePropertyName() => ['nullable', 'file']]);
        $this->disk(config('forms.default_filesystem_disk'));
    }

    public function acceptedFileTypes($types)
    {
        if (! is_array($types)) $types = explode(',', $types);

        $this->acceptedFileTypes = $types;

        $types = implode(',', $types);

        $this->addRules([$this->getTemporaryUploadedFilePropertyName() => ["mimetypes:{$types}"]]);

        return $this;
    }

    public function avatar()
    {
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

        return $this;
    }

    public function disk($disk)
    {
        $this->disk = $disk;

        return $this;
    }

    public function directory($directory)
    {
        $this->directory = $directory;

        return $this;
    }

    public function getTemporaryUploadedFilePropertyName()
    {
        return "temporaryUploadedFiles.{$this->name}";
    }

    public function getValidationAttributes()
    {
        $attributes = parent::getValidationAttributes();

        $attributes = array_merge(
            $attributes,
            [$this->getTemporaryUploadedFilePropertyName() => Str::lower($this->label)],
        );

        return $attributes;
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
        $this->imageCropAspectRatio = $ratio;

        return $this;
    }

    public function imagePreviewHeight($height)
    {
        $this->imagePreviewHeight = $height;

        return $this;
    }

    public function imageResizeTargetHeight($height)
    {
        $this->imageResizeTargetHeight = $height;

        return $this;
    }

    public function imageResizeTargetWidth($width)
    {
        $this->imageResizeTargetWidth = $width;

        return $this;
    }

    public function loadingIndicatorPosition($position)
    {
        $this->loadingIndicatorPosition = $position;

        return $this;
    }

    public function maxSize($size)
    {
        $this->maxSize = $size;

        $this->addRules([$this->getTemporaryUploadedFilePropertyName() => ["max:{$this->maxSize}"]]);

        return $this;
    }

    public function minSize($size)
    {
        $this->minSize = $size;

        $this->addRules([$this->getTemporaryUploadedFilePropertyName() => ["min:{$this->minSize}"]]);

        return $this;
    }

    public function panelAspectRatio($ratio)
    {
        $this->panelAspectRatio = $ratio;

        return $this;
    }

    public function panelLayout($layout)
    {
        $this->panelLayout = $layout;

        return $this;
    }

    public function removeUploadButtonPosition($position)
    {
        $this->removeUploadButtonPosition = $position;

        return $this;
    }

    public function uploadButtonPosition($position)
    {
        $this->uploadButtonPosition = $position;

        return $this;
    }

    public function uploadProgressIndicatorPosition($position)
    {
        $this->uploadProgressIndicatorPosition = $position;

        return $this;
    }

    public function visibility($visibility)
    {
        $this->visibility = $visibility;

        return $this;
    }
}
