<?php

namespace Filament\Forms\Components\RichEditor\Plugins\Contracts;

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\RichEditorTool;
use Tiptap\Core\Extension;

interface RichContentPlugin
{
    /**
     * @return array<Extension>
     */
    public function getTipTapPhpExtensions(): array;

    /**
     * @return array<string>
     */
    public function getTipTapJsExtensions(): array;

    /**
     * @return array<RichEditorTool>
     */
    public function getEditorTools(): array;

    /**
     * @return array<Action>
     */
    public function getEditorActions(): array;
}
