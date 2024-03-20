<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div
        {{
            $attributes
                ->merge($getExtraAttributes(), escape: false)
                ->class(['fi-in-key-value w-full rounded-lg bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10'])
        }}
    >
        <table
            class="w-full table-auto divide-y divide-gray-200 dark:divide-white/5"
        >
            <thead>
                <tr>
                    <th
                        scope="col"
                        class="px-3 py-2 text-start text-sm font-medium text-gray-700 dark:text-gray-200"
                    >
                        {{ $getKeyLabel() }}
                    </th>

                    <th
                        scope="col"
                        class="px-3 py-2 text-start text-sm font-medium text-gray-700 dark:text-gray-200"
                    >
                        {{ $getValueLabel() }}
                    </th>
                </tr>
            </thead>

            <tbody
                class="divide-y divide-gray-200 font-mono text-base dark:divide-white/5 sm:text-sm sm:leading-6"
            >
                @forelse (($getState() ?? []) as $key => $value)
                    <tr
                        class="divide-x divide-gray-200 dark:divide-white/5 rtl:divide-x-reverse"
                    >
                        <td class="w-1/2 px-3 py-1.5">
                            {{ $key }}
                        </td>

                        <td class="w-1/2 px-3 py-1.5">
                            {{ $value }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td
                            colspan="2"
                            class="px-3 py-2 text-center font-sans text-sm text-gray-400 dark:text-gray-500"
                        >
                            {{ $getPlaceholder() }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-dynamic-component>
