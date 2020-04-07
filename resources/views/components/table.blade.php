<div {{ $attributes->merge(['class' => 'flex flex-col']) }}>
    <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
        <div class="align-middle inline-block min-w-full shadow rounded overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 text-gray-500 dark:text-gray-400 uppercase tracking-wider">                    
                    <tr>
                        @foreach($headers as $header)
                            <th class="px-6 py-3 font-medium"{!! $loop->last && isset($rows[0]['row_action']) ? ' colspan="2"' : '' !!}>
                                {{ $header }}
                            </th>
                        @endforeach
                    </tr> 
                </thead>
                <tbody class="bg-white dark:bg-gray-800 text-gray-500 dark:text-white text-sm leading-5">
                    @foreach ($rows as $row)
                        <tr>
                            @foreach($headers as $column => $header)
                                <td class="px-6 py-4 whitespace-no-wrap border-t border-gray-200 dark:border-gray-700">
                                    {{ $row[$column] }}
                                </td>
                            @endforeach
                            @isset($row['row_action'])
                                <td class="px-6 py-4 whitespace-no-wrap border-t border-gray-200 dark:border-gray-700 font-medium text-right">
                                    <a href="{{ $row['row_action']['url'] }}" class="transition-colors duration-200 text-indigo-500 dark:text-indigo-400 hover:text-indigo-700 dark-hover:text-indigo-200">{{ $row['row_action']['label'] }}</a>
                                </td>
                            @endisset
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>