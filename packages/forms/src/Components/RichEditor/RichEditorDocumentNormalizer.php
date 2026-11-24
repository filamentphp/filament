<?php

namespace Filament\Forms\Components\RichEditor;

/**
 * @internal Never call or extend this class. It is not part of the public API and may change in minor and patch releases.
 */
class RichEditorDocumentNormalizer
{
    /**
     * @return string | array<string, mixed>
     */
    public function __invoke(mixed $content): string | array
    {
        if (is_string($content)) {
            $document = json_decode($content, associative: true);

            // Match upstream JSON detection; leave HTML parsing unchanged.
            if (json_last_error() !== JSON_ERROR_NONE) {
                return $content;
            }

            $content = $document;
        }

        if (! is_array($content) || (($content['type'] ?? null) !== 'doc')) {
            return ['type' => 'doc', 'content' => []];
        }

        $content['content'] ??= [];

        return $this->normalizeNode($content);
    }

    /**
     * @param  array<string, mixed>  $node
     * @return array<string, mixed>
     */
    protected function normalizeNode(array $node): array
    {
        if ($node['type'] === 'text') {
            // `descendants()` treats text as a leaf, so serialization must too.
            unset($node['content']);
        } elseif (array_key_exists('content', $node)) {
            $content = [];

            foreach (is_array($node['content'] ?? null) ? $node['content'] : [] as $child) {
                if (! is_array($child) || ! is_string($child['type'] ?? null)) {
                    continue;
                }

                // These nodes are only for trusted renderer transformations.
                if (in_array($child['type'], ['renderedCustomBlock', 'rawHtmlMergeTag'], strict: true)) {
                    continue;
                }

                $content[] = $this->normalizeNode($child);
            }

            $node['content'] = $content;
        }

        if (array_key_exists('marks', $node)) {
            $node['marks'] = array_values(array_filter(
                is_array($node['marks']) ? $node['marks'] : [],
                static fn (mixed $mark): bool => is_array($mark) && is_string($mark['type'] ?? null),
            ));
        }

        return $node;
    }
}
