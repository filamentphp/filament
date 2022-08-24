<form
    x-data="{
        isUploadingFile: false,
        get validationErrors() {
            return $wire.__instance.serverMemo.errors
        }
    }"
    x-on:submit="if (isUploadingFile) $event.preventDefault()"
    x-on:file-upload-started="isUploadingFile = true"
    x-on:file-upload-finished="isUploadingFile = false"
    {{ $attributes->class('space-y-6 filament-form') }}
>
    {{ $slot }}
</form>
