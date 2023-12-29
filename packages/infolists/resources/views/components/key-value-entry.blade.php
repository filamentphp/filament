<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    @php
        $state = $getState();
    @endphp

    <div
        {{
            $attributes
                ->merge($getExtraAttributes(), escape: false)
                ->class(['fi-key-value w-full rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10'])
        }}
    >
        <table
            class="w-full table-auto divide-y divide-gray-200 dark:divide-white/5"
        >
            <thead>
                <tr>
                    <th
                        class="px-3 py-2 text-start text-sm font-medium text-gray-700 dark:text-gray-200"
                        scope="col"
                    >
                        {{ $getKeyLabel() }}
                    </th>

                    <th
                        class="px-3 py-2 text-start text-sm font-medium text-gray-700 dark:text-gray-200"
                        scope="col"
                    >
                        {{ $getValueLabel() }}
                    </th>
                </tr>
            </thead>

            <tbody
                class="divide-y divide-gray-200 font-mono text-base sm:text-sm sm:leading-6 dark:divide-white/5"
            >
                @foreach ($state as $key => $value)
                    <tr
                        class="divide-x divide-gray-200 rtl:divide-x-reverse dark:divide-white/5"
                    >
                        <td class="w-1/2 px-3 py-1.5">
                            {{ $key }}
                        </td>

                        <td class="w-1/2 px-3 py-1.5">
                            {{ $value }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-dynamic-component>
