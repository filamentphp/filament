@if (count($pageOptions) > 1)
    <div class="fi-pagination-records-per-page-select-ctn">
        <label class="fi-pagination-records-per-page-select fi-compact">
            <x-filament::input.wrapper>
                <x-filament::input.select
                    :wire:model.live="$currentPageOptionProperty"
                >
                    @foreach ($pageOptions as $option)
                        <option value="{{ $option }}">
                            {{ $option === 'all' ? __('filament::components/pagination.fields.records_per_page.options.all') : $option }}
                        </option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>

            <span class="fi-sr-only">
                {{ __('filament::components/pagination.fields.records_per_page.label') }}
            </span>
        </label>

        <label class="fi-pagination-records-per-page-select">
            <x-filament::input.wrapper
                :prefix="__('filament::components/pagination.fields.records_per_page.label')"
            >
                <x-filament::input.select
                    :wire:model.live="$currentPageOptionProperty"
                >
                    @foreach ($pageOptions as $option)
                        <option value="{{ $option }}">
                            {{ $option === 'all' ? __('filament::components/pagination.fields.records_per_page.options.all') : $option }}
                        </option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>
        </label>
    </div>
@endif
