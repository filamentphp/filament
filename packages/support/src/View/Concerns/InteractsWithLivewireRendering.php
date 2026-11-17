<?php

namespace Filament\Support\View\Concerns;

use Generator;
use Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys;
use Livewire\Mechanisms\ExtendBlade\ExtendBlade;

trait InteractsWithLivewireRendering
{
    /**
     * Bound optional siblings that could otherwise consume neighboring nodes
     * during morphing, including keyed insertions before unkeyed content.
     * Keep matching boundaries present even when their content is empty.
     * Wholly keyed sibling lists need no boundary when isolated from unkeyed content.
     * Terminal optional content needs no boundary if preceding variable siblings are isolated.
     *
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateMorphMarkerHtml(bool $isEnd = false): string
    {
        if ((! config('livewire.inject_morph_markers', true)) || (! ExtendBlade::isRenderingLivewireComponent())) {
            return '';
        }

        return $isEnd ? '<!--[if ENDBLOCK]><![endif]-->' : '<!--[if BLOCK]><![endif]-->';
    }

    /**
     * Keep nested Blade views' loop metadata.
     *
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @param  iterable<mixed>  $items
     * @return Generator<mixed>
     */
    protected function iterateRenderLoop(iterable $items): Generator
    {
        $viewFactory = app('view');
        $previousRenderLoop = SupportCompiledWireKeys::$currentLoop;
        $viewFactory->addLoop($items);

        try {
            foreach ($items as $key => $item) {
                $viewFactory->incrementLoopIndices();

                yield $key => $item;
            }
        } finally {
            $viewFactory->popLoop();
            SupportCompiledWireKeys::$currentLoop = $previousRenderLoop;
        }
    }

    /**
     * The key is already evaluated; reparsing it as Blade is unnecessary.
     *
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function setRenderKey(?string $key): void
    {
        SupportCompiledWireKeys::$currentLoop = [
            'count' => null,
            'index' => null,
            'key' => $key,
        ];
    }
}
