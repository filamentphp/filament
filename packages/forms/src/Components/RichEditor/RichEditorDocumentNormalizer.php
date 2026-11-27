<?php

namespace Filament\Forms\Components\RichEditor;

use stdClass;

/**
 * @internal Never call or extend this class. It is not part of the public API and may change in minor and patch releases.
 */
class RichEditorDocumentNormalizer
{
    // Leave room for Livewire's synthetic tuples within PHP's 512-level JSON limit.
    protected const MAX_DEPTH = 100;

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

        // PHP checks encoding depth after descent, so bound containers before encoding too.
        if (! $this->isWithinDepth($content)) {
            return ['type' => 'doc', 'content' => []];
        }

        $content = $this->normalizeCustomBlockConfiguration($content);

        if (json_encode($content, depth: self::MAX_DEPTH) === false) {
            return ['type' => 'doc', 'content' => []];
        }

        $content['content'] ??= [];

        return $this->normalizeNode($content);
    }

    /**
     * @param  string | array<string, mixed>  $content
     * @return ($content is array ? array<string, mixed> : string | array<string, mixed>)
     */
    public function normalizeCustomBlockConfiguration(string | array $content): string | array
    {
        if (is_string($content)) {
            $document = json_decode($content, associative: true);

            if (! is_array($document)) {
                return $content;
            }

            $content = $document;
        }

        if (($content['type'] ?? null) === 'customBlock') {
            $attributes = $content['attrs'] ?? null;

            if ((is_array($attributes) || is_object($attributes)) && (json_encode(((array) $attributes)['config'] ?? null) === false)) {
                $content['attrs'] = [...(array) $attributes, 'config' => null];
            }
        }

        if (is_array($content['content'] ?? null)) {
            foreach ($content['content'] as $index => $node) {
                if (is_array($node)) {
                    $content['content'][$index] = $this->normalizeCustomBlockConfiguration($node);
                }
            }
        }

        return $content;
    }

    /**
     * @param  array<string, mixed>  $node
     * @return array<string, mixed>
     */
    protected function normalizeNode(array $node): array
    {
        $node = $this->normalizeAttributes($node);

        if (isset($node['text']) && ! is_string($node['text'])) {
            unset($node['text']);
        }

        if ($node['type'] === 'text') {
            // `descendants()` treats text as a leaf, so serialization must too.
            unset($node['content']);
        } elseif (array_key_exists('content', $node)) {
            $content = [];

            foreach (is_array($node['content'] ?? null) ? $node['content'] : [] as $child) {
                if (! is_array($child) || ! is_string($child['type'] ?? null)) {
                    continue;
                }

                if (($child['type'] === 'text') && ((! is_string($child['text'] ?? null)) || ($child['text'] === ''))) {
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
            $node['marks'] = array_map($this->normalizeAttributes(...), array_values(array_filter(
                is_array($node['marks']) ? $node['marks'] : [],
                static fn (mixed $mark): bool => is_array($mark) && is_string($mark['type'] ?? null),
            )));
        }

        return $node;
    }

    /**
     * @param  array<string, mixed>  $node
     * @return array<string, mixed>
     */
    protected function normalizeAttributes(array $node): array
    {
        $attributes = $node['attrs'] ?? null;

        if ($attributes instanceof stdClass) {
            $attributes = (array) $attributes;
        }

        if (($attributes !== null) && (! is_array($attributes) || array_is_list($attributes))) {
            unset($node['attrs']);
        }

        return $node;
    }

    protected function isWithinDepth(mixed $value, int $depth = 0): bool
    {
        if (! is_array($value) && ! ($value instanceof stdClass)) {
            return true;
        }

        if ($depth >= self::MAX_DEPTH) {
            return false;
        }

        foreach ($value as $child) {
            if (! $this->isWithinDepth($child, $depth + 1)) {
                return false;
            }
        }

        return true;
    }
}
