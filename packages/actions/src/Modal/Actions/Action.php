<?php

namespace Filament\Actions\Modal\Actions;

use Filament\Actions\Concerns\CanBeOutlined;
use Filament\Actions\Concerns\CanOpenUrl;
use Filament\Actions\Concerns\CanSubmitForm;
use Filament\Actions\Contracts\SubmitsForm;
use Filament\Actions\StaticAction;

class Action extends StaticAction implements SubmitsForm
{
    use CanBeOutlined;
    use CanOpenUrl;
    use CanSubmitForm;
    use Concerns\CanCancelAction;
    use Concerns\HasAction;

    public const BUTTON_VIEW = 'filament-actions::modal.actions.button-action';

    public static string $alignment = 'left';

    protected function setUp(): void
    {
        parent::setUp();

        $this->defaultView(static::BUTTON_VIEW);
    }

    public function button(): static
    {
        $this->view(static::BUTTON_VIEW);

        return $this;
    }

    public static function alignLeft(): void
    {
        static::$alignment = 'left';
    }

    public static function alignCenter(): void
    {
        static::$alignment = 'center';
    }

    public static function alignRight(): void
    {
        static::$alignment = 'right';
    }

    public static function getAlignment(): string
    {
        return static::$alignment;
    }
}
