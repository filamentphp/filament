<?php

namespace Filament\Forms\Components\Concerns;

use Illuminate\Support\HtmlString;

trait Croppable
{
    protected bool $croppable = false;

    protected string $shape = 'square';

    protected int $cropperViewPortWidth = 360;

    protected int $cropperViewPortHeight = 360;

    protected int $viewMode = 0;

    protected string $fillColor = 'transparent';

    protected array | \Closure $aspectRatios = [];

    public function croppable(): self
    {
        $this->croppable = true;

        return $this;
    }

    public function cropperViewPortWidth(int $cropperViewPortWidth): self
    {
        $this->cropperViewPortWidth = $cropperViewPortWidth;

        return $this;
    }

    public function cropperViewPortHeight(int $cropperViewPortHeight): self
    {
        $this->cropperViewPortHeight = $cropperViewPortHeight;

        return $this;
    }

    /** will crop the final image in a circular shape */
    public function circle(): self
    {
        $this->shape = 'circle';

        return $this;
    }

    public function square(): self
    {
        $this->shape = 'square';

        return $this;
    }

    /** src: https://github.com/fengyuanchen/cropperjs#viewmode */
    public function viewMode(int $mode): self
    {
        if (in_array($mode, [0, 1, 2, 3])) {
            $this->viewMode = $mode;
        }

        return $this;
    }

    /** src: https://github.com/fengyuanchen/cropperjs#getcroppedcanvasoptions */
    public function fillColor(string $color): self
    {
        $this->fillColor = $color;

        return $this;
    }

    public function cropperAspectRatios(array $ratios): self
    {
        $this->aspectRatios = $ratios;

        return $this;
    }

    public function getCropperViewPortHeight(): int
    {
        if ( ($targetH = (int) $this->getImageResizeTargetHeight()) > 1) {
            return (int) round($targetH * $this->getParentTargetSizes($targetH), 0);
        }

        if (is_string($ratio = $this->getImageCropAspectRatio())) {
            [$numerator, $denominator] = explode(':', $ratio);

            return $denominator;
        }

        return $this->cropperViewPortHeight;
    }

    public function getCropperViewPortWidth(): int
    {
        if (($targetW = (int) $this->getImageResizeTargetWidth()) > 1) {
            return (int) round($targetW * $this->getParentTargetSizes($targetW), 0);
        }

        if (is_string($ratio = $this->getImageCropAspectRatio())) {
            [$numerator, $denominator] = explode(':', $ratio);

            return $numerator;
        }

        return $this->cropperViewPortWidth;
    }

    protected function getParentTargetSizes(int $withOrHeight): float
    {
        return $withOrHeight > 1 ? 360 / (int) $this->getImageResizeTargetWidth() : 1;
    }

    public function getShape(): string
    {
        return $this->isAvatar ? 'circle' : $this->shape;
    }

    public function getViewMode(): int
    {
        return $this->viewMode;
    }

    public function getFillColor(): string
    {
        return $this->fillColor;
    }

    public function isCroppable(): bool
    {
        return $this->croppable;
    }

    public function getAspectRatios(): array
    {
        $ratios = $this->evaluate($this->aspectRatios);

        $ratios[] = $this->getImageCropAspectRatio();

        if (is_array($ratios)) {

            $compiled = [];

            foreach (array_unique($ratios) as $ratio) {

                if ($validRatio = $this->makeRatio($ratio)) {
                    $compiled[$ratio ?? __('filament-forms::components.cropper.free')] = $validRatio;
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

    public function getEditBtns(): array
    {
        $size = 'h-5 w-5 mx-auto';

        return [
            'zoom' => [
                [
                    'tooltip' => __('filament-forms::components.cropper.drag-move'),
                    'icon' => new HtmlString('<svg class="' . $size . '" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M13 6v5h5V7.75L22.25 12L18 16.25V13h-5v5h3.25L12 22.25L7.75 18H11v-5H6v3.25L1.75 12L6 7.75V11h5V6H7.75L12 1.75L16.25 6H13Z"/></svg>'),
                    'click' => "cropper.setDragMode('move')",
                ],
                [
                    'tooltip' => __('filament-forms::components.cropper.drag-crop'),
                    'icon' => new HtmlString('<svg class="' . $size . '" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M17 23v-4H7q-.825 0-1.412-.587Q5 17.825 5 17V7H1V5h4V1h2v16h16v2h-4v4Zm0-8V7H9V5h8q.825 0 1.413.588Q19 6.175 19 7v8Z"/></svg>'),
                    'click' => "cropper.setDragMode('crop')",
                ],
                [
                    'tooltip' => __('filament-forms::components.cropper.zoom-in'),
                    'icon' => svg('heroicon-o-magnifying-glass-plus', $size)->toHtml(),
                    'click' => 'cropper.zoom(0.1)',
                ],
                [
                    'tooltip' => __('filament-forms::components.cropper.zoom-out'),
                    'icon' => svg('heroicon-o-magnifying-glass-minus', $size)->toHtml(),
                    'click' => 'cropper.zoom(-0.1)',
                ],
                [
                    'tooltip' => __('filament-forms::components.cropper.zoom-100'),
                    'icon' => svg('heroicon-o-arrows-pointing-out', $size)->toHtml(),
                    'click' => 'cropper.zoomTo(1)',
                ],
            ],
            'move' => [
                [
                    'tooltip' => __('filament-forms::components.cropper.move-left'),
                    'icon' => svg('heroicon-o-arrow-left-circle', $size)->toHtml(),
                    'click' => 'cropper.move(-10, 0)',
                ],
                [
                    'tooltip' => __('filament-forms::components.cropper.move-right'),
                    'icon' => svg('heroicon-o-arrow-right-circle', $size)->toHtml(),
                    'click' => 'cropper.move(10, 0)',
                ],
                [
                    'tooltip' => __('filament-forms::components.cropper.move-up'),
                    'icon' => svg('heroicon-o-arrow-up-circle', $size)->toHtml(),
                    'click' => 'cropper.move(0, -10)',
                ],
                [
                    'tooltip' => __('filament-forms::components.cropper.move-down'),
                    'icon' => svg('heroicon-o-arrow-down-circle', $size)->toHtml(),
                    'click' => 'cropper.move(0, 10)',
                ],
                [
                    'tooltip' => __('filament-forms::components.cropper.move-top-left'),
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
                    'tooltip' => __('filament-forms::components.cropper.rotate-left'),
                    'icon' => svg('heroicon-o-arrow-uturn-left', $size)->toHtml(),
                    'click' => 'cropper.rotate(-45)',
                ],
                [
                    'tooltip' => __('filament-forms::components.cropper.rotate-right'),
                    'icon' => svg('heroicon-o-arrow-uturn-right', $size)->toHtml(),
                    'click' => 'cropper.rotate(45)',
                ],
                [
                    'tooltip' => __('filament-forms::components.cropper.rotate-180'),
                    'icon' => svg('heroicon-o-arrow-uturn-down', $size)->toHtml(),
                    'click' => 'cropper.rotate(180)',
                ],
                [
                    'tooltip' => __('filament-forms::components.cropper.flip-horizontal'),
                    'icon' => new HtmlString('<svg class="' . $size . '" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m17 3l-5 5l-5-5h10m0 18l-5-5l-5 5h10M4 12H2m8 0H8m8 0h-2m8 0h-2"/></svg>'),
                    'click' => 'cropper.scaleX(-cropper.getData().scaleX || -1)',
                ],
                [
                    'tooltip' => __('filament-forms::components.cropper.flip-vertical'),
                    'icon' => new HtmlString('<svg class="' . $size . '" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m3 7l5 5l-5 5V7m18 0l-5 5l5 5V7m-9 13v2m0-8v2m0-8v2m0-8v2"/></svg>'),
                    'click' => 'cropper.scaleY(-cropper.getData().scaleY || -1)',
                ],
            ],
        ];
    }

    public function getCropperInputs(): array
    {
        return [
            [
                'label' => 'X',
                'name' => 'inputX',
                'placeholder' => 'x',
                'unit' => 'px',
                'onEnter' => 'cropper.moveTo(+$el.value, $refs.inputY.value)',
            ],
            [
                'label' => 'Y',
                'name' => 'inputY',
                'placeholder' => 'y',
                'unit' => 'px',
                'onEnter' => 'cropper.moveTo($refs.inputX.value, +$el.value)',
            ],
            [
                'label' => __('filament-forms::components.cropper.width'),
                'name' => 'inputWidth',
                'placeholder' => 'width',
                'unit' => 'px',
                'onEnter' => 'cropper.setCropBoxData({...cropper.getCropBoxData(), width:+$el.value})',
            ],
            [
                'label' => __('filament-forms::components.cropper.height'),
                'name' => 'inputHeight',
                'placeholder' => 'height',
                'unit' => 'px',
                'onEnter' => 'cropper.setCropBoxData({...cropper.getCropBoxData(), height:+$el.value})',
            ],
            [
                'label' => __('filament-forms::components.cropper.rotate'),
                'name' => 'inputRotate',
                'placeholder' => 'rotate',
                'unit' => 'deg',
                'onEnter' => 'cropper.rotateTo(+$el.value)',
            ],
            /*[
                'label' => 'FlipX',
                'name' => 'inputScaleX',
                'placeholder' => 'scaleX',
                'unit' => 'int',
                'onEnter' => 'cropper.scaleX(+$el.value)'
            ],
            [
                'label' => 'FlipY',
                'name' => 'inputScaleY',
                'placeholder' => 'scaleY',
                'unit' => 'int',
                'onEnter' => 'cropper.scaleY(+$el.value)'
            ],*/
        ];
    }
}
