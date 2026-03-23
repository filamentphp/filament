<div class="min-h-screen">
    <div id="paginationSimple" class="p-8" style="min-width: 960px;">
        <x-filament::pagination :paginator="$paginator" />
    </div>

    <div id="paginationSimplePaginator" class="p-8 max-w-xl">
        <x-filament::pagination :paginator="$simplePaginator" />
    </div>

    <div id="paginationExtremeLinks" class="p-8" style="min-width: 960px;">
        <x-filament::pagination :paginator="$paginator" extreme-links />
    </div>

    <div id="paginationPageOptions" class="p-8" style="min-width: 960px;">
        <x-filament::pagination
            :paginator="$paginator"
            :page-options="[5, 10, 20, 50, 100, 'all']"
            current-page-option-property="perPage"
        />
    </div>
</div>
