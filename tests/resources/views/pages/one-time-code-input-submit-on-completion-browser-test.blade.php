<x-filament-panels::page>
    <div x-data="{ submissionAttemptCount: 0 }">
        <form x-on:submit="submissionAttemptCount++" wire:submit="save">
            {{ $this->form }}
        </form>

        <x-filament::button data-testid="reset-code" wire:click="resetCode">
            Reset code
        </x-filament::button>

        <p>
            Submitted code:
            <span data-testid="submitted-code">
                {{ $submittedCode }}
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
