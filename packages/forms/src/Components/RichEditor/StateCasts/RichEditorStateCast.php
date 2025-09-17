<?php

namespace Filament\Forms\Components\RichEditor\StateCasts;

use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\StateCasts\Contracts\StateCast;
use Illuminate\Contracts\Support\Htmlable;

class RichEditorStateCast implements StateCast
{
    public function __construct(
        protected RichEditor $richEditor,
    ) {}

    /**
     * @return string | array<string, mixed>
     */
    public function get(mixed $state): string | array
    {
        $editor = $this->richEditor->getTipTapEditor()
            ->setContent($state ?? [
                'type' => 'doc',
                'content' => [],
            ]);

        if ($this->richEditor->getFileAttachmentsVisibility() === 'private') {
            $editor->descendants(function (object &$node): void {
                if ($node->type !== 'image') {
                    return;
                }

                if (blank($node->attrs->id ?? null)) {
                    return;
                }

                if (blank($node->attrs->src ?? null)) {
                    return;
                }

                $node->attrs->src = null;
            });
        }

        if ($this->richEditor->getCustomBlocks()) {
            $editor->descendants(function (object &$node): void {
                if ($node->type !== 'customBlock') {
                    return;
                }

                unset($node->attrs->label);
                unset($node->attrs->preview);
            });
        }

        return $editor->{$this->richEditor->isJson() ? 'getDocument' : 'getHtml'}();
    }

    /**
     * @return array<string, mixed>
     */
    public function set(mixed $state): array
    {
        if ($state instanceof Htmlable) {
            $state = $state->toHtml();
        }

        $editor = $this->richEditor->getTipTapEditor()
            ->setContent($state ?? [
                'type' => 'doc',
                'content' => [
                    [
                        'type' => 'paragraph',
                        'content' => [],
                    ],
                ],
            ])
            ->descendants(function (object &$node): void {
                if ($node->type !== 'image') {
                    return;
                }

                if (blank($node->attrs->id ?? null)) {
                    return;
                }

                $node->attrs->src = $this->richEditor->getFileAttachmentUrl($node->attrs->id) ?? $this->richEditor->getFileAttachmentUrlFromAnotherRecord($node->attrs->id) ?? $node->attrs->src ?? null;
            });

        if ($this->richEditor->getCustomBlocks()) {
            $editor->descendants(function (object &$node): void {
                if ($node->type !== 'customBlock') {
                    return;
                }

                $block = $this->richEditor->getCustomBlock($node->attrs->id);

                if (blank($block)) {
                    return;
                }

                $nodeConfig = json_decode(json_encode($node->attrs->config ?? []), associative: true);

                $node->attrs->label = $block::getPreviewLabel($nodeConfig);
                $node->attrs->preview = base64_encode($block::toPreviewHtml($nodeConfig));
            });
        }

        return $editor->getDocument();
    }
}
