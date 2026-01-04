<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Forms\View\FormsIconAlias;
use Filament\Support\Concerns\HasAlignment;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use InvalidArgumentException;

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

    protected string | bool | Closure | null $automaticImageCropAspectRatio = null;

    protected string | Closure | null $automaticImageResizeMode = null;

    protected string | Closure | null $automaticImageResizeTargetHeight = null;

    protected string | Closure | null $automaticImageResizeTargetWidth = null;

    protected bool | Closure $automaticImageResizeUpscale = true;

    protected string | Closure | null $imagePreviewHeight = null;

    protected bool | Closure $isAvatar = false;

    protected string | int | float | Closure | null $itemPanelAspectRatio = null;

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

    protected bool | Closure $shouldAutomaticallyOpenImageEditorForAspectRatio = false;

    protected int | Closure | null $imageEditorViewportWidth = null;

    protected int | Closure | null $imageEditorViewportHeight = null;

    protected int $imageEditorMode = 1;

    /**
     * @var string | array<string> | Closure | null
     */
    protected string | array | Closure | null $imageEditorEmptyFillColor = null;

    /**
     * @var array<string | null> | Closure
     */
    protected array | Closure $imageEditorAspectRatioOptions = [];

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
        $this->imageAspectRatio('1:1');
        $this->automaticImageResizeMode('cover');
        $this->automaticImageResizeUpscale(false);
        $this->automaticImageCropAspectRatio();
        $this->automaticImageResizeTargetHeight('500');
        $this->automaticImageResizeTargetWidth('500');
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

    public function automaticImageCropAspectRatio(string | bool | Closure $ratio = true): static
    {
        $this->automaticImageCropAspectRatio = $ratio;

        return $this;
    }

    /**
     * @deprecated Use `automaticImageCropAspectRatio()` instead.
     */
    public function imageCropAspectRatio(string | bool | Closure $ratio = true): static
    {
        return $this->automaticImageCropAspectRatio($ratio);
    }

    public function automaticImageResizeMode(string | Closure | null $mode): static
    {
        $this->automaticImageResizeMode = $mode;

        return $this;
    }

    /**
     * @deprecated Use `automaticImageResizeMode()` instead.
     */
    public function imageResizeMode(string | Closure | null $mode): static
    {
        return $this->automaticImageResizeMode($mode);
    }

    public function automaticImageResizeTargetHeight(string | Closure | null $height): static
    {
        $this->automaticImageResizeTargetHeight = $height;

        return $this;
    }

    /**
     * @deprecated Use `automaticImageResizeTargetHeight()` instead.
     */
    public function imageResizeTargetHeight(string | Closure | null $height): static
    {
        return $this->automaticImageResizeTargetHeight($height);
    }

    public function automaticImageResizeTargetWidth(string | Closure | null $width): static
    {
        $this->automaticImageResizeTargetWidth = $width;

        return $this;
    }

    /**
     * @deprecated Use `automaticImageResizeTargetWidth()` instead.
     */
    public function imageResizeTargetWidth(string | Closure | null $width): static
    {
        return $this->automaticImageResizeTargetWidth($width);
    }

    public function automaticImageResizeUpscale(bool | Closure $condition = true): static
    {
        $this->automaticImageResizeUpscale = $condition;

        return $this;
    }

    /**
     * @deprecated Use `automaticImageResizeUpscale()` instead.
     */
    public function imageResizeUpscale(bool | Closure $condition = true): static
    {
        return $this->automaticImageResizeUpscale($condition);
    }

    public function imagePreviewHeight(string | Closure | null $height): static
    {
        $this->imagePreviewHeight = $height;

        return $this;
    }

    public function itemPanelAspectRatio(string | int | float | Closure | null $ratio): static
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

    public function getAutomaticImageCropAspectRatio(): ?string
    {
        $ratio = $this->evaluate($this->automaticImageCropAspectRatio);

        if ($ratio === true) {
            $imageAspectRatio = $this->getImageAspectRatio();

            if (blank($imageAspectRatio)) {
                return null;
            }

            if (is_array($imageAspectRatio)) {
                $imageAspectRatio = $imageAspectRatio[0] ?? null;
            }

            return $this->normalizeAspectRatio($imageAspectRatio);
        }

        return $this->normalizeAspectRatio($ratio);
    }

    /**
     * @deprecated Use `getAutomaticImageCropAspectRatio()` instead.
     */
    public function getImageCropAspectRatio(): ?string
    {
        return $this->getAutomaticImageCropAspectRatio();
    }

    public function getAutomaticImageResizeMode(): ?string
    {
        return $this->evaluate($this->automaticImageResizeMode);
    }

    /**
     * @deprecated Use `getAutomaticImageResizeMode()` instead.
     */
    public function getImageResizeMode(): ?string
    {
        return $this->getAutomaticImageResizeMode();
    }

    public function getAutomaticImageResizeTargetHeight(): ?string
    {
        return $this->evaluate($this->automaticImageResizeTargetHeight);
    }

    /**
     * @deprecated Use `getAutomaticImageResizeTargetHeight()` instead.
     */
    public function getImageResizeTargetHeight(): ?string
    {
        return $this->getAutomaticImageResizeTargetHeight();
    }

    public function getAutomaticImageResizeTargetWidth(): ?string
    {
        return $this->evaluate($this->automaticImageResizeTargetWidth);
    }

    /**
     * @deprecated Use `getAutomaticImageResizeTargetWidth()` instead.
     */
    public function getImageResizeTargetWidth(): ?string
    {
        return $this->getAutomaticImageResizeTargetWidth();
    }

    public function getAutomaticImageResizeUpscale(): bool
    {
        return (bool) $this->evaluate($this->automaticImageResizeUpscale);
    }

    /**
     * @deprecated Use `getAutomaticImageResizeUpscale()` instead.
     */
    public function getImageResizeUpscale(): bool
    {
        return $this->getAutomaticImageResizeUpscale();
    }

    public function getImagePreviewHeight(): ?string
    {
        return $this->evaluate($this->imagePreviewHeight);
    }

    public function getItemPanelAspectRatio(): int | float | null
    {
        $ratio = $this->evaluate($this->itemPanelAspectRatio);

        if (
            ($this->getPanelLayout() === 'grid') &&
            (! $ratio)
        ) {
            return 1;
        }

        if (is_string($ratio)) {
            return $this->calculateAspectRatio($this->normalizeAspectRatio($ratio));
        }

        return $ratio;
    }

    public function getLoadingIndicatorPosition(): string
    {
        return $this->evaluate($this->loadingIndicatorPosition);
    }

    public function getPanelAspectRatio(): ?string
    {
        return $this->normalizeAspectRatio($this->evaluate($this->panelAspectRatio));
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

    public function automaticallyOpenImageEditorForAspectRatio(bool | Closure $condition = true): static
    {
        $this->shouldAutomaticallyOpenImageEditorForAspectRatio = $condition;

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
            throw new InvalidArgumentException("The file upload editor mode must be either 1, 2 or 3. [{$mode}] given, which is unsupported. See https://github.com/fengyuanchen/cropperjs#viewmode for more information on the available modes. Mode 0 is not supported, as it does not allow configuration via manual inputs.");
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
     * @param  array<string | null> | Closure  $ratios
     */
    public function imageEditorAspectRatioOptions(array | Closure $ratios): static
    {
        $this->imageEditorAspectRatioOptions = $ratios;

        return $this;
    }

    /**
     * @deprecated Use `imageEditorAspectRatioOptions()` instead.
     *
     * @param  array<string | null> | Closure  $ratios
     */
    public function imageEditorAspectRatios(array | Closure $ratios): static
    {
        return $this->imageEditorAspectRatioOptions($ratios);
    }

    public function getImageEditorViewportHeight(): ?int
    {
        if (($targetHeight = (int) $this->getAutomaticImageResizeTargetHeight()) > 1) {
            return (int) round($targetHeight * $this->getParentTargetSizes($targetHeight), precision: 0);
        }

        if (filled($ratio = $this->getAutomaticImageCropAspectRatio())) {
            $parts = explode(':', $ratio);

            if (count($parts) === 2) {
                return (int) $parts[1];
            }
        }

        return $this->evaluate($this->imageEditorViewportHeight);
    }

    public function getImageEditorViewportWidth(): ?int
    {
        if (($targetWidth = (int) $this->getAutomaticImageResizeTargetWidth()) > 1) {
            return (int) round($targetWidth * $this->getParentTargetSizes($targetWidth), precision: 0);
        }

        if (filled($ratio = $this->getAutomaticImageCropAspectRatio())) {
            $parts = explode(':', $ratio);

            if (count($parts) === 2) {
                return (int) $parts[0];
            }
        }

        return $this->evaluate($this->imageEditorViewportWidth);
    }

    protected function getParentTargetSizes(int $widthOrHeight): int | float
    {
        $targetWidth = (int) $this->getAutomaticImageResizeTargetWidth();

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
        if ($this->shouldAutomaticallyOpenImageEditorForAspectRatio()) {
            return true;
        }

        return (bool) $this->evaluate($this->hasImageEditor);
    }

    public function isImageEditorExplicitlyEnabled(): bool
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

    public function shouldAutomaticallyOpenImageEditorForAspectRatio(): bool
    {
        if (! $this->evaluate($this->shouldAutomaticallyOpenImageEditorForAspectRatio)) {
            return false;
        }

        $ratio = $this->getImageAspectRatio();

        if (blank($ratio)) {
            throw new InvalidArgumentException('The [automaticallyOpenImageEditorForAspectRatio()] method requires [imageAspectRatio()] to be set with a single aspect ratio.');
        }

        if (is_array($ratio) && count($ratio) > 1) {
            throw new InvalidArgumentException('The [automaticallyOpenImageEditorForAspectRatio()] method cannot be used when [imageAspectRatio()] has multiple allowed aspect ratios.');
        }

        return true;
    }

    public function getAutomaticallyOpenImageEditorForAspectRatioForJs(): ?float
    {
        if (! $this->shouldAutomaticallyOpenImageEditorForAspectRatio()) {
            return null;
        }

        $ratio = $this->getImageAspectRatio();

        if (is_array($ratio)) {
            $ratio = $ratio[0] ?? null;
        }

        if (blank($ratio)) {
            return null;
        }

        return $this->calculateAspectRatio($ratio);
    }

    /**
     * @return array<string, float | string>
     */
    public function getImageEditorAspectRatioOptionsForJs(): array
    {
        return collect($this->evaluate($this->imageEditorAspectRatioOptions) ?? [])
            ->when(
                filled($automaticCropRatio = $this->getAutomaticImageCropAspectRatio()),
                fn (Collection $ratios): Collection => $ratios->push($automaticCropRatio),
            )
            ->unique()
            ->mapWithKeys(function (?string $ratio): array {
                $label = $ratio === null
                    ? __('filament-forms::components.file_upload.editor.aspect_ratios.no_fixed.label')
                    : str_replace('/', ':', $ratio);

                $floatValue = $ratio === null ? 'NaN' : $this->calculateAspectRatio($ratio);

                return [$label => $floatValue];
            })
            ->filter(fn (float | string | null $ratio): bool => $ratio !== null)
            ->when(
                fn (Collection $ratios): bool => $ratios->count() < 2,
                fn (Collection $ratios) => $ratios->take(0),
            )
            ->all();
    }

    /**
     * @deprecated Use `getImageEditorAspectRatioOptionsForJs()` instead.
     *
     * @return array<string, float | string>
     */
    public function getImageEditorAspectRatiosForJs(): array
    {
        return $this->getImageEditorAspectRatioOptionsForJs();
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
