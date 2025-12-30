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

        if ($this->richEditor->getCustomBlocks()) {
            $editor->descendants(function (object &$node): void {
                if ($node->type !== 'customBlock') {
                    return;
                }

                unset($node->attrs->label);
                unset($node->attrs->preview);
            });
        }

        // Strip mention labels before saving - they are hydrated on load
        if ($this->richEditor->getMentionProviders()) {
            $editor->descendants(function (object &$node): void {
                if (! in_array($node->type, ['mention', 'mentionLink'], true)) {
                    return;
                }

                unset($node->attrs->label);
            });
        }

        // Always return JSON since the JS editor uses JSON internally
        return $editor->getDocument();
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

        $this->hydrateMentionLabels($editor);

        return $editor->getDocument();
    }

    protected function hydrateMentionLabels(Editor $editor): void
    {
        $mentionProviders = $this->richEditor->getMentionProviders();

        if (blank($mentionProviders)) {
            return;
        }

        // Collect all mentions by char
        $mentionsByChar = [];

        $editor->descendants(function (object &$node) use (&$mentionsByChar): void {
            if (! in_array($node->type, ['mention', 'mentionLink'], true)) {
                return;
            }

            $char = $node->attrs->char ?? '@';
            $id = $node->attrs->id ?? null;

            if (blank($id)) {
                return;
            }

            $mentionsByChar[$char][] = (string) $id;
        });

        if (blank($mentionsByChar)) {
            return;
        }

        // Fetch labels for each char
        $labelsByChar = [];

        foreach ($mentionsByChar as $char => $ids) {
            foreach ($mentionProviders as $provider) {
                if ($provider->getChar() === $char) {
                    $labelsByChar[$char] = $provider->getLabels(array_unique($ids));

                    break;
                }
            }
        }

        // Apply labels to mentions
        $editor->descendants(function (object &$node) use ($labelsByChar): void {
            if (! in_array($node->type, ['mention', 'mentionLink'], true)) {
                return;
            }

            $char = $node->attrs->char ?? '@';
            $id = $node->attrs->id ?? null;

            if (blank($id)) {
                return;
            }

            $node->attrs->label = $labelsByChar[$char][(string) $id] ?? '';
        });
    }
}
