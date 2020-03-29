@auth
    <script data-turbolinks-eval="false">
        /*
        * Code is modified from kdion4891 and inspired by Pastor Ryan Hayden
        * https://github.com/livewire/livewire/issues/106
        * https://github.com/kdion4891/laravel-livewire-forms/blob/master/resources/views/form.blade.php
        * Thank you, gentlemen!
        */
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('input[type="file"]').forEach(file => {
                file.addEventListener('input', event => {
                    let form_data = new FormData();
                    form_data.append('component', @json(get_class($this)));
                    form_data.append('field_name', file.id);

                    for (let i = 0; i < event.target.files.length; i++) {
                        form_data.append('files[]', event.target.files[i]);
                    }

                    fetch('{{ route('filament.admin.file-upload') }}', {
                        method: 'POST',
                        body: form_data,
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }).then(function (response) {
                        if (response.ok) {
                            return response.json();
                        }
                        return Promise.reject(response);
                    }).then(function (data) {
                        window.livewire.emit('filament.fileUpdate', data.field_name, data.uploaded_files);
                    }).catch(function (error) {
                        console.warn('File upload error', error);
                    });
                })
            });
        });
    </script>
@endauth