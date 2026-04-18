<?php

namespace Filament\Tests\Fixtures\RichEditor;

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\HasToolbarButtons;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\RichContentPlugin;
use Filament\Forms\Components\RichEditor\RichEditorTool;
use Tiptap\Core\Extension;

class TestRichContentPlugin implements HasToolbarButtons, RichContentPlugin
{
    /**
     * @param  array<string | array<string | array<string>>>  $enabledButtons
     * @param  array<string>  $disabledButtons
     */
    public function __construct(
        protected array $enabledButtons = [],
        protected array $disabledButtons = [],
    ) {}

    /**
     * @return array<Extension>
     */
    public function getTipTapPhpExtensions(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    public function getTipTapJsExtensions(): array
    {
        return [];
    }

    /**
     * @return array<RichEditorTool>
     */
    public function getEditorTools(): array
    {
        return [];
    }

    /**
     * @return array<Action>
     */
    public function getEditorActions(): array
    {
        return [];
    }

    /**
     * @return array<string | array<string | array<string>>>
     */
    public function getEnabledToolbarButtons(): array
    {
        return $this->enabledButtons;
    }

    /**
     * @return array<string>
     */
    public function getDisabledToolbarButtons(): array
    {
        return $this->disabledButtons;
    }
}
