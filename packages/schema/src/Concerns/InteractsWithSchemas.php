<?php

namespace Filament\Schema\Concerns;

use Filament\Support\Contracts\TranslatableContentDriver;
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
    protected array $oldFormState = [];

    public function getFormComponentFileAttachment(string $statePath): ?TemporaryUploadedFile
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

        return app($driver, ['activeLocale' => $this->getActiveFormsLocale() ?? app()->getLocale()]);
    }

    public function getActiveFormsLocale(): ?string
    {
        return null;
    }

    public function getOldFormState(string $statePath): mixed
    {
        return data_get($this->oldFormState, $statePath);
    }
}
