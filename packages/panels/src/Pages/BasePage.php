<?php

namespace Filament\Pages;

use Closure;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasRenderHookScopes;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Attributes\Exposed;
use Filament\Support\Attributes\ServerOnly;
use Filament\Support\Concerns\CanCallHooks;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Filament\Support\Exceptions\Halt;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Component;

abstract class BasePage extends Component implements HasActions, HasRenderHookScopes, HasSchemas
{
    use CanCallHooks;
    use InteractsWithActions;
    use InteractsWithSchemas;

    protected static string $layout = 'filament-panels::components.layout.base';

    protected static ?string $title = null;

    protected ?string $heading = null;

    protected ?string $subheading = null;

    protected string $view;

    public static ?Closure $reportValidationErrorUsing = null;

    protected Width | string | null $maxContentWidth = null;

    /**
     * @var array<mixed>
     */
    protected array $extraBodyAttributes = [];

    public static string | Alignment $formActionsAlignment = Alignment::Start;

    public static bool $formActionsAreSticky = false;

    public static bool $hasInlineLabels = false;

    #[Exposed]
    #[On('refresh-page')]
    public function refresh(): void {}

    #[ServerOnly]
    public function getDefaultActionUrlIdentifier(): ?string
    {
        return 'page';
    }

    protected function getDefaultActionUrlTarget(): mixed
    {
        return $this->defaultActionComponent ?? 'page';
    }

    #[ServerOnly]
    public function render(): View
    {
        return view($this->getView(), $this->getViewData())
            ->layout($this->getLayout(), [
                'livewire' => $this,
                'maxContentWidth' => $this->getMaxContentWidth(),
                ...$this->getLayoutData(),
            ]);
    }

    #[ServerOnly]
    public function getView(): string
    {
        return $this->view;
    }

    #[ServerOnly]
    public function getLayout(): string
    {
        return static::$layout;
    }

    #[ServerOnly]
    public function getHeading(): string | Htmlable | null
    {
        return $this->heading ?? $this->getTitle();
    }

    #[ServerOnly]
    public function getSubheading(): string | Htmlable | null
    {
        return $this->subheading;
    }

    #[ServerOnly]
    public function getTitle(): string | Htmlable
    {
        return static::$title ?? (string) str(class_basename(static::class))
            ->kebab()
            ->replace('-', ' ')
            ->ucwords();
    }

    #[ServerOnly]
    public function getMaxContentWidth(): Width | string | null
    {
        return $this->maxContentWidth;
    }

    /**
     * @return array<mixed>
     */
    #[ServerOnly]
    public function getExtraBodyAttributes(): array
    {
        return $this->extraBodyAttributes;
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

    protected function halt(bool $shouldRollbackDatabaseTransaction = false): void
    {
        throw (new Halt)->rollBackDatabaseTransaction($shouldRollbackDatabaseTransaction);
    }

    #[ServerOnly]
    public static function stickyFormActions(bool $condition = true): void
    {
        static::$formActionsAreSticky = $condition;
    }

    #[ServerOnly]
    public static function alignFormActionsStart(): void
    {
        static::$formActionsAlignment = Alignment::Start;
    }

    #[ServerOnly]
    public static function alignFormActionsCenter(): void
    {
        static::$formActionsAlignment = Alignment::Center;
    }

    #[ServerOnly]
    public static function alignFormActionsEnd(): void
    {
        static::$formActionsAlignment = Alignment::End;
    }

    /**
     * @deprecated Use `alignFormActionsStart()` instead
     */
    #[ServerOnly]
    public static function alignFormActionsLeft(): void
    {
        static::alignFormActionsStart();
    }

    /**
     * @deprecated Use `alignFormActionsEnd()` instead
     */
    #[ServerOnly]
    public static function alignFormActionsRight(): void
    {
        static::alignFormActionsEnd();
    }

    #[ServerOnly]
    public function getFormActionsAlignment(): string | Alignment
    {
        return static::$formActionsAlignment;
    }

    #[ServerOnly]
    public function areFormActionsSticky(): bool
    {
        return static::$formActionsAreSticky;
    }

    #[ServerOnly]
    public function hasInlineLabels(): bool
    {
        return static::$hasInlineLabels;
    }

    #[ServerOnly]
    public static function formActionsAlignment(string | Alignment $alignment): void
    {
        static::$formActionsAlignment = $alignment;
    }

    #[ServerOnly]
    public static function inlineLabels(bool $condition = true): void
    {
        static::$hasInlineLabels = $condition;
    }

    /**
     * @return array<string>
     */
    #[ServerOnly]
    public function getRenderHookScopes(): array
    {
        return [static::class];
    }
}
