@foreach ($fields as $field)
    @if ($group)
        @if ($group === $field->group)
            @include($field->getView())
        @endif
    @else
        @include($field->getView())
    @endisset
@endforeach

@auth
    @pushonce('scripts')
        <script>
            /*
            * Code is modified from kdion4891 and inspired by Pastor Ryan Hayden
            * https://github.com/livewire/livewire/issues/106
            * https://github.com/kdion4891/laravel-livewire-forms/blob/master/resources/views/form.blade.php
            * Thank you, gentlemen!
            */
            document.addEventListener('turbolinks:load', function () {
                document.querySelectorAll('input[type="file"]').forEach(file => {
                    file.addEventListener('change', event => {
                        let form_data = new FormData();

                        form_data.append('component', @json(get_class($this)));
                        form_data.append('field_name', file.name);
                        form_data.append('validation_rules', file.getAttribute('data-validation-rules'));
                        form_data.append('validation_messages', file.getAttribute('data-validation-messages'));

                        for (let i = 0; i < event.target.files.length; i++) {
                            form_data.append('files[]', event.target.files[i]);
                        }
                        
                        fetch('{{ route('filament.admin.file-upload') }}', {
                            method: 'POST',
                            body: form_data,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        }).then(function (response) {
                            if (response.ok) {                                
                                return response.json();
                            }
                            return Promise.reject(response);
                        }).then(function (data) {
                            if (data.errors) {
                                window.livewire.emit('filament.fileUploadError', data.field_name, data.errors);
                            } else {
                                window.livewire.emit('filament.fileUpdate', data.field_name, data.uploaded_files);
                            }
                        }).catch(function (error) {
                            window.livewire.emit('filament.fileUploadError', file.name, error.statusText);
                        });
                    })
                });
            });
        </script>
        @endpushonce
    @endauth
