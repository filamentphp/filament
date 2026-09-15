<x-layouts.app>
    @php
        $variants = [
            'layoutControl' => [
                'description' => 'No layout() call. The default, for side-by-side comparison. Must look exactly like layoutDemo.',
                'code' => '// no ->layout() call',
            ],
            'layoutDefaultExplicit' => [
                'description' => 'The default layout passed explicitly. Proves the explicit default renders the same as the implicit one.',
                'code' => '->layout(fn (): Schema => $table->getDefaultLayout())',
            ],
            'layoutSortAndSearchRow' => [
                'description' => 'Bulk actions and sort selects at the start of the row, search and the filters trigger at the end, above a grid of records.',
                'code' => <<<'PHP'
->contentGrid(['md' => 2, 'xl' => 3])
->layout(fn (Schema $schema): Schema => $schema->components([
    TableToolbar::make([
        TableToolbarActions::make(),
        TableSortingSettings::make(),
        TableStack::make([TableSearch::make(), TableFiltersTrigger::make()]),
    ]),
    TableSelectionIndicator::make(),
    TableFilterIndicators::make(),
    TableContent::make(),
    TableEmptyState::make(),
    TablePagination::make(),
]))
PHP,
            ],
            'layoutSidebarFilters' => [
                'description' => 'Filters as a page sidebar, no frame around the table.',
                'code' => <<<'PHP'
->contained(false)
->layout(fn (Schema $schema): Schema => $schema->components([
    Grid::make(['lg' => 3])->schema([
        TableStack::make([
            TableStack::make([
                TableToolbar::make([TableToolbarActions::make(), TableSearch::make(), TableColumnManager::make()]),
                TableSelectionIndicator::make(),
                TableFilterIndicators::make(),
                TableContent::make(),
                TableEmptyState::make(),
                TablePagination::make(),
            ])->extraAttributes(['class' => 'fi-ta-main']),
        ])->extraAttributes(['class' => 'fi-ta-ctn'])->columnSpan(['lg' => 2]),
        Section::make('Filters')->schema([TableFilters::make()]),
    ]),
]))
PHP,
            ],
            'layoutHeaderRow' => [
                'description' => 'Heading, header actions, bulk actions and search on one row.',
                'code' => <<<'PHP'
->heading('Team')->description('Everyone with access to this workspace.')
->headerActions([CreateAction::make()])
->layout(fn (Schema $schema): Schema => $schema->components([
    TableToolbar::make([TableHeader::make(), TableToolbarActions::make(), TableStack::make([TableSearch::make()])]),
    TableFilterIndicators::make(),
    TableContent::make(),
    TableEmptyState::make(),
    TablePagination::make(),
]))
PHP,
            ],
            'layoutBulkActionsBelow' => [
                'description' => 'Toolbar actions under the records, search above.',
                'code' => <<<'PHP'
->layout(fn (Schema $schema): Schema => $schema->components([
    TableToolbar::make([TableSearch::make(), TableFiltersTrigger::make()]),
    TableFilterIndicators::make(),
    TableContent::make(),
    TableEmptyState::make(),
    TableToolbar::make([TableToolbarActions::make(), TableSelectionIndicator::make()]),
    TablePagination::make(),
]))
PHP,
            ],
            'layoutPaginationTop' => [
                'description' => 'Pagination above the records, nothing below.',
                'code' => <<<'PHP'
->layout(fn (Schema $schema): Schema => $schema->components([
    TableToolbar::make(),
    TablePagination::make(),
    TableContent::make(),
    TableEmptyState::make(),
]))
PHP,
            ],
            'layoutMinimal' => [
                'description' => 'Records only, everything else self-hides.',
                'code' => <<<'PHP'
->layout(fn (Schema $schema): Schema => $schema->components([
    TableContent::make(),
    TableEmptyState::make(),
]))
PHP,
            ],
            'layoutCollapsibleFiltersAbove' => [
                'description' => 'The collapsible option outside the default position.',
                'code' => <<<'PHP'
->layout(fn (Schema $schema): Schema => $schema->components([
    TableFilters::make()->collapsible(),
    TableToolbar::make([TableToolbarActions::make(), TableSearch::make()]),
    TableContent::make(),
    TableEmptyState::make(),
    TablePagination::make(),
]))
PHP,
            ],
            'layoutBroken' => [
                'description' => 'Deliberately omits TableContent, so you can see the exception message and confirm it says what to do.',
                'code' => <<<'PHP'
->layout(fn (Schema $schema): Schema => $schema->components([
    TableToolbar::make(),
    TablePagination::make(),
]))
PHP,
            ],
        ];
    @endphp

    <div class="mx-auto max-w-4xl space-y-8 p-6">
        <h1 class="text-2xl font-bold">Table layout variants</h1>

        <p class="text-sm text-gray-600 dark:text-gray-400">
            Every variant builds on the same users table, so only the arrangement of the parts differs.
            Compare <code>layoutControl</code> with <code>layoutDefaultExplicit</code> pixel by pixel; in
            <code>layoutSidebarFilters</code> filter by job and confirm the badges appear in the left column;
            in <code>layoutBulkActionsBelow</code> select rows and confirm the count updates in the lower toolbar;
            on <code>layoutCollapsibleFiltersAbove</code> shrink the window to 390px and toggle the filters.
        </p>

        <ul class="space-y-6">
            @foreach ($variants as $method => $variant)
                <li class="rounded-xl border border-gray-200 p-4 dark:border-white/10">
                    <a class="font-semibold text-primary-600 underline" href="{{ url('/tables?table=' . $method) }}">
                        {{ $method }}
                    </a>

                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $variant['description'] }}</p>

                    <pre class="mt-3 overflow-x-auto rounded-lg bg-gray-950 p-4 text-xs text-gray-100">{{ $variant['code'] }}</pre>
                </li>
            @endforeach
        </ul>
    </div>
</x-layouts.app>
