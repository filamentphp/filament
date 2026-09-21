<?php

namespace Filament\Tests\Fixtures\Schemas\Components;

use Filament\Forms\Components\Concerns\HasJsRenderer;
use Filament\Forms\Components\Field;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Components\Attributes\Exposed;
use Filament\Support\Facades\FilamentAsset;
use Livewire\Attributes\Renderless;

class FrameworkField extends Field
{
    use HasJsRenderer;

    #[Exposed]
    public function replaceTitle(string $title): array
    {
        $previous = $this->getState();
        $this->state([...$previous, 'title' => $title]);

        return ['path' => $this->getStatePath(), 'previous' => $previous['title'], 'title' => $title];
    }

    #[Exposed]
    #[Renderless]
    public function inspectTitle(string $prefix): string
    {
        return $prefix . $this->getState()['title'];
    }

    public function unexposedMethod(): void
    {
        $this->state(['title' => 'Forbidden', 'enabled' => false, 'tags' => []]);
    }

    public function getRenderer(): string
    {
        return FilamentAsset::getScriptSrc($this->getLivewire()->framework, 'tests/js-fields');
    }

    public function getRendererConfiguration(): array
    {
        return $this->evaluate(static fn (Get $get): array => blank($get('caption')) ? [] : ['caption' => $get('caption')]);
    }
}
