@extends('filament::layouts.app', ['classes' => ['body' => '']])

@section('content')
    <div class="h-screen flex overflow-hidden bg-gray-100" x-data="{ sidebarOpen: false }" @keydown.window.escape="sidebarOpen = false">    
        {{-- Off-canvas menu for mobile --}}
        <div class="md:hidden">
            <div @click="sidebarOpen = false" class="fixed inset-0 z-30 bg-gray-600 opacity-0 pointer-events-none transition-opacity ease-linear duration-300" :class="{'opacity-75 pointer-events-auto': sidebarOpen, 'opacity-0 pointer-events-none': !sidebarOpen}"></div>
            <div class="fixed inset-y-0 left-0 flex flex-col z-40 max-w-xs w-full bg-gray-800 transform ease-in-out duration-300 -translate-x-full" :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}">
                <div class="absolute top-0 right-0 -mr-14 p-1">
                    <button x-show="sidebarOpen" @click="sidebarOpen = false" class="flex items-center justify-center h-12 w-12 rounded-full focus:outline-none focus:bg-gray-600">
                        {{ Filament::svg('heroicons/outline-md/md-x', 'h-6 w-6 text-white') }}
                    </button>
                </div>
                <div class="h-0 flex-1 overflow-y-auto pt-5 pb-4">
                    <div class="flex-shrink-0 flex items-center px-4">
                        @include('filament::partials.logo', ['classes' => 'h-8 w-auto rounded-full mr-3'])
                        <h2 class="text-lg leading-5 font-medium text-gray-200">
                            {{ config('app.name') }}
                        </h2>
                    </div>
                    @includeWhen(config('filament.nav.admin'), 'filament::partials.nav', ['nav' => config('filament.nav.admin')])
                </div>
                <div class="flex-shrink-0 flex bg-gray-700 p-4">
                    @include('filament::partials.user-actions')
                </div>
            </div>
        </div>   
        {{-- Static sidebar for desktop --}}
        <div class="hidden md:flex md:flex-shrink-0">
            <div class="flex flex-col w-64 bg-gray-800">
                <div class="h-0 flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
                    <div class="flex items-center flex-shrink-0 px-4">
                        @include('filament::partials.logo', ['classes' => 'h-8 w-auto rounded-full mr-3'])
                        <h2 class="text-lg leading-5 font-medium text-gray-200">
                            {{ config('app.name') }}
                        </h2>
                    </div>
                    @includeWhen(config('filament.nav.admin'), 'filament::partials.nav', ['nav' => config('filament.nav.admin')])
                </div>
                <div class="flex-shrink-0 flex bg-gray-700 p-4">
                    @include('filament::partials.user-actions')
                </div>
            </div>
        </div>
        {{-- Main content --}}
        <div class="flex flex-col w-0 flex-1 overflow-hidden">
            <main id="content" class="flex-1 relative z-0 overflow-y-auto focus:outline-none" tabindex="0">
                <div class="max-w-7xl mx-auto p-4 sm:px-6 lg:py-6 lg:px-8 flex items-center justify-between">
                    <h1 class="text-2xl font-semibold text-gray-900">@yield('title')</h1>
                    <button @click.stop="sidebarOpen = true" class="-ml-0.5 -mt-0.5 h-12 w-12 inline-flex items-center justify-center rounded text-gray-500 transition ease-in-out duration-150 hover:text-gray-900 focus:outline-none focus:bg-gray-200 md:hidden">
                        {{ Filament::svg('heroicons/outline-md/md-menu-alt-1', 'h-6 w-6') }}
                    </button>
                </div>       
                <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                    @yield('main')
                </div>
            </main>
        </div>
    </div>

@endsection

@push('scripts')
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
@endpush