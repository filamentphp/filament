<?php

namespace Filament\Tests\Fixtures\Widgets;

use Filament\Support\Facades\FilamentAsset;
use Filament\Support\RawJs;
use Filament\Widgets\Concerns\HasJsRenderer;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\Widget;
use Livewire\Attributes\Locked;

class JsWidget extends Widget
{
    use HasJsRenderer;
    use InteractsWithPageFilters;

    protected static bool $isLazy = false;

    #[Locked]
    public string $framework = 'js';

    public int $total = 19;

    public function getRenderer(): string | RawJs
    {
        if ($this->framework === 'inline') {
            return RawJs::make(<<<'JS'
                ({ host, props, utilities }) => {
                    window.widgetMounts = (window.widgetMounts ?? 0) + 1
                    const heading = document.createElement('h2')
                    const description = document.createElement('p')
                    const button = document.createElement('button')
                    button.type = 'button'
                    button.textContent = 'Refresh total'
                    button.onclick = () => utilities.$wire.$call('refreshTotal')
                    host.append(heading, description, button)
                    const update = ({ configuration }) => {
                        heading.textContent = configuration.heading
                        description.textContent = configuration.description
                    }
                    update(props)
                    return {
                        update,
                        destroy() {
                            window.widgetDisposals = (window.widgetDisposals ?? 0) + 1
                            host.replaceChildren()
                        },
                    }
                }
                JS);
        }

        if ($this->framework === 'broken') {
            return RawJs::make('() => { throw new Error("Unavailable renderer") }');
        }

        return FilamentAsset::getScriptSrc('widget-' . $this->framework, 'tests/js-widgets');
    }

    /** @return array<string, mixed> */
    public function getRendererConfiguration(): array
    {
        return [
            'heading' => 'Revenue overview',
            'description' => ($this->pageFilters['period'] ?? 'All time') . ': ' . $this->total,
        ];
    }

    public function refreshTotal(): void
    {
        $this->total = 0;
    }
}
