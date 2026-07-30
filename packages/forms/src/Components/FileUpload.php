<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Forms\View\FormsIconAlias;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns\HasAlignment;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Filament\Support\Enums\Alignment;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Icons\Heroicon;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Filament\Support\View\Components\ButtonComponent;
use Illuminate\Support\Collection;
use Illuminate\Support\Js;
use InvalidArgumentException;

use function Filament\Support\generate_icon_html;

class FileUpload extends BaseFileUpload implements HasEmbeddedView
{
    use Concerns\HasExtraInputAttributes;
    use Concerns\HasPlaceholder;
    use HasAlignment;
    use HasExtraAlpineAttributes;

    protected ?string $publishedViewOverrideCheckPath = 'filament-forms::components.file-upload';

    protected bool | Closure $shouldAutomaticallyCropImagesToAspectRatio = false;

    protected string | Closure | null $automaticallyResizeImagesMode = null;

    protected string | Closure | null $automaticallyResizeImagesHeight = null;

    protected string | Closure | null $automaticallyResizeImagesWidth = null;

    protected bool | Closure $shouldAutomaticallyUpscaleImagesWhenResizing = true;

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
        $this->automaticallyResizeImagesMode('cover');
        $this->automaticallyUpscaleImagesWhenResizing(false);
        $this->automaticallyCropImagesToAspectRatio();
        $this->automaticallyResizeImagesToHeight('500');
        $this->automaticallyResizeImagesToWidth('500');
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

    public function automaticallyCropImagesToAspectRatio(bool | Closure $condition = true): static
    {
        $this->shouldAutomaticallyCropImagesToAspectRatio = $condition;

        return $this;
    }

    /**
     * @deprecated Use `imageAspectRatio()` and `automaticallyCropImagesToAspectRatio()` instead.
     */
    public function imageCropAspectRatio(string | Closure | null $ratio): static
    {
        $this->imageAspectRatio($ratio);
        $this->automaticallyCropImagesToAspectRatio(($ratio instanceof Closure) ? $ratio : filled($ratio));

        return $this;
    }

    public function automaticallyResizeImagesMode(string | Closure | null $mode): static
    {
        $this->automaticallyResizeImagesMode = $mode;

        return $this;
    }

    /**
     * @deprecated Use `automaticallyResizeImagesMode()` instead.
     */
    public function imageResizeMode(string | Closure | null $mode): static
    {
        return $this->automaticallyResizeImagesMode($mode);
    }

    public function automaticallyResizeImagesToHeight(string | Closure | null $height): static
    {
        $this->automaticallyResizeImagesHeight = $height;

        return $this;
    }

    /**
     * @deprecated Use `automaticallyResizeImagesToHeight()` instead.
     */
    public function imageResizeTargetHeight(string | Closure | null $height): static
    {
        return $this->automaticallyResizeImagesToHeight($height);
    }

    public function automaticallyResizeImagesToWidth(string | Closure | null $width): static
    {
        $this->automaticallyResizeImagesWidth = $width;

        return $this;
    }

    /**
     * @deprecated Use `automaticallyResizeImagesToWidth()` instead.
     */
    public function imageResizeTargetWidth(string | Closure | null $width): static
    {
        return $this->automaticallyResizeImagesToWidth($width);
    }

    public function automaticallyUpscaleImagesWhenResizing(bool | Closure $condition = true): static
    {
        $this->shouldAutomaticallyUpscaleImagesWhenResizing = $condition;

        return $this;
    }

    /**
     * @deprecated Use `automaticallyUpscaleImagesWhenResizing()` instead.
     */
    public function imageResizeUpscale(bool | Closure $condition = true): static
    {
        return $this->automaticallyUpscaleImagesWhenResizing($condition);
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

    public function shouldAutomaticallyCropImagesToAspectRatio(): bool
    {
        return (bool) $this->evaluate($this->shouldAutomaticallyCropImagesToAspectRatio);
    }

    public function getAutomaticallyCropImagesAspectRatio(): ?string
    {
        if (! $this->shouldAutomaticallyCropImagesToAspectRatio()) {
            return null;
        }

        $imageAspectRatio = $this->getImageAspectRatio();

        if (blank($imageAspectRatio)) {
            return null;
        }

        if (is_array($imageAspectRatio)) {
            $imageAspectRatio = $imageAspectRatio[0] ?? null;
        }

        return $this->normalizeAspectRatio($imageAspectRatio);
    }

    /**
     * @deprecated Use `getAutomaticallyCropImagesAspectRatio()` instead.
     */
    public function getImageCropAspectRatio(): ?string
    {
        return $this->getAutomaticallyCropImagesAspectRatio();
    }

    public function getAutomaticallyResizeImagesMode(): ?string
    {
        return $this->evaluate($this->automaticallyResizeImagesMode);
    }

    /**
     * @deprecated Use `getAutomaticallyResizeImagesMode()` instead.
     */
    public function getImageResizeMode(): ?string
    {
        return $this->getAutomaticallyResizeImagesMode();
    }

    public function getAutomaticallyResizeImagesHeight(): ?string
    {
        return $this->evaluate($this->automaticallyResizeImagesHeight);
    }

    /**
     * @deprecated Use `getAutomaticallyResizeImagesHeight()` instead.
     */
    public function getImageResizeTargetHeight(): ?string
    {
        return $this->getAutomaticallyResizeImagesHeight();
    }

    public function getAutomaticallyResizeImagesWidth(): ?string
    {
        return $this->evaluate($this->automaticallyResizeImagesWidth);
    }

    /**
     * @deprecated Use `getAutomaticallyResizeImagesWidth()` instead.
     */
    public function getImageResizeTargetWidth(): ?string
    {
        return $this->getAutomaticallyResizeImagesWidth();
    }

    public function shouldAutomaticallyUpscaleImagesWhenResizing(): bool
    {
        return (bool) $this->evaluate($this->shouldAutomaticallyUpscaleImagesWhenResizing);
    }

    /**
     * @deprecated Use `shouldAutomaticallyUpscaleImagesWhenResizing()` instead.
     */
    public function getImageResizeUpscale(): bool
    {
        return $this->shouldAutomaticallyUpscaleImagesWhenResizing();
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
            throw new InvalidArgumentException("The file upload editor mode must be either 1, 2 or 3. [{$mode}] given, which is unsupported. See https://github.com/fengyuanchen/cropperjs/blob/v1/README.md#viewmode for more information on the available modes. Mode 0 is not supported, as it does not allow configuration via manual inputs.");
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
        if (($targetHeight = (int) $this->getAutomaticallyResizeImagesHeight()) > 1) {
            return (int) round($targetHeight * $this->getParentTargetSizes($targetHeight), precision: 0);
        }

        if (filled($ratio = $this->getAutomaticallyCropImagesAspectRatio())) {
            $parts = explode(':', $ratio);

            if (count($parts) === 2) {
                return (int) $parts[1];
            }
        }

        return $this->evaluate($this->imageEditorViewportHeight);
    }

    public function getImageEditorViewportWidth(): ?int
    {
        if (($targetWidth = (int) $this->getAutomaticallyResizeImagesWidth()) > 1) {
            return (int) round($targetWidth * $this->getParentTargetSizes($targetWidth), precision: 0);
        }

        if (filled($ratio = $this->getAutomaticallyCropImagesAspectRatio())) {
            $parts = explode(':', $ratio);

            if (count($parts) === 2) {
                return (int) $parts[0];
            }
        }

        return $this->evaluate($this->imageEditorViewportWidth);
    }

    protected function getParentTargetSizes(int $widthOrHeight): int | float
    {
        $targetWidth = (int) $this->getAutomaticallyResizeImagesWidth();

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

        if ($this->isMultiple()) {
            throw new InvalidArgumentException('The [automaticallyOpenImageEditorForAspectRatio()] method cannot be used when [multiple()] is enabled.');
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

    public function getAutomaticallyOpenImageEditorForAspectRatio(): ?float
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
                filled($automaticCropRatio = $this->getAutomaticallyCropImagesAspectRatio()),
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

    public function toEmbeddedHtml(): string
    {
        $id = $this->getId();
        $automaticallyCropImagesAspectRatio = $this->getAutomaticallyCropImagesAspectRatio();
        $automaticallyResizeImagesHeight = $this->getAutomaticallyResizeImagesHeight();
        $automaticallyResizeImagesWidth = $this->getAutomaticallyResizeImagesWidth();
        $isAvatar = $this->isAvatar();
        $isMultiple = $this->isMultiple();
        $key = $this->getKey();
        $statePath = $this->getStatePath();
        $isDisabled = $this->isDisabled();
        $hasImageEditor = $this->hasImageEditor();
        $isImageEditorExplicitlyEnabled = $this->isImageEditorExplicitlyEnabled();
        $hasCircleCropper = $this->hasCircleCropper();
        $livewireKey = $this->getLivewireKey();
        $maxFiles = $this->getMaxFiles();
        $maxSize = $this->getMaxSize();
        $minSize = $this->getMinSize();

        $alignment = $this->getAlignment() ?? Alignment::Start;

        if (! $alignment instanceof Alignment) {
            $alignment = filled($alignment) ? (Alignment::tryFrom($alignment) ?? $alignment) : null;
        }

        $wireKey = $livewireKey . '.' . substr(md5(serialize([$isDisabled])), 0, 64);

        $outerAttributes = $this->getExtraAttributeBag()
            ->merge([
                'aria-labelledby' => "{$id}-label",
                'id' => $id,
                'role' => 'group',
            ], escape: false)
            ->merge($this->getExtraAlpineAttributes(), escape: false)
            ->class([
                'fi-fo-file-upload',
                'fi-fo-file-upload-avatar' => $isAvatar,
                ($alignment instanceof Alignment) ? "fi-align-{$alignment->value}" : $alignment,
            ]);

        $inputAttributes = $this->getExtraInputAttributeBag()
            ->merge([
                'aria-labelledby' => "{$id}-label",
                'disabled' => $isDisabled,
                'multiple' => $isMultiple,
                'type' => 'file',
            ], escape: false);

        $alpineComponentSrc = FilamentAsset::getAlpineComponentSrc('file-upload', 'filament/forms');

        ob_start(); ?>

        <div
            x-load
            x-load-src="<?= e($alpineComponentSrc) ?>"
            x-data="fileUploadFormComponent({
                        acceptedFileTypes: <?= Js::from($this->getAcceptedFileTypes()) ?>,
                        automaticallyCropImagesAspectRatio: <?= Js::from($automaticallyCropImagesAspectRatio) ?>,
                        automaticallyOpenImageEditorForAspectRatio: <?= Js::from($this->getAutomaticallyOpenImageEditorForAspectRatio()) ?>,
                        automaticallyResizeImagesMode: <?= Js::from($this->getAutomaticallyResizeImagesMode()) ?>,
                        automaticallyResizeImagesHeight: <?= Js::from($automaticallyResizeImagesHeight) ?>,
                        automaticallyResizeImagesWidth: <?= Js::from($automaticallyResizeImagesWidth) ?>,
                        cancelUploadUsing: (fileKey) => {
                            $wire.cancelUpload(`<?= e($statePath) ?>.${fileKey}`)
                        },
                        canEditSvgs: <?= Js::from($this->canEditSvgs()) ?>,
                        confirmSvgEditingMessage: <?= Js::from(__('filament-forms::components.file_upload.editor.svg.messages.confirmation')) ?>,
                        deleteUploadedFileUsing: async (fileKey) => {
                            return await $wire.callSchemaComponentMethod(
                                <?= Js::from($key) ?>,
                                'deleteUploadedFile',
                                { fileKey },
                            )
                        },
                        disabledSvgEditingMessage: <?= Js::from(__('filament-forms::components.file_upload.editor.svg.messages.disabled')) ?>,
                        getUploadedFilesUsing: async () => {
                            return await $wire.callSchemaComponentMethod(
                                <?= Js::from($key) ?>,
                                'getUploadedFiles',
                            )
                        },
                        hasCircleCropper: <?= Js::from($hasCircleCropper) ?>,
                        hasImageEditor: <?= Js::from($hasImageEditor) ?>,
                        imageEditorEmptyFillColor: <?= Js::from($this->getImageEditorEmptyFillColor()) ?>,
                        imageEditorMode: <?= Js::from($this->getImageEditorMode()) ?>,
                        imageEditorViewportHeight: <?= Js::from($this->getImageEditorViewportHeight()) ?>,
                        imageEditorViewportWidth: <?= Js::from($this->getImageEditorViewportWidth()) ?>,
                        imagePreviewHeight: <?= Js::from($this->getImagePreviewHeight()) ?>,
                        isAvatar: <?= Js::from($isAvatar) ?>,
                        isDeletable: <?= Js::from($this->isDeletable()) ?>,
                        isDisabled: <?= Js::from($isDisabled) ?>,
                        isDownloadable: <?= Js::from($this->isDownloadable()) ?>,
                        isImageEditorExplicitlyEnabled: <?= Js::from($isImageEditorExplicitlyEnabled) ?>,
                        isMultiple: <?= Js::from($isMultiple) ?>,
                        isOpenable: <?= Js::from($this->isOpenable()) ?>,
                        isPasteable: <?= Js::from($this->isPasteable()) ?>,
                        isPreviewable: <?= Js::from($this->isPreviewable()) ?>,
                        isReorderable: <?= Js::from($this->isReorderable()) ?>,
                        isSvgEditingConfirmed: <?= Js::from($this->isSvgEditingConfirmed()) ?>,
                        itemPanelAspectRatio: <?= Js::from($this->getItemPanelAspectRatio()) ?>,
                        loadingIndicatorPosition: <?= Js::from($this->getLoadingIndicatorPosition()) ?>,
                        locale: <?= Js::from(app()->getLocale()) ?>,
                        maxFiles: <?= Js::from($maxFiles) ?>,
                        maxFilesValidationMessage: <?= Js::from($maxFiles ? trans_choice('validation.max.array', $maxFiles, ['attribute' => $this->getValidationAttribute(), 'max' => $maxFiles]) : null) ?>,
                        maxParallelUploads: <?= Js::from($this->getMaxParallelUploads()) ?>,
                        maxSize: <?= Js::from($maxSize ? "{$maxSize}KB" : null) ?>,
                        mimeTypeMap: <?= Js::from($this->getMimeTypeMap()) ?>,
                        minSize: <?= Js::from($minSize ? "{$minSize}KB" : null) ?>,
                        panelAspectRatio: <?= Js::from($this->getPanelAspectRatio()) ?>,
                        panelLayout: <?= Js::from($this->getPanelLayout()) ?>,
                        placeholder: <?= Js::from($this->getPlaceholder()) ?>,
                        removeUploadedFileButtonPosition: <?= Js::from($this->getRemoveUploadedFileButtonPosition()) ?>,
                        removeUploadedFileUsing: async (fileKey) => {
                            return await $wire.callSchemaComponentMethod(
                                <?= Js::from($key) ?>,
                                'removeUploadedFile',
                                { fileKey },
                            )
                        },
                        reorderUploadedFilesUsing: async (fileKeys) => {
                            return await $wire.callSchemaComponentMethod(
                                <?= Js::from($key) ?>,
                                'reorderUploadedFiles',
                                { fileKeys },
                            )
                        },
                        shouldAppendFiles: <?= Js::from($this->shouldAppendFiles()) ?>,
                        shouldAutomaticallyUpscaleImagesWhenResizing: <?= Js::from($this->shouldAutomaticallyUpscaleImagesWhenResizing()) ?>,
                        shouldOrientImageFromExif: <?= Js::from($this->shouldOrientImagesFromExif()) ?>,
                        shouldTransformImage: <?= Js::from($automaticallyCropImagesAspectRatio || $automaticallyResizeImagesHeight || $automaticallyResizeImagesWidth) ?>,
                        state: $wire.<?= $this->applyStateBindingModifiers("\$entangle('{$statePath}')") ?>,
                        uploadButtonPosition: <?= Js::from($this->getUploadButtonPosition()) ?>,
                        uploadingMessage: <?= Js::from($this->getUploadingMessage()) ?>,
                        downloadActionLabel: <?= Js::from(__('filament-forms::components.file_upload.actions.download.label')) ?>,
                        openActionLabel: <?= Js::from(__('filament-forms::components.file_upload.actions.open.label')) ?>,
                        uploadProgressIndicatorPosition: <?= Js::from($this->getUploadProgressIndicatorPosition()) ?>,
                        uploadUsing: (fileKey, file, success, error, progress) => {
                            $wire.upload(
                                `<?= e($statePath) ?>.${fileKey}`,
                                file,
                                () => {
                                    success(fileKey)
                                },
                                error,
                                (progressEvent) => {
                                    progress(true, progressEvent.detail.progress, 100)
                                },
                            )
                        },
                    })"
            wire:ignore
            wire:key="<?= e($wireKey) ?>"
            <?= $outerAttributes->toHtml() ?>
        >
            <div class="fi-fo-file-upload-input-ctn">
                <input
                    x-ref="input"
                    <?= $inputAttributes->toHtml() ?>
                />
            </div>

            <div
                x-show="error"
                x-text="error"
                x-cloak
                role="alert"
                class="fi-fo-file-upload-error-message"
            ></div>

            <?php if ($hasImageEditor && ! $isDisabled) { ?>
                <div
                    aria-label="<?= e(__('filament-forms::components.file_upload.editor.label')) ?>"
                    aria-modal="true"
                    role="dialog"
                    x-show="isEditorOpen"
                    x-cloak
                    x-on:click.stop=""
                    x-trap.noscroll="isEditorOpen"
                    x-on:keydown.escape.prevent.stop="closeEditor"
                    <?= (new FilamentComponentAttributeBag)->class([
                        'fi-fo-file-upload-editor',
                        'fi-fo-file-upload-editor-circle-cropper' => $hasCircleCropper,
                        'fi-fo-file-upload-editor-crop-only' => ! $isImageEditorExplicitlyEnabled,
                    ])->toHtml() ?>
                >
                    <div
                        aria-hidden="true"
                        class="fi-fo-file-upload-editor-overlay"
                    ></div>

                    <div class="fi-fo-file-upload-editor-window">
                        <div class="fi-fo-file-upload-editor-image-ctn">
                            <?php // Decorative: Cropper.js drives this image and the editor dialog is labelled elsewhere.?>
                            <img
                                alt=""
                                x-ref="editor"
                                class="fi-fo-file-upload-editor-image"
                            />
                        </div>

                        <div class="fi-fo-file-upload-editor-control-panel">
                            <?php if ($isImageEditorExplicitlyEnabled) { ?>
                                <div class="fi-fo-file-upload-editor-control-panel-main">
                                    <div class="fi-fo-file-upload-editor-control-panel-group">
                                        <?php foreach ([
                                            [
                                                'label' => __('filament-forms::components.file_upload.editor.fields.x_position.label'),
                                                'ref' => 'xPositionInput',
                                                'unit' => __('filament-forms::components.file_upload.editor.fields.x_position.unit'),
                                                'alpineSaveHandler' => 'editor.setData({...editor.getData(true), x: +$el.value})',
                                            ],
                                            [
                                                'label' => __('filament-forms::components.file_upload.editor.fields.y_position.label'),
                                                'ref' => 'yPositionInput',
                                                'unit' => __('filament-forms::components.file_upload.editor.fields.y_position.unit'),
                                                'alpineSaveHandler' => 'editor.setData({...editor.getData(true), y: +$el.value})',
                                            ],
                                            [
                                                'label' => __('filament-forms::components.file_upload.editor.fields.width.label'),
                                                'ref' => 'widthInput',
                                                'unit' => __('filament-forms::components.file_upload.editor.fields.width.unit'),
                                                'alpineSaveHandler' => 'editor.setData({...editor.getData(true), width: +$el.value})',
                                            ],
                                            [
                                                'label' => __('filament-forms::components.file_upload.editor.fields.height.label'),
                                                'ref' => 'heightInput',
                                                'unit' => __('filament-forms::components.file_upload.editor.fields.height.unit'),
                                                'alpineSaveHandler' => 'editor.setData({...editor.getData(true), height: +$el.value})',
                                            ],
                                            [
                                                'label' => __('filament-forms::components.file_upload.editor.fields.rotation.label'),
                                                'ref' => 'rotationInput',
                                                'unit' => __('filament-forms::components.file_upload.editor.fields.rotation.unit'),
                                                'alpineSaveHandler' => 'editor.rotateTo(+$el.value)',
                                            ],
                                        ] as $input) { ?>
                                            <label>
                                                <div class="fi-input-wrp">
                                                    <div class="fi-input-wrp-prefix fi-input-wrp-prefix-has-content fi-input-wrp-prefix-has-label">
                                                        <span class="fi-input-wrp-label">
                                                            <?= e($input['label']) ?>
                                                        </span>
                                                    </div>

                                                    <div class="fi-input-wrp-content-ctn">
                                                        <input
                                                            x-on:keyup.enter.prevent.stop="editor && <?= $input['alpineSaveHandler'] ?>"
                                                            x-on:blur="editor && <?= $input['alpineSaveHandler'] ?>"
                                                            x-ref="<?= e($input['ref']) ?>"
                                                            x-on:keydown.enter.prevent
                                                            type="text"
                                                            class="fi-input"
                                                        />
                                                    </div>

                                                    <div class="fi-input-wrp-suffix fi-input-wrp-suffix-has-label">
                                                        <span class="fi-input-wrp-label">
                                                            <?= e($input['unit']) ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </label>
                                        <?php } ?>
                                    </div>

                                    <div class="fi-fo-file-upload-editor-control-panel-group">
                                        <?php foreach ($this->getImageEditorActions() as $groupedActions) { ?>
                                            <div class="fi-btn-group">
                                                <?php foreach ($groupedActions as $action) { ?>
                                                    <button
                                                        aria-label="<?= e($action['label']) ?>"
                                                        type="button"
                                                        x-on:click.prevent.stop="<?= e($action['alpineClickHandler']) ?>"
                                                        x-tooltip="{ content: <?= Js::from($action['label']) ?>, theme: $store.theme }"
                                                        class="fi-btn"
                                                    >
                                                        <?= $action['iconHtml']?->toHtml() ?>
                                                    </button>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                    </div>

                                    <?php
                                    $aspectRatios = $this->getImageEditorAspectRatioOptionsForJs();

                                if (count($aspectRatios)) { ?>
                                        <div class="fi-fo-file-upload-editor-control-panel-group">
                                            <div class="fi-fo-file-upload-editor-control-panel-group-title">
                                                <?= e(__('filament-forms::components.file_upload.editor.aspect_ratios.label')) ?>
                                            </div>

                                            <?php foreach (collect($aspectRatios)->chunk(5) as $ratiosChunk) { ?>
                                                <div class="fi-btn-group">
                                                    <?php foreach ($ratiosChunk as $label => $ratio) { ?>
                                                        <button
                                                            type="button"
                                                            x-on:click.prevent.stop="
                                                                currentRatio = <?= Js::from($label) ?>;
                                                                editor.setAspectRatio(<?= Js::from($ratio) ?>)
                                                            "
                                                            x-tooltip="{ content: <?= Js::from(__('filament-forms::components.file_upload.editor.actions.set_aspect_ratio.label', ['ratio' => $label])) ?>, theme: $store.theme }"
                                                            x-bind:class="{ 'fi-active': currentRatio === <?= Js::from($label) ?> }"
                                                            class="fi-btn"
                                                        >
                                                            <?= e($label) ?>
                                                        </button>
                                                    <?php } ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>

                            <div class="fi-fo-file-upload-editor-control-panel-footer">
                                <?php if ($isImageEditorExplicitlyEnabled) { ?>
                                    <button
                                        type="button"
                                        x-on:click.prevent="pond.imageEditEditor.oncancel"
                                        class="fi-btn"
                                    >
                                        <?= e(__('filament-forms::components.file_upload.editor.actions.cancel.label')) ?>
                                    </button>

                                    <button
                                        type="button"
                                        x-on:click.prevent.stop="editor.reset()"
                                        <?= (new FilamentComponentAttributeBag)
                                            ->color(ButtonComponent::class, 'danger')
                                            ->class(['fi-btn fi-fo-file-upload-editor-control-panel-reset-action'])
                                            ->toHtml() ?>
                                    >
                                        <?= e(__('filament-forms::components.file_upload.editor.actions.reset.label')) ?>
                                    </button>

                                    <button
                                        type="button"
                                        x-on:click.prevent="saveEditor"
                                        <?= (new FilamentComponentAttributeBag)
                                            ->color(ButtonComponent::class, 'success')
                                            ->class(['fi-btn'])
                                            ->toHtml() ?>
                                    >
                                        <?= e(__('filament-forms::components.file_upload.editor.actions.save.label')) ?>
                                    </button>
                                <?php } else { ?>
                                    <button
                                        type="button"
                                        x-on:click.prevent="saveEditor"
                                        <?= (new FilamentComponentAttributeBag)
                                            ->color(ButtonComponent::class, 'success')
                                            ->class(['fi-btn'])
                                            ->toHtml() ?>
                                    >
                                        <?= e(__('filament-forms::components.file_upload.editor.actions.save.label')) ?>
                                    </button>

                                    <button
                                        type="button"
                                        x-on:click.prevent="pond.imageEditEditor.oncancel"
                                        class="fi-btn"
                                    >
                                        <?= e(__('filament-forms::components.file_upload.editor.actions.cancel.label')) ?>
                                    </button>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>

        <?php return $this->wrapEmbeddedHtml(ob_get_clean(), labelTag: 'div');
    }
}
