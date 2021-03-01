<div class="space-x-1">
    <select wire:model="recordsPerPage" id="records-per-page" class="text-sm md:text-base rounded shadow-sm focus:border-secondary-300 focus:ring focus:ring-secondary-200 focus:ring-opacity-50 border-gray-300">
        <option value="5">5</option>
        <option value="10">10</option>
        <option value="25">25</option>
        <option value="50">50</option>
    </select>

    <label for="records-per-page" class="text-sm md:text-base leading-tight font-medium cursor-pointer">
        {{ __('tables::table.pagination.fields.recordsPerPage.label') }}
    </label>
</div>
