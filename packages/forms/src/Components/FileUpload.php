<?php

namespace Filament\Forms\Components;

use Closure;
use Exception;
use Filament\Support\Concerns\HasAlignment;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

class FileUpload extends BaseFileUpload
{
    use Concerns\HasExtraInputAttributes;
    use Concerns\HasPlaceholder;
    use HasAlignment;
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

    protected bool | Closure $hasCroppableImages = false;

    protected int | Closure | null $imageCropperViewportWidth = null;

    protected int | Closure | null $imageCropperViewportHeight = null;

    protected int $imageCropperMode = 1;

    protected string | Closure $imageCropperEmptyFillColor = 'transparent';

    /**
     * @var array<?string> | Closure
     */
    protected array | Closure $imageCropperAspectRatios = [];

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

    public function croppableImages(bool | Closure $condition = true): static
    {
        $this->hasCroppableImages = $condition;

        return $this;
    }

    public function imageCropperViewportWidth(int | Closure | null $width): static
    {
        $this->imageCropperViewportWidth = $width;

        return $this;
    }

    public function imageCropperViewportHeight(int | Closure | null $height): static
    {
        $this->imageCropperViewportHeight = $height;

        return $this;
    }

    public function imageCropperMode(int $mode): static
    {
        if (! in_array($mode, [1, 2, 3])) {
            throw new Exception("The file upload cropper mode must be either 1, 2 or 3. [{$mode}] given, which is unsupported. See https://github.com/fengyuanchen/cropperjs#viewmode for more information on the available modes. Mode 0 is not supported, as it does not allow configuration via manual inputs.");
        }

        $this->imageCropperMode = $mode;

        return $this;
    }

    public function imageCropperEmptyFillColor(string | Closure $color): static
    {
        $this->imageCropperEmptyFillColor = $color;

        return $this;
    }

    /**
     * @param array<?string> | Closure $ratios
     */
    public function imageCropperAspectRatios(array | Closure $ratios): static
    {
        $this->imageCropperAspectRatios = $ratios;

        return $this;
    }

    public function getImageCropperViewportHeight(): ?int
    {
        if (($targetHeight = (int) $this->getImageResizeTargetHeight()) > 1) {
            return (int) round($targetHeight * $this->getParentTargetSizes($targetHeight), precision: 0);
        }

        if (filled($ratio = $this->getImageCropAspectRatio())) {
            [$numerator, $denominator] = explode(':', $ratio);

            return (int) $denominator;
        }

        return $this->evaluate($this->imageCropperViewportHeight);
    }

    public function getImageCropperViewportWidth(): ?int
    {
        if (($targetWidth = (int) $this->getImageResizeTargetWidth()) > 1) {
            return (int) round($targetWidth * $this->getParentTargetSizes($targetWidth), precision: 0);
        }

        if (filled($ratio = $this->getImageCropAspectRatio())) {
            [$numerator, $denominator] = explode(':', $ratio);

            return (int) $numerator;
        }

        return $this->evaluate($this->imageCropperViewportWidth);
    }

    protected function getParentTargetSizes(int $withOrHeight): float
    {
        return $withOrHeight > 1 ? 360 / (int) $this->getImageResizeTargetWidth() : 1;
    }

    public function getImageCropperMode(): int
    {
        return $this->imageCropperMode;
    }

    public function getImageCropperEmptyFillColor(): string
    {
        return $this->evaluate($this->imageCropperEmptyFillColor);
    }

    public function hasCroppableImages(): bool
    {
        return (bool) $this->evaluate($this->hasCroppableImages);
    }

    /**
     * @return array<string, float | string>
     */
    public function getImageCropperAspectRatiosForJs(): array
    {
        return collect($this->evaluate($this->imageCropperAspectRatios) ?? [])
            ->when(
                filled($imageCropAspectRatio = $this->getImageCropAspectRatio()),
                fn (Collection $ratios): Collection => $ratios->push($imageCropAspectRatio),
            )
            ->unique()
            ->mapWithKeys(fn (?string $ratio): array => [
                $ratio ?? __('filament-forms::components.file_upload.cropper.aspect_ratios.no_fixed.label') => $this->normalizeImageCroppingRatioForJs($ratio),
            ])
            ->filter(fn (float | string | false $ratio): bool => $ratio !== false)
            ->when(
                fn (Collection $ratios): bool => $ratios->count() < 2,
                fn (Collection $ratios): Collection => $ratios->take(0),
            )
            ->all();
    }

    protected function normalizeImageCroppingRatioForJs(?string $ratio): float | string | false
    {
        if ($ratio === null) {
            return 'NaN';
        }

        $ratioParts = explode(':', $ratio);

        if (count($ratioParts) !== 2) {
            return false;
        }

        [$numerator, $denominator] = $ratioParts;

        if (! $denominator) {
            return false;
        }

        if (! is_numeric($numerator)) {
            return false;
        }

        if (! is_numeric($denominator)) {
            return false;
        }

        return $numerator / $denominator;
    }

    /**
     * @return array<array<array<string, mixed>>>
     */
    public function getImageCropperActions(string $iconSizeClasses): array
    {
        return [
            'zoom' => [
                [
                    'label' => __('filament-forms::components.file_upload.cropper.actions.drag_move.label'),
                    'iconHtml' => new HtmlString('<svg class="' . $iconSizeClasses . '" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M13 6v5h5V7.75L22.25 12L18 16.25V13h-5v5h3.25L12 22.25L7.75 18H11v-5H6v3.25L1.75 12L6 7.75V11h5V6H7.75L12 1.75L16.25 6H13Z"/></svg>'),
                    'alpineClickHandler' => "cropper.setDragMode('move')",
                ],
                [
                    'label' => __('filament-forms::components.file_upload.cropper.actions.drag_crop.label'),
                    'iconHtml' => new HtmlString('<svg class="' . $iconSizeClasses . '" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M17 23v-4H7q-.825 0-1.412-.587Q5 17.825 5 17V7H1V5h4V1h2v16h16v2h-4v4Zm0-8V7H9V5h8q.825 0 1.413.588Q19 6.175 19 7v8Z"/></svg>'),
                    'alpineClickHandler' => "cropper.setDragMode('crop')",
                ],
                [
                    'label' => __('filament-forms::components.file_upload.cropper.actions.zoom_in.label'),
                    'iconHtml' => svg('heroicon-o-magnifying-glass-plus', $iconSizeClasses)->toHtml(),
                    'alpineClickHandler' => 'cropper.zoom(0.1)',
                ],
                [
                    'label' => __('filament-forms::components.file_upload.cropper.actions.zoom_out.label'),
                    'iconHtml' => svg('heroicon-o-magnifying-glass-minus', $iconSizeClasses)->toHtml(),
                    'alpineClickHandler' => 'cropper.zoom(-0.1)',
                ],
                [
                    'label' => __('filament-forms::components.file_upload.cropper.actions.zoom_100.label'),
                    'iconHtml' => svg('heroicon-o-arrows-pointing-out', $iconSizeClasses)->toHtml(),
                    'alpineClickHandler' => 'cropper.zoomTo(1)',
                ],
            ],
            'move' => [
                [
                    'label' => __('filament-forms::components.file_upload.cropper.actions.move_left.label'),
                    'iconHtml' => svg('heroicon-o-arrow-left-circle', $iconSizeClasses)->toHtml(),
                    'alpineClickHandler' => 'cropper.move(-10, 0)',
                ],
                [
                    'label' => __('filament-forms::components.file_upload.cropper.actions.move_right.label'),
                    'iconHtml' => svg('heroicon-o-arrow-right-circle', $iconSizeClasses)->toHtml(),
                    'alpineClickHandler' => 'cropper.move(10, 0)',
                ],
                [
                    'label' => __('filament-forms::components.file_upload.cropper.actions.move_up.label'),
                    'iconHtml' => svg('heroicon-o-arrow-up-circle', $iconSizeClasses)->toHtml(),
                    'alpineClickHandler' => 'cropper.move(0, -10)',
                ],
                [
                    'label' => __('filament-forms::components.file_upload.cropper.actions.move_down.label'),
                    'iconHtml' => svg('heroicon-o-arrow-down-circle', $iconSizeClasses)->toHtml(),
                    'alpineClickHandler' => 'cropper.move(0, 10)',
                ],
            ],
            'transform' => [
                [
                    'label' => __('filament-forms::components.file_upload.cropper.actions.rotate_left.label'),
                    'iconHtml' => svg('heroicon-o-arrow-uturn-left', $iconSizeClasses)->toHtml(),
                    'alpineClickHandler' => 'cropper.rotate(-90)',
                ],
                [
                    'label' => __('filament-forms::components.file_upload.cropper.actions.rotate_right.label'),
                    'iconHtml' => svg('heroicon-o-arrow-uturn-right', $iconSizeClasses)->toHtml(),
                    'alpineClickHandler' => 'cropper.rotate(90)',
                ],
                [
                    'label' => __('filament-forms::components.file_upload.cropper.actions.flip_horizontal.label'),
                    'iconHtml' => new HtmlString('<svg class="' . $iconSizeClasses . '" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m17 3l-5 5l-5-5h10m0 18l-5-5l-5 5h10M4 12H2m8 0H8m8 0h-2m8 0h-2"/></svg>'),
                    'alpineClickHandler' => 'cropper.scaleX(-cropper.getData().scaleX || -1)',
                ],
                [
                    'label' => __('filament-forms::components.file_upload.cropper.actions.flip_vertical.label'),
                    'iconHtml' => new HtmlString('<svg class="' . $iconSizeClasses . '" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m3 7l5 5l-5 5V7m18 0l-5 5l5 5V7m-9 13v2m0-8v2m0-8v2m0-8v2"/></svg>'),
                    'alpineClickHandler' => 'cropper.scaleY(-cropper.getData().scaleY || -1)',
                ],
            ],
        ];
    }
}
