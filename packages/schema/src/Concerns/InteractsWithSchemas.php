<?php

namespace Filament\Schema\Concerns;

use Filament\Support\Contracts\TranslatableContentDriver;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Renderless;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

trait InteractsWithSchemas
{
    /**
     * @var array <string, TemporaryUploadedFile | null>
     */
    public array $componentFileAttachments = [];

    /**
     * @var array<string, mixed>
     */
    protected array $oldSchemaState = [];

    /**
     * @var array<string>
     */
    #[Locked]
    public array $schemaNames = [];

    public function getSchemaComponentFileAttachment(string $statePath): ?TemporaryUploadedFile
    {
        return data_get($this->componentFileAttachments, $statePath);
    }

    /**
     * @return class-string<TranslatableContentDriver> | null
     */
    public function getFilamentTranslatableContentDriver(): ?string
    {
        return null;
    }

    public function makeFilamentTranslatableContentDriver(): ?TranslatableContentDriver
    {
        $driver = $this->getFilamentTranslatableContentDriver();

        if (! $driver) {
            return null;
        }

        return app($driver, ['activeLocale' => $this->getActiveSchemaLocale() ?? app()->getLocale()]);
    }

    public function getActiveSchemaLocale(): ?string
    {
        return null;
    }

    public function getOldSchemaState(string $statePath): mixed
    {
        return data_get($this->oldSchemaState, $statePath);
    }

    public function updatingInteractsWithSchemas(string $statePath): void
    {
        $statePath = (string) str($statePath)->before('.');

        $this->oldSchemaState[$statePath] = data_get($this, $statePath);
    }

    public function updatedInteractsWithSchemas(string $statePath): void
    {
        foreach ($this->getCachedForms() as $form) {
            $form->callAfterStateUpdated($statePath);
        }
    }

    #[Renderless]
    public function getSchemaComponentFileAttachmentUrl(string $statePath): ?string
    {
        foreach ($this->getCachedForms() as $form) {
            if ($url = $form->getComponentFileAttachmentUrl($statePath)) {
                return $url;
            }
        }

        return null;
    }
}
