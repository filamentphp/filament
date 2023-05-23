<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Illuminate\Support\HtmlString;

class FileUpload extends BaseFileUpload
{
    use Concerns\HasExtraInputAttributes;
    use Concerns\HasPlaceholder;
    use HasExtraAlpineAttributes;

    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.file-upload';

    protected string | Closure | null $imageCropAspectRatio = null;

    protected string | Closure | null $imagePreviewHeight = null;

    protected string | Closure | null $imageResizeTargetHeight = null;

    protected string | Closure | null $imageResizeTargetWidth = null;

    protected string | Closure | null $imageResizeMode = null;

    protected bool | Closure $imageResizeUpscale = true;

    protected bool | Closure $isAvatar = false;

    protected string | Closure $loadingIndicatorPosition = 'right';

    protected string | Closure | null $panelAspectRatio = null;

    protected string | Closure | null $panelLayout = 'compact';

    protected string | Closure $removeUploadedFileButtonPosition = 'left';

    protected bool | Closure $shouldAppendFiles = false;

    protected bool | Closure $shouldOrientImagesFromExif = true;

    protected string | Closure $uploadButtonPosition = 'right';

    protected string | Closure $uploadProgressIndicatorPosition = 'right';

    protected bool | Closure $isCroppable = false;

    protected ?int $cropperViewportWidth = null;

    protected ?int $cropperViewportHeight = null;

    protected int $cropperMode = 1;

    protected string $cropperEmptyFillColor = 'transparent';

    protected array | Closure $cropperAspectRatios = [];

    public function appendFiles(bool | Closure $condition = true): static
    {
        $this->shouldAppendFiles = $condition;

        return $this;
    }

    public function avatar(): static
    {
        $this->isAvatar = true;

        $this->image();
        $this->imageCropAspectRatio('1:1');
        $this->imageResizeTargetHeight('500');
        $this->imageResizeTargetWidth('500');
        $this->loadingIndicatorPosition('center bottom');
        $this->panelLayout('compact circle');
        $this->removeUploadedFileButtonPosition('center bottom');
        $this->uploadButtonPosition('center bottom');
        $this->uploadProgressIndicatorPosition('center bottom');
        $this->circularCropper();

        return $this;
    }

    /**
     * @deprecated Use `placeholder()` instead.
     */
    public function idleLabel(string | Closure | null $label): static
    {
        $this->placeholder($label);

        return $this;
    }

    public function image(): static
    {
        $this->acceptedFileTypes([
            'image/*',
        ]);

        return $this;
    }

    public function imageCropAspectRatio(string | Closure | null $ratio): static
    {
        $this->imageCropAspectRatio = $ratio;

        return $this;
    }

    public function imagePreviewHeight(string | Closure | null $height): static
    {
        $this->imagePreviewHeight = $height;

        return $this;
    }

    public function imageResizeTargetHeight(string | Closure | null $height): static
    {
        $this->imageResizeTargetHeight = $height;

        return $this;
    }

    public function imageResizeTargetWidth(string | Closure | null $width): static
    {
        $this->imageResizeTargetWidth = $width;

        return $this;
    }

    public function imageResizeMode(string | Closure | null $mode): static
    {
        $this->imageResizeMode = $mode;

        return $this;
    }

    public function imageResizeUpscale(bool | Closure $condition = true): static
    {
        $this->imageResizeUpscale = $condition;

        return $this;
    }

    public function loadingIndicatorPosition(string | Closure | null $position): static
    {
        $this->loadingIndicatorPosition = $position;

        return $this;
    }

    public function orientImagesFromExif(bool | Closure $condition = true): static
    {
        $this->shouldOrientImagesFromExif = $condition;

        return $this;
    }

    /**
     * @deprecated Use `orientImagesFromExif()` instead.
     */
    public function orientImageFromExif(bool | Closure $condition = true): static
    {
        $this->orientImagesFromExif($condition);

        return $this;
    }

    public function panelAspectRatio(string | Closure | null $ratio): static
    {
        $this->panelAspectRatio = $ratio;

        return $this;
    }

    public function panelLayout(string | Closure | null $layout): static
    {
        $this->panelLayout = $layout;

        return $this;
    }

    public function removeUploadedFileButtonPosition(string | Closure | null $position): static
    {
        $this->removeUploadedFileButtonPosition = $position;

        return $this;
    }

    public function uploadButtonPosition(string | Closure | null $position): static
    {
        $this->uploadButtonPosition = $position;

        return $this;
    }

    public function uploadProgressIndicatorPosition(string | Closure | null $position): static
    {
        $this->uploadProgressIndicatorPosition = $position;

        return $this;
    }

    public function getImageCropAspectRatio(): ?string
    {
        return $this->evaluate($this->imageCropAspectRatio);
    }

    public function getImagePreviewHeight(): ?string
    {
        return $this->evaluate($this->imagePreviewHeight);
    }

    public function getImageResizeTargetHeight(): ?string
    {
        return $this->evaluate($this->imageResizeTargetHeight);
    }

    public function getImageResizeTargetWidth(): ?string
    {
        return $this->evaluate($this->imageResizeTargetWidth);
    }

    public function getImageResizeMode(): ?string
    {
        return $this->evaluate($this->imageResizeMode);
    }

    public function getImageResizeUpscale(): bool
    {
        return (bool) $this->evaluate($this->imageResizeUpscale);
    }

    public function getLoadingIndicatorPosition(): string
    {
        return $this->evaluate($this->loadingIndicatorPosition);
    }

    public function getPanelAspectRatio(): ?string
    {
        return $this->evaluate($this->panelAspectRatio);
    }

    public function getPanelLayout(): ?string
    {
        return $this->evaluate($this->panelLayout);
    }

    public function getRemoveUploadedFileButtonPosition(): string
    {
        return $this->evaluate($this->removeUploadedFileButtonPosition);
    }

    public function getUploadButtonPosition(): string
    {
        return $this->evaluate($this->uploadButtonPosition);
    }

    public function getUploadProgressIndicatorPosition(): string
    {
        return $this->evaluate($this->uploadProgressIndicatorPosition);
    }

    public function isAvatar(): bool
    {
        return (bool) $this->evaluate($this->isAvatar);
    }

    public function shouldAppendFiles(): bool
    {
        return (bool) $this->evaluate($this->shouldAppendFiles);
    }

    public function shouldOrientImagesFromExif(): bool
    {
        return (bool) $this->evaluate($this->shouldOrientImagesFromExif);
    }

    public function croppable(bool | Closure $condition = true): static
    {
        $this->isCroppable = $condition;

        return $this;
    }

    public function cropperViewportWidth(int $width): static
    {
        $this->cropperViewportWidth = $width;

        return $this;
    }

    public function cropperViewportHeight(int $height): static
    {
        $this->cropperViewportHeight = $height;

        return $this;
    }

    // https://github.com/fengyuanchen/cropperjs#viewmode
    public function cropperMode(int $mode): static
    {
        //setting data via inputs won't work with viewMode 0 src: https://github.com/fengyuanchen/cropperjs#setdatadata, see "note"
        if (in_array($mode, [1, 2, 3])) {
            $this->cropperMode = $mode;
        }

        return $this;
    }

    // https://github.com/fengyuanchen/cropperjs#getcroppedcanvasoptions
    public function cropperEmptyFillColor(string $color): static
    {
        $this->cropperEmptyFillColor = $color;

        return $this;
    }

    public function cropperAspectRatios(array $ratios): static
    {
        $this->cropperAspectRatios = $ratios;

        return $this;
    }

    public function getCropperViewportHeight(): ?int
    {
        if (($targetHeight = (int) $this->getImageResizeTargetHeight()) > 1) {
            return (int) round($targetHeight * $this->getParentTargetSizes($targetHeight), 0);
        }

        if (is_string($ratio = $this->getImageCropAspectRatio())) {
            [$numerator, $denominator] = explode(':', $ratio);

            return $denominator;
        }

        return $this->cropperViewportHeight;
    }

    public function getCropperViewportWidth(): ?int
    {
        if (($targetWidth = (int) $this->getImageResizeTargetWidth()) > 1) {
            return (int) round($targetWidth * $this->getParentTargetSizes($targetWidth), 0);
        }

        if (is_string($ratio = $this->getImageCropAspectRatio())) {
            [$numerator, $denominator] = explode(':', $ratio);

            return $numerator;
        }

        return $this->cropperViewportWidth;
    }

    protected function getParentTargetSizes(int $withOrHeight): float
    {
        return $withOrHeight > 1 ? 360 / (int) $this->getImageResizeTargetWidth() : 1;
    }

    public function getCropperShape(): string
    {
        return $this->isAvatar ? 'circle' : $this->cropperShape;
    }

    public function getCropperMode(): int
    {
        return $this->isAvatar ? 1 : $this->cropperMode;
    }

    public function getCropperEmptyFillColor(): string
    {
        return $this->cropperEmptyFillColor;
    }

    public function isCroppable(): bool
    {
        return (bool) $this->evaluate($this->isCroppable);
    }

    public function getCropperAspectRatios(): array
    {
        $ratios = $this->evaluate($this->cropperAspectRatios);

        $ratios[] = $this->getImageCropAspectRatio();

        if (is_array($ratios)) {
            $compiled = [];

            foreach (array_unique($ratios) as $ratio) {
                if ($validRatio = $this->makeRatio($ratio)) {
                    $compiled[$ratio ?? __('filament-forms::components.file_upload.cropper.actions.free.label')] = $validRatio;
                }
            }

            //don't output any aspect ratio buttons if there is only one, nothing to toggle...
            return count($compiled) > 1 ? $compiled : [];
        }

        return [];
    }

    protected function makeRatio(null | string | int $ratio): float | int | bool | string
    {
        if ($ratio === null) {
            return 'NaN';
        }

        $parts = explode(':', $ratio);

        if (count($parts) !== 2) {
            return false;
        }

        [$numerator, $denominator] = $parts;

        if ($denominator == 0 || ! is_numeric($numerator) || ! is_numeric($denominator)) {
            return false;
        }

        return $numerator / $denominator;
    }

    public function getCropperActions(): array
    {
        $size = 'h-5 w-5 mx-auto';

        return [
            'zoom' => [
                [
                    'tooltip' => __('filament-forms::components.file_upload.cropper.actions.drag_move.label'),
                    'icon' => new HtmlString('<svg class="' . $size . '" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M13 6v5h5V7.75L22.25 12L18 16.25V13h-5v5h3.25L12 22.25L7.75 18H11v-5H6v3.25L1.75 12L6 7.75V11h5V6H7.75L12 1.75L16.25 6H13Z"/></svg>'),
                    'click' => "cropper.setDragMode('move')",
                ],
                [
                    'tooltip' => __('filament-forms::components.file_upload.cropper.actions.drag_crop.label'),
                    'icon' => new HtmlString('<svg class="' . $size . '" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M17 23v-4H7q-.825 0-1.412-.587Q5 17.825 5 17V7H1V5h4V1h2v16h16v2h-4v4Zm0-8V7H9V5h8q.825 0 1.413.588Q19 6.175 19 7v8Z"/></svg>'),
                    'click' => "cropper.setDragMode('crop')",
                ],
                [
                    'tooltip' => __('filament-forms::components.file_upload.cropper.actions.zoom_in.label'),
                    'icon' => svg('heroicon-o-magnifying-glass-plus', $size)->toHtml(),
                    'click' => 'cropper.zoom(0.1)',
                ],
                [
                    'tooltip' => __('filament-forms::components.file_upload.cropper.actions.zoom_out.label'),
                    'icon' => svg('heroicon-o-magnifying-glass-minus', $size)->toHtml(),
                    'click' => 'cropper.zoom(-0.1)',
                ],
                [
                    'tooltip' => __('filament-forms::components.file_upload.cropper.actions.zoom_100.label'),
                    'icon' => svg('heroicon-o-arrows-pointing-out', $size)->toHtml(),
                    'click' => 'cropper.zoomTo(1)',
                ],
            ],
            'move' => [
                [
                    'tooltip' => __('filament-forms::components.file_upload.cropper.actions.move_left.label'),
                    'icon' => svg('heroicon-o-arrow-left-circle', $size)->toHtml(),
                    'click' => 'cropper.move(-10, 0)',
                ],
                [
                    'tooltip' => __('filament-forms::components.file_upload.cropper.actions.move_right.label'),
                    'icon' => svg('heroicon-o-arrow-right-circle', $size)->toHtml(),
                    'click' => 'cropper.move(10, 0)',
                ],
                [
                    'tooltip' => __('filament-forms::components.file_upload.cropper.actions.move_up.label'),
                    'icon' => svg('heroicon-o-arrow-up-circle', $size)->toHtml(),
                    'click' => 'cropper.move(0, -10)',
                ],
                [
                    'tooltip' => __('filament-forms::components.file_upload.cropper.actions.move_down.label'),
                    'icon' => svg('heroicon-o-arrow-down-circle', $size)->toHtml(),
                    'click' => 'cropper.move(0, 10)',
                ],
                [
                    'tooltip' => __('filament-forms::components.file_upload.cropper.actions.move_top_left.label'),
                    'icon' => new HtmlString('<svg  class="' . $size . '" width="24" height="24" stroke-width="1.5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 16.01L4.01 15.9989" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M4 20.01L4.01 19.9989" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8 20.01L8.01 19.9989" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 20.01L12.01 19.9989" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M16 20.01L16.01 19.9989" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M20 20.01L20.01 19.9989" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M20 16.01L20.01 15.9989" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M20 12.01L20.01 11.9989" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M20 8.01L20.01 7.99889" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M20 4.01L20.01 3.99889" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M16 4.01L16.01 3.99889" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M4 12V4H12V12H4Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>'),
                    'click' => 'cropper.moveTo(0, 0)',
                ],
            ],
            'rotate_flip' => [
                [
                    'tooltip' => __('filament-forms::components.file_upload.cropper.actions.rotate_left.label'),
                    'icon' => svg('heroicon-o-arrow-uturn-left', $size)->toHtml(),
                    'click' => 'cropper.rotate(-45)',
                ],
                [
                    'tooltip' => __('filament-forms::components.file_upload.cropper.actions.rotate_right.label'),
                    'icon' => svg('heroicon-o-arrow-uturn-right', $size)->toHtml(),
                    'click' => 'cropper.rotate(45)',
                ],
                [
                    'tooltip' => __('filament-forms::components.file_upload.cropper.actions.rotate_180.label'),
                    'icon' => svg('heroicon-o-arrow-uturn-down', $size)->toHtml(),
                    'click' => 'cropper.rotate(180)',
                ],
                [
                    'tooltip' => __('filament-forms::components.file_upload.cropper.actions.flip_horizontal.label'),
                    'icon' => new HtmlString('<svg class="' . $size . '" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m17 3l-5 5l-5-5h10m0 18l-5-5l-5 5h10M4 12H2m8 0H8m8 0h-2m8 0h-2"/></svg>'),
                    'click' => 'cropper.scaleX(-cropper.getData().scaleX || -1)',
                ],
                [
                    'tooltip' => __('filament-forms::components.file_upload.cropper.actions.flip_vertical.label'),
                    'icon' => new HtmlString('<svg class="' . $size . '" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m3 7l5 5l-5 5V7m18 0l-5 5l5 5V7m-9 13v2m0-8v2m0-8v2m0-8v2"/></svg>'),
                    'click' => 'cropper.scaleY(-cropper.getData().scaleY || -1)',
                ],
            ],
        ];
    }
}
