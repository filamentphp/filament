<?php

namespace Filament\Forms\Contracts;

use SplFileInfo;

interface HasForms
{
    public function dispatchFormEvent(...$args): void;

    public function getComponentFileAttachment(string $statePath): ?SplFileInfo;

    public function getComponentFileAttachmentUrl(string $statePath): ?string;

    public function getMultiSelectOptionLabels(string $statePath): array;

    public function getMultiSelectSearchResults(string $statePath, string $query): array;

    public function getSelectOptionLabel(string $statePath): ?string;

    public function getSelectSearchResults(string $statePath, string $query): array;

    public function getUploadedFileUrl(string $statePath): ?string;

    public function removeUploadedFile(string $statePath): void;

    public function validate(?array $rules = null, array $messages = [], array $attributes = []);
}
