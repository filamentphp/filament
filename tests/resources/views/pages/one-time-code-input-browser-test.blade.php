<x-filament-panels::page>
    <div x-data="{ submissionAttemptCount: 0 }">
        <form x-on:submit="submissionAttemptCount++" wire:submit="save">
            {{ $this->form }}

            <x-filament::button type="submit">Save</x-filament::button>
        </form>

        <p>
            Submitted code:
            <span data-testid="submitted-code">
                {{ data_get($data, 'code') }}
            </span>
        </p>

        <p>
            Submission attempt count:
            <span
                data-testid="submission-attempt-count"
                x-text="submissionAttemptCount"
            ></span>
        </p>
    </div>
</x-filament-panels::page>
