<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns\CanConfigureCommonMark;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Js;
use Illuminate\View\ComponentAttributeBag;
use LogicException;

class MarkdownEditor extends Field implements Contracts\CanBeLengthConstrained, HasEmbeddedView
{
    use CanConfigureCommonMark;
    use Concerns\CanBeLengthConstrained;
    use Concerns\HasFileAttachments;
    use Concerns\HasMaxHeight;
    use Concerns\HasMinHeight;
    use Concerns\HasPlaceholder;
    use Concerns\InteractsWithToolbarButtons;
    use HasExtraAlpineAttributes;

    /**
     * @return array<string | array<string>>
     */
    public function getDefaultToolbarButtons(): array
    {
        return [
            ['bold', 'italic', 'strike', 'link'],
            ['heading'],
            ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
            [
                'table',
                ...($this->hasFileAttachments(default: true) ? ['attachFiles'] : []),
            ],
            ['undo', 'redo'],
        ];
    }

    public function getFileAttachmentsDiskName(): string
    {
        $name = $this->evaluate($this->fileAttachmentsDiskName);

        if (filled($name)) {
            return $name;
        }

        $defaultName = config('filament.default_filesystem_disk');

        return ($defaultName === 'local') ? 'public' : $defaultName;
    }

    public function fileAttachmentsVisibility(string | Closure | null $visibility): static
    {
        throw new LogicException('The visibility of file attachments for markdown content is always `public`, since generating temporary file upload URLs is not supported in static content.');
    }

    public function getFileAttachmentsVisibility(): string
    {
        return 'public';
    }

    public function hasFileAttachmentsByDefault(): bool
    {
        return $this->hasToolbarButton('attachFiles');
    }

    public function toEmbeddedHtml(): string
    {
        $id = $this->getId();
        $isDisabled = $this->isDisabled();
        $statePath = $this->getStatePath();

        if ($isDisabled) {
            ob_start(); ?>

            <div id="<?= e($id) ?>" class="fi-fo-markdown-editor fi-disabled fi-prose">
                <?= str($this->getState())->markdown($this->getCommonMarkOptions(), $this->getCommonMarkExtensions())->sanitizeHtml() ?>
            </div>

            <?php return $this->wrapEmbeddedHtml(ob_get_clean());
        }

        $extraAttributeBag = $this->getExtraAttributeBag();
        $key = $this->getKey();
        $fileAttachmentsMaxSize = $this->getFileAttachmentsMaxSize();
        $fileAttachmentsAcceptedFileTypes = $this->getFileAttachmentsAcceptedFileTypes();

        $wrapperAttributes = \Filament\Support\prepare_inherited_attributes($extraAttributeBag)
            ->class([
                'fi-input-wrp',
                'fi-fo-markdown-editor',
                'fi-invalid' => filled($statePath) && view()->shared('errors')?->has($statePath),
            ]);

        ob_start(); ?>

        <div <?= $wrapperAttributes->toHtml() ?>>
            <div class="fi-input-wrp-content-ctn">
            <div
                aria-labelledby="<?= e($id) ?>-label"
                id="<?= e($id) ?>"
                role="group"
                x-load
                x-load-src="<?= e(FilamentAsset::getAlpineComponentSrc('markdown-editor', 'filament/forms')) ?>"
                x-data="markdownEditorFormComponent({
                            canAttachFiles: <?= Js::from($this->hasFileAttachments()) ?>,
                            isLiveDebounced: <?= Js::from($this->isLiveDebounced()) ?>,
                            isLiveOnBlur: <?= Js::from($this->isLiveOnBlur()) ?>,
                            liveDebounce: <?= Js::from($this->getNormalizedLiveDebounce()) ?>,
                            maxHeight: <?= Js::from($this->getMaxHeight()) ?>,
                            minHeight: <?= Js::from($this->getMinHeight()) ?>,
                            placeholder: <?= Js::from($this->getPlaceholder()) ?>,
                            state: $wire.<?= $this->applyStateBindingModifiers("\$entangle('{$statePath}')", isOptimisticallyLive: false) ?>,
                            toolbarButtons: <?= Js::from($this->getToolbarButtons()) ?>,
                            translations: <?= Js::from(__('filament-forms::components.markdown_editor')) ?>,
                            uploadFileAttachmentUsing: async (file, onSuccess, onError) => {
                                const acceptedTypes = <?= Js::from($fileAttachmentsAcceptedFileTypes) ?>

                                if (acceptedTypes && ! acceptedTypes.includes(file.type)) {
                                    return onError(<?= Js::from($fileAttachmentsAcceptedFileTypes ? __('filament-forms::components.markdown_editor.file_attachments_accepted_file_types_message', ['values' => implode(', ', $fileAttachmentsAcceptedFileTypes)]) : null) ?>)
                                }

                                const maxSize = <?= Js::from($fileAttachmentsMaxSize) ?>

                                if (maxSize && file.size > +maxSize * 1024) {
                                    return onError(<?= Js::from($fileAttachmentsMaxSize ? trans_choice('filament-forms::components.markdown_editor.file_attachments_max_size_message', $fileAttachmentsMaxSize, ['max' => $fileAttachmentsMaxSize]) : null) ?>)
                                }

                                $wire.upload(`componentFileAttachments.<?= e($statePath) ?>`, file, () => {
                                    $wire
                                        .callSchemaComponentMethod(
                                            <?= Js::from($key) ?>,
                                            'saveUploadedFileAttachmentAndGetUrl',
                                        )
                                        .then((url) => {
                                            if (! url) {
                                                return onError()
                                            }

                                            onSuccess(url)
                                        })
                                })
                            },
                        })"
                wire:ignore
                <?= $this->getExtraAlpineAttributeBag()->toHtml() ?>
            >
                <textarea x-ref="editor" x-cloak></textarea>
            </div>
            </div>
        </div>

        <?php return $this->wrapEmbeddedHtml(ob_get_clean());
    }
}
