<x-dynamic-component
    :component="$getEntryWrapperView()"
    :entry="$entry"
>
    @php
        $state = $getState();
    @endphp

    <div
        {{
            $attributes
                ->merge($getExtraAttributes(), escape: false)
                ->class(['fi-key-value w-full rounded-xl shadow-sm bg-white ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10'])
        }}
    >
        <table class="w-full divide-y divide-gray-200 table-auto dark:divide-white/5">
            <thead>
                <tr>
                    <th
                        class="px-3 py-2 text-sm font-medium text-gray-700 text-start dark:text-gray-200"
                        scope="col"
                    >
                        {{ $getKeyLabel() }}
                    </th>

                    <th
                        class="px-3 py-2 text-sm font-medium text-gray-700 text-start dark:text-gray-200"
                        scope="col"
                    >
                        {{ $getValueLabel() }}
                    </th>
                </tr>
            </thead>

            <tbody class="font-mono text-base divide-y divide-gray-200 sm:leading-6 sm:text-sm dark:divide-white/5">
                @foreach ($state as $key => $value)
                    <tr class="divide-x divide-gray-200 rtl:divide-x-reverse dark:divide-white/5">
                        <td class="w-1/2 py-1.5 px-3">
                            {{ $key }}
                        </td>

                        <td class="w-1/2 py-1.5 px-3">
                            {{ $value }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-dynamic-component>
