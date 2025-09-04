<?php

namespace Filament\Forms\Components\TextInput\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Forms\View\FormsIconAlias;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Js;

class CopyAction extends Action
{
    protected string | Closure | null $copyMessage = null;

    protected int | Closure | null $copyMessageDuration = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-forms::components.text_input.actions.copy.label'));

        $this->icon(FilamentIcon::resolve(FormsIconAlias::COMPONENTS_TEXT_INPUT_ACTIONS_COPY) ?? Heroicon::ClipboardDocumentList);

        $this->defaultColor('gray');

        $this->alpineClickHandler(function (mixed $state): string {
            $copyableState = Js::from($state);
            $copyMessageJs = Js::from($this->getCopyMessage($state));
            $copyMessageDurationJs = Js::from($this->getCopyMessageDuration($state));

            return <<<JS
                window.navigator.clipboard.writeText({$copyableState})
                \$tooltip({$copyMessageJs}, {
                    theme: \$store.theme,
                    timeout: {$copyMessageDurationJs},
                })
                JS;
        });
    }

    public static function getDefaultName(): ?string
    {
        return 'copy';
    }

    public function copyMessage(string | Closure | null $message): static
    {
        $this->copyMessage = $message;

        return $this;
    }

    public function copyMessageDuration(int | Closure | null $duration): static
    {
        $this->copyMessageDuration = $duration;

        return $this;
    }

    public function getCopyMessage(mixed $state): string
    {
        return $this->evaluate($this->copyMessage, [
            'state' => $state,
        ]) ?? __('filament-forms::components.text_input.actions.copy.message');
    }

    public function getCopyMessageDuration(mixed $state): int
    {
        return $this->evaluate($this->copyMessageDuration, [
            'state' => $state,
        ]) ?? 2000;
    }
}
