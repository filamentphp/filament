<?php

namespace Filament\Actions\Contracts;

interface SubmitsForm
{
    public function canSubmitForm(): bool;

    public function getFormToSubmit(): ?string;
}
