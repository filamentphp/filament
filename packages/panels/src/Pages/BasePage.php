<?php

namespace Filament\Pages;

use Closure;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Exceptions\Halt;
use Filament\Tables\Contracts\RendersActionModal;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

abstract class BasePage extends Component implements HasForms, HasActions, RendersActionModal
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected static string $layout = 'filament-panels::components.layout.base';

    protected static ?string $title = null;

    protected ?string $heading = null;

    protected ?string $subheading = null;

    protected static string $view;

    public static ?Closure $reportValidationErrorUsing = null;

    protected ?string $maxContentWidth = null;

    public static string $formActionsAlignment = 'start';

    public static bool $formActionsAreSticky = false;

    public static bool $hasInlineLabels = false;

    public function render(): View
    {
        return view(static::$view, $this->getViewData())
            ->layout(static::$layout, [
                'livewire' => $this,
                'maxContentWidth' => $this->getMaxContentWidth(),
                ...$this->getLayoutData(),
            ]);
    }

    public function getHeading(): string | Htmlable
    {
        return $this->heading ?? $this->getTitle();
    }

    public function getSubheading(): string | Htmlable | null
    {
        return $this->subheading;
    }

    public function getTitle(): string | Htmlable
    {
        return static::$title ?? (string) str(class_basename(static::class))
            ->kebab()
            ->replace('-', ' ')
            ->title();
    }

    public function getMaxContentWidth(): ?string
    {
        return $this->maxContentWidth;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getLayoutData(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [];
    }

    protected function onValidationError(ValidationException $exception): void
    {
        if (! static::$reportValidationErrorUsing) {
            return;
        }

        (static::$reportValidationErrorUsing)($exception);
    }

    protected function halt(): void
    {
        throw new Halt();
    }

    protected function callHook(string $hook): void
    {
        if (! method_exists($this, $hook)) {
            return;
        }

        $this->{$hook}();
    }

    public static function stickyFormActions(bool $condition = true): void
    {
        static::$formActionsAreSticky = $condition;
    }

    public static function alignFormActionsStart(): void
    {
        static::$formActionsAlignment = 'start';
    }

    public static function alignFormActionsCenter(): void
    {
        static::$formActionsAlignment = 'center';
    }

    public static function alignFormActionsEnd(): void
    {
        static::$formActionsAlignment = 'end';
    }

    /**
     * @deprecated Use `alignFormActionsStart()` instead
     */
    public static function alignFormActionsLeft(): void
    {
        static::alignFormActionsStart();
    }

    /**
     * @deprecated Use `alignFormActionsEnd()` instead
     */
    public static function alignFormActionsRight(): void
    {
        static::alignFormActionsEnd();
    }

    public function getFormActionsAlignment(): string
    {
        return static::$formActionsAlignment;
    }

    public function areFormActionsSticky(): bool
    {
        return static::$formActionsAreSticky;
    }

    public function hasInlineLabels(): bool
    {
        return static::$hasInlineLabels;
    }

    public static function formActionsAlignment(string $alignment): void
    {
        static::$formActionsAlignment = $alignment;
    }

    public static function inlineLabels(bool $condition = true): void
    {
        static::$hasInlineLabels = $condition;
    }

    /**
     * @return array<string>
     */
    public function getRenderHookScopes(): array
    {
        return [static::class];
    }
}
