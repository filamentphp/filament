<?php

namespace Filament\Support\Commands\Concerns;

use Filament\Exceptions\NoDefaultPanelSetException;
use Filament\Facades\Filament;
use Filament\Panel;
use Illuminate\Support\Arr;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;

trait HasPanel
{
    protected ?Panel $panel;

    protected function configurePanel(string $question, ?string $initialQuestion = null): void
    {
        if (! class_exists(Panel::class)) {
            $this->panel = null;

            return;
        }

        $panelName = ($this->hasArgument('panel') ? $this->argument('panel') : null) ?? $this->option('panel'); /** @phpstan-ignore-line */
        $this->panel = filled($panelName) ? Filament::getPanel($panelName, isStrict: false) : null;

        if ($this->panel) {
            return;
        }

        if (filled($initialQuestion) && (! confirm(label: $initialQuestion))) {
            $this->panel = null;

            return;
        }

        $panels = Filament::getPanels();

        if (count($panels) > 1) {
            try {
                $defaultPanelId = Filament::getDefaultPanel()->getId();
            } catch (NoDefaultPanelSetException) {
                $defaultPanelId = null;
            }

            /** @var Panel $panel */
            $panel = $panels[select(
                label: $question,
                options: array_map(
                    fn (Panel $panel): string => $panel->getId(),
                    $panels,
                ),
                default: $defaultPanelId,
            )];
        } else {
            /** @var Panel $panel */
            $panel = Arr::first($panels);
        }

        $this->panel = $panel;
    }
}
