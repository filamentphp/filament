<?php

namespace Filament\Forms\Components\RichEditor\StateCasts;

use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\StateCasts\Contracts\StateCast;
use Illuminate\Contracts\Support\Htmlable;
use Tiptap\Editor;

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

        $editor->descendants(function (object &$node): void {
            if ($node->type !== 'customBlock') {
                return;
            }

            unset($node->attrs->label);
            unset($node->attrs->preview);
            unset($node->attrs->shouldApplyProseStylingToPreview);
        });

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

        $shouldPreventTampering = $this->richEditor->shouldPreventFileAttachmentPathTampering();
        $originalFileAttachmentPaths = $shouldPreventTampering ? $this->richEditor->getOriginalFileAttachmentPaths() : [];

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
            ->descendants(function (object &$node) use ($shouldPreventTampering, $originalFileAttachmentPaths): void {
                if ($node->type !== 'image') {
                    return;
                }

                if (blank($node->attrs->id ?? null)) {
                    return;
                }

                if ($shouldPreventTampering && ! $this->richEditor->isFileAttachmentPathAuthorized($node->attrs->id, $originalFileAttachmentPaths)) {
                    $node->attrs->src = null;

                    return;
                }

                $node->attrs->src = $this->richEditor->getFileAttachmentUrl($node->attrs->id) ?? $this->richEditor->getFileAttachmentUrlFromAnotherRecord($node->attrs->id) ?? $node->attrs->src ?? null;
            });

        $editor->descendants(function (object &$node): void {
            if ($node->type !== 'customBlock') {
                return;
            }

            unset($node->attrs->label);
            unset($node->attrs->preview);
            unset($node->attrs->shouldApplyProseStylingToPreview);

            if (blank($node->attrs->id ?? null)) {
                return;
            }

            $block = $this->richEditor->getCustomBlock($node->attrs->id);

            if (blank($block)) {
                return;
            }

            $nodeConfig = json_decode(json_encode($node->attrs->config ?? []), associative: true);

            $node->attrs->label = $block::getPreviewLabel($nodeConfig);
            $node->attrs->preview = base64_encode($block::toPreviewHtml($nodeConfig));
            $node->attrs->shouldApplyProseStylingToPreview = $block::shouldApplyProseStylingToPreview($nodeConfig);
        });

        $this->hydrateMentionLabels($editor);

        return $editor->getDocument();
    }

    protected function hydrateMentionLabels(Editor $editor): void
    {
        $mentionProviders = $this->richEditor->getMentionProviders();

        if (blank($mentionProviders)) {
            return;
        }

        $mentionsByTriggerCharacter = [];

        $editor->descendants(function (object &$node) use (&$mentionsByTriggerCharacter): void {
            if ($node->type !== 'mention') {
                return;
            }

            $triggerCharacter = $node->attrs->char ?? '@';
            $id = $node->attrs->id ?? null;

            if (blank($id)) {
                return;
            }

            $mentionsByTriggerCharacter[$triggerCharacter][] = (string) $id;
        });

        if (blank($mentionsByTriggerCharacter)) {
            return;
        }

        $labelsByTriggerCharacter = [];

        foreach ($mentionsByTriggerCharacter as $triggerCharacter => $ids) {
            foreach ($mentionProviders as $provider) {
                if ($provider->getTriggerCharacter() === $triggerCharacter) {
                    $labelsByTriggerCharacter[$triggerCharacter] = $provider->getLabels(array_unique($ids));

                    break;
                }
            }
        }

        $editor->descendants(function (object &$node) use ($labelsByTriggerCharacter): void {
            if ($node->type !== 'mention') {
                return;
            }

            $triggerCharacter = $node->attrs->char ?? '@';
            $id = $node->attrs->id ?? null;

            if (blank($id)) {
                return;
            }

            $node->attrs->label = $labelsByTriggerCharacter[$triggerCharacter][(string) $id] ?? '';
        });
    }
}
