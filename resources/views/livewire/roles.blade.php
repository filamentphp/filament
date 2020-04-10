@section('title', $title)

<div>

    <div class="flex flex-col">
        <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
            <div class="table-simple">
                <table>
                    <thead>                    
                        <tr>
                            <th>
                                <button class="flex" wire:click.prevent="sortBy('name')">
                                    @include('filament::partials.sort-header', [
                                        'field' => 'name',
                                        'label' => __('Name'),
                                    ])
                                </button>
                            </th>
                            <th colspan="2">
                                <button class="flex" wire:click.prevent="sortBy('description')">
                                    @include('filament::partials.sort-header', [
                                        'field' => 'description',
                                        'label' => __('Description'),
                                    ])
                                </button>
                            </th>
                        </tr> 
                    </thead>
                    <tbody>
                        @forelse ($items as $item)
                            <tr>
                                <td class="font-medium">{{ $item->name }}</td>
                                <td>{{ $item->description }}</td>
                                <td class="font-medium text-right">
                                    {{ $item->action }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center" colspan="3">{{ __('No roles found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-6">
        {{ $results->links('filament::partials.links') }}
    </div>

</div>