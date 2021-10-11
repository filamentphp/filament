<div class="overflow-hidden bg-white shadow rounded-xl">
    <table class="w-full text-left divide-y table-auto">
        <thead>
            <tr class="divide-x bg-gray-50">
                @foreach ($table->getColumns() as $column)
                    <th class="px-4 py-2 text-sm font-semibold text-gray-600">
                        {{ $column->getLabel() }}
                    </th>
                @endforeach
            </tr>
        </thead>

        <tbody class="divide-y whitespace-nowrap">
            <tr class="divide-x">
                <td class="px-4 py-3">Jeff</td>

                <td class="px-4 py-3">Overcoming the imposter syndrome</td>

                <td class="px-4 py-3">24-09-2020</td>
            </tr>

            <tr class="divide-x">
                <td class="px-4 py-3">Lois</td>

                <td class="px-4 py-3">How to work remote</td>

                <td class="px-4 py-3">
                    <span class="inline-flex items-center justify-center h-6 px-2 text-sm font-semibold tracking-tight text-gray-600 bg-gray-100 rounded-full">
                        Draft
                    </span>
                </td>
            </tr>
        </tbody>
    </table>
</div>
