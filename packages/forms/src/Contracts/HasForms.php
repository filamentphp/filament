<?php

namespace Filament\Forms\Contracts;

use Livewire\TemporaryUploadedFile;

interface HasForms
{
    public function dispatchFormEvent(mixed ...$args): void;

    public function getActiveFormLocale(): ?string;

    public function getComponentFileAttachment(string $statePath): ?TemporaryUploadedFile;

    public function getComponentFileAttachmentUrl(string $statePath): ?string;

    /**
     * @return array<array{'label': string, 'value': string}>
     */
    public function getSelectOptionLabels(string $statePath): array;

    public function getSelectOptionLabel(string $statePath): ?string;

    /**
     * @return array<array{'label': string, 'value': string}>
     */
    public function getSelectOptions(string $statePath): array;

    /**
     * @return array<array{'label': string, 'value': string}>
     */
    public function getSelectSearchResults(string $statePath, string $search): array;

    /**
     * @return array<array{name: string, size: int, type: string, url: string} | null> | null
     */
    public function getUploadedFiles(string $statePath): ?array;

    public function removeUploadedFile(string $statePath, string $fileKey): void;

    /**
     * @param  array<array-key>  $fileKeys
     */
    public function reorderUploadedFiles(string $statePath, array $fileKeys): void;

    /**
     * @param  array<string, array<mixed>> | null  $rules
     * @param  array<string, string>  $messages
     * @param  array<string, string>  $attributes
     * @return array<string, mixed>
     */
    public function validate(?array $rules = null, array $messages = [], array $attributes = []): array;
}
