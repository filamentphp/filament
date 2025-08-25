<?php

namespace Filament\Forms\Components;

use Closure;
use Exception;
use Filament\Forms\View\FormsIconAlias;
use Filament\Support\Concerns\HasAlignment;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;

use function Filament\Support\generate_icon_html;

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

    protected int | float | Closure | null $itemPanelAspectRatio = null;

    protected string | Closure $loadingIndicatorPosition = 'right';

    protected string | Closure | null $panelAspectRatio = null;

    protected string | Closure | null $panelLayout = 'compact';

    protected string | Closure $removeUploadedFileButtonPosition = 'left';

    protected bool | Closure $shouldAppendFiles = false;

    protected bool | Closure $shouldOrientImagesFromExif = true;

    protected string | Closure $uploadButtonPosition = 'right';

    protected string | Closure $uploadProgressIndicatorPosition = 'right';

    protected bool | Closure $hasImageEditor = false;

    protected bool | Closure $hasCircleCropper = false;

    protected bool | Closure $canEditSvgs = true;

    protected bool | Closure $isSvgEditingConfirmed = false;

    protected int | Closure | null $imageEditorViewportWidth = null;

    protected int | Closure | null $imageEditorViewportHeight = null;

    protected int $imageEditorMode = 1;

    /**
     * @var string | array<string> | Closure | null
     */
    protected string | array | Closure | null $imageEditorEmptyFillColor = null;

    /**
     * @var array<?string> | Closure
     */
    protected array | Closure $imageEditorAspectRatios = [];

    /**
     * @var array<string, string> | Closure
     */
    protected array | Closure $mimeTypeMap = [];

    public function appendFiles(bool | Closure $condition = true): static
    {
        $this->shouldAppendFiles = $condition;

        return $this;
    }

    public function avatar(): static
    {
        $this->isAvatar = true;

        $this->image();
        $this->imageResizeMode('cover');
        $this->imageResizeUpscale(false);
        $this->imageCropAspectRatio('1:1');
        $this->imageResizeTargetHeight('500');
        $this->imageResizeTargetWidth('500');
        $this->loadingIndicatorPosition('center bottom');
        $this->panelLayout('compact circle');
        $this->removeUploadedFileButtonPosition(fn (FileUpload $component) => $component->hasImageEditor() ? 'left bottom' : 'center bottom');
        $this->uploadButtonPosition(fn (FileUpload $component) => $component->hasImageEditor() ? 'right bottom' : 'center bottom');
        $this->uploadProgressIndicatorPosition(fn (FileUpload $component) => $component->hasImageEditor() ? 'right bottom' : 'center bottom');

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

    public function itemPanelAspectRatio(int | float | Closure | null $ratio): static
    {
        $this->itemPanelAspectRatio = $ratio;

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

    public function getItemPanelAspectRatio(): int | float | null
    {
        $itemPanelAspectRatio = $this->evaluate($this->itemPanelAspectRatio);

        if (
            ($this->getPanelLayout() === 'grid') &&
            (! $itemPanelAspectRatio)
        ) {
            return 1;
        }

        return $itemPanelAspectRatio;
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

    public function imageEditor(bool | Closure $condition = true): static
    {
        $this->hasImageEditor = $condition;

        return $this;
    }

    public function circleCropper(bool | Closure $condition = true): static
    {
        $this->hasCircleCropper = $condition;

        return $this;
    }

    public function editableSvgs(bool | Closure $condition = true): static
    {
        $this->canEditSvgs = $condition;

        return $this;
    }

    public function confirmSvgEditing(bool | Closure $condition = true): static
    {
        $this->isSvgEditingConfirmed = $condition;

        return $this;
    }

    public function imageEditorViewportWidth(int | Closure | null $width): static
    {
        $this->imageEditorViewportWidth = $width;

        return $this;
    }

    public function imageEditorViewportHeight(int | Closure | null $height): static
    {
        $this->imageEditorViewportHeight = $height;

        return $this;
    }

    public function imageEditorMode(int $mode): static
    {
        if (! in_array($mode, [1, 2, 3])) {
            throw new Exception("The file upload editor mode must be either 1, 2 or 3. [{$mode}] given, which is unsupported. See https://github.com/fengyuanchen/cropperjs#viewmode for more information on the available modes. Mode 0 is not supported, as it does not allow configuration via manual inputs.");
        }

        $this->imageEditorMode = $mode;

        return $this;
    }

    public function imageEditorEmptyFillColor(string | Closure | null $color): static
    {
        $this->imageEditorEmptyFillColor = $color;

        return $this;
    }

    /**
     * @param  array<?string> | Closure  $ratios
     */
    public function imageEditorAspectRatios(array | Closure $ratios): static
    {
        $this->imageEditorAspectRatios = $ratios;

        return $this;
    }

    public function getImageEditorViewportHeight(): ?int
    {
        if (($targetHeight = (int) $this->getImageResizeTargetHeight()) > 1) {
            return (int) round($targetHeight * $this->getParentTargetSizes($targetHeight), precision: 0);
        }

        if (filled($ratio = $this->getImageCropAspectRatio())) {
            [$numerator, $denominator] = explode(':', $ratio);

            return (int) $denominator;
        }

        return $this->evaluate($this->imageEditorViewportHeight);
    }

    public function getImageEditorViewportWidth(): ?int
    {
        if (($targetWidth = (int) $this->getImageResizeTargetWidth()) > 1) {
            return (int) round($targetWidth * $this->getParentTargetSizes($targetWidth), precision: 0);
        }

        if (filled($ratio = $this->getImageCropAspectRatio())) {
            [$numerator, $denominator] = explode(':', $ratio);

            return (int) $numerator;
        }

        return $this->evaluate($this->imageEditorViewportWidth);
    }

    protected function getParentTargetSizes(int $widthOrHeight): int | float
    {
        $targetWidth = (int) $this->getImageResizeTargetWidth();

        if ($targetWidth === 0) {
            return 1;
        }

        return $widthOrHeight > 1 ? 360 / $targetWidth : 1;
    }

    public function getImageEditorMode(): int
    {
        return $this->imageEditorMode;
    }

    public function getImageEditorEmptyFillColor(): ?string
    {
        return $this->evaluate($this->imageEditorEmptyFillColor);
    }

    public function hasImageEditor(): bool
    {
        return (bool) $this->evaluate($this->hasImageEditor);
    }

    public function hasCircleCropper(): bool
    {
        return (bool) $this->evaluate($this->hasCircleCropper);
    }

    public function canEditSvgs(): bool
    {
        return (bool) $this->evaluate($this->canEditSvgs);
    }

    public function isSvgEditingConfirmed(): bool
    {
        return (bool) $this->evaluate($this->isSvgEditingConfirmed);
    }

    /**
     * @return array<string, float | string>
     */
    public function getImageEditorAspectRatiosForJs(): array
    {
        return collect($this->evaluate($this->imageEditorAspectRatios) ?? [])
            ->when(
                filled($imageCropAspectRatio = $this->getImageCropAspectRatio()),
                fn (Collection $ratios): Collection => $ratios->push($imageCropAspectRatio),
            )
            ->unique()
            ->mapWithKeys(fn (?string $ratio): array => [
                $ratio ?? __('filament-forms::components.file_upload.editor.aspect_ratios.no_fixed.label') => $this->normalizeImageCroppingRatioForJs($ratio),
            ])
            ->filter(fn (float | string | false $ratio): bool => $ratio !== false)
            ->when(
                fn (Collection $ratios): bool => $ratios->count() < 2,
                fn (Collection $ratios) => $ratios->take(0),
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
    public function getImageEditorActions(): array
    {
        return [
            'zoom' => [
                [
                    'label' => __('filament-forms::components.file_upload.editor.actions.drag_move.label'),
                    'iconHtml' => generate_icon_html(
                        'fi-o-arrows-move',
                        alias: FormsIconAlias::COMPONENTS_FILE_UPLOAD_EDITOR_ACTIONS_DRAG_MOVE,
                    ),
                    'alpineClickHandler' => "editor.setDragMode('move')",
                ],
                [
                    'label' => __('filament-forms::components.file_upload.editor.actions.drag_crop.label'),
                    'iconHtml' => generate_icon_html(
                        'fi-o-crop',
                        alias: FormsIconAlias::COMPONENTS_FILE_UPLOAD_EDITOR_ACTIONS_DRAG_CROP,
                    ),
                    'alpineClickHandler' => "editor.setDragMode('crop')",
                ],
                [
                    'label' => __('filament-forms::components.file_upload.editor.actions.zoom_in.label'),
                    'iconHtml' => generate_icon_html(
                        Heroicon::MagnifyingGlassPlus,
                        alias: FormsIconAlias::COMPONENTS_FILE_UPLOAD_EDITOR_ACTIONS_ZOOM_IN,
                    ),
                    'alpineClickHandler' => 'editor.zoom(0.1)',
                ],
                [
                    'label' => __('filament-forms::components.file_upload.editor.actions.zoom_out.label'),
                    'iconHtml' => generate_icon_html(
                        Heroicon::MagnifyingGlassMinus,
                        alias: FormsIconAlias::COMPONENTS_FILE_UPLOAD_EDITOR_ACTIONS_ZOOM_OUT,
                    ),
                    'alpineClickHandler' => 'editor.zoom(-0.1)',
                ],
                [
                    'label' => __('filament-forms::components.file_upload.editor.actions.zoom_100.label'),
                    'iconHtml' => generate_icon_html(
                        Heroicon::ArrowsPointingOut,
                        alias: FormsIconAlias::COMPONENTS_FILE_UPLOAD_EDITOR_ACTIONS_ZOOM_100,
                    ),
                    'alpineClickHandler' => 'editor.zoomTo(1)',
                ],
            ],
            'move' => [
                [
                    'label' => __('filament-forms::components.file_upload.editor.actions.move_left.label'),
                    'iconHtml' => generate_icon_html(
                        Heroicon::ArrowLeftCircle,
                        alias: FormsIconAlias::COMPONENTS_FILE_UPLOAD_EDITOR_ACTIONS_MOVE_LEFT,
                    ),
                    'alpineClickHandler' => 'editor.move(-10, 0)',
                ],
                [
                    'label' => __('filament-forms::components.file_upload.editor.actions.move_right.label'),
                    'iconHtml' => generate_icon_html(
                        Heroicon::ArrowRightCircle,
                        alias: FormsIconAlias::COMPONENTS_FILE_UPLOAD_EDITOR_ACTIONS_MOVE_RIGHT,
                    ),
                    'alpineClickHandler' => 'editor.move(10, 0)',
                ],
                [
                    'label' => __('filament-forms::components.file_upload.editor.actions.move_up.label'),
                    'iconHtml' => generate_icon_html(
                        Heroicon::ArrowUpCircle,
                        alias: FormsIconAlias::COMPONENTS_FILE_UPLOAD_EDITOR_ACTIONS_MOVE_UP,
                    ),
                    'alpineClickHandler' => 'editor.move(0, -10)',
                ],
                [
                    'label' => __('filament-forms::components.file_upload.editor.actions.move_down.label'),
                    'iconHtml' => generate_icon_html(
                        Heroicon::ArrowDownCircle,
                        alias: FormsIconAlias::COMPONENTS_FILE_UPLOAD_EDITOR_ACTIONS_MOVE_DOWN,
                    ),
                    'alpineClickHandler' => 'editor.move(0, 10)',
                ],
            ],
            'transform' => [
                [
                    'label' => __('filament-forms::components.file_upload.editor.actions.rotate_left.label'),
                    'iconHtml' => generate_icon_html(
                        Heroicon::ArrowUturnLeft,
                        alias: FormsIconAlias::COMPONENTS_FILE_UPLOAD_EDITOR_ACTIONS_ROTATE_LEFT,
                    ),
                    'alpineClickHandler' => 'editor.rotate(-90)',
                ],
                [
                    'label' => __('filament-forms::components.file_upload.editor.actions.rotate_right.label'),
                    'iconHtml' => generate_icon_html(
                        Heroicon::ArrowUturnRight,
                        alias: FormsIconAlias::COMPONENTS_FILE_UPLOAD_EDITOR_ACTIONS_ROTATE_RIGHT,
                    ),
                    'alpineClickHandler' => 'editor.rotate(90)',
                ],
                [
                    'label' => __('filament-forms::components.file_upload.editor.actions.flip_horizontal.label'),
                    'iconHtml' => generate_icon_html(
                        'fi-o-flip-horizontal',
                        alias: FormsIconAlias::COMPONENTS_FILE_UPLOAD_EDITOR_ACTIONS_FLIP_HORIZONTAL,
                    ),
                    'alpineClickHandler' => 'editor.scaleX(-editor.getData().scaleX || -1)',
                ],
                [
                    'label' => __('filament-forms::components.file_upload.editor.actions.flip_vertical.label'),
                    'iconHtml' => generate_icon_html(
                        'fi-o-flip-vertical',
                        alias: FormsIconAlias::COMPONENTS_FILE_UPLOAD_EDITOR_ACTIONS_FLIP_VERTICAL,
                    ),
                    'alpineClickHandler' => 'editor.scaleY(-editor.getData().scaleY || -1)',
                ],
            ],
        ];
    }

    /**
     * @param  array<string, string> | Closure  $map
     */
    public function mimeTypeMap(array | Closure $map): static
    {
        $this->mimeTypeMap = $map;

        return $this;
    }

    /**
     * @return array<string, string>
     */
    public function getMimeTypeMap(): array
    {
        return $this->evaluate($this->mimeTypeMap);
    }
}
