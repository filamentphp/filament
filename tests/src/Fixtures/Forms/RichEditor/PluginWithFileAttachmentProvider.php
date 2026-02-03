<?php

namespace Filament\Tests\Fixtures\Forms\RichEditor;

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\FileAttachmentProviders\Contracts\FileAttachmentProvider;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\HasFileAttachmentProvider;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\RichContentPlugin;
use Filament\Forms\Components\RichEditor\RichEditorTool;
use Tiptap\Core\Extension;

class PluginWithFileAttachmentProvider implements HasFileAttachmentProvider, RichContentPlugin
{
    public function __construct(
        protected ?FileAttachmentProvider $fileAttachmentProvider = null
    ) {
        $this->fileAttachmentProvider ??= new FileAttachmentProviderStub;
    }

    public static function make(?FileAttachmentProvider $fileAttachmentProvider = null): static
    {
        return new static($fileAttachmentProvider);
    }

    public function getFileAttachmentProvider(): ?FileAttachmentProvider
    {
        return $this->fileAttachmentProvider;
    }

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
}
