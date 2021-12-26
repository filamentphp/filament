<?php

namespace Filament\Forms\Contracts;

use Livewire\TemporaryUploadedFile;

interface HasForms
{
    public function dispatchFormEvent(...$args): void;

    public function getComponentFileAttachment(string $statePath): ?TemporaryUploadedFile;

    public function getComponentFileAttachmentUrl(string $statePath): ?string;

    public function getMultiSelectOptionLabels(string $statePath): array;

    public function getMultiSelectSearchResults(string $statePath, string $query): array;

    public function getSelectOptionLabel(string $statePath): ?string;

    public function getSelectSearchResults(string $statePath, string $query): array;

    public function getUploadedFileUrl(string $statePath, string $fileKey): ?string;

    public function removeUploadedFile(string $statePath, string $fileKey): void;

    public function validate(?array $rules = null, array $messages = [], array $attributes = []);
}
