<?php

namespace Filament\Forms\Contracts;

use Livewire\TemporaryUploadedFile;

interface HasForms
{
    public function dispatchFormEvent(...$args): void;

    public function getActiveFormLocale(): ?string;

    public function getComponentFileAttachment(string $statePath): ?TemporaryUploadedFile;

    public function getComponentFileAttachmentUrl(string $statePath): ?string;

    public function getSelectOptionLabels(string $statePath): array;

    public function getSelectOptionLabel(string $statePath): ?string;

    public function getSelectOptions(string $statePath): array;

    public function getSelectSearchResults(string $statePath, string $search): array;

    public function getUploadedFileUrls(string $statePath): ?array;

    public function removeUploadedFile(string $statePath, string $fileKey): void;

    public function reorderUploadedFiles(string $statePath, array $fileKeys): void;

    public function validate(?array $rules = null, array $messages = [], array $attributes = []);
}
