@php
    $columnSpanClass = [
        '',
        'lg:col-span-1',
        'lg:col-span-2',
        'lg:col-span-3',
        'lg:col-span-4',
        'lg:col-span-5',
        'lg:col-span-6',
        'lg:col-span-7',
        'lg:col-span-8',
        'lg:col-span-9',
        'lg:col-span-10',
        'lg:col-span-11',
        'lg:col-span-12',
    ][$formComponent->getColumnSpan()]
@endphp

<div class="{{ $columnSpanClass }} space-y-3">
    @if ($formComponent->getHeading())
        <div class="space-y-1">
            <h3 class="text-lg leading-tight font-medium">
                {{ __($formComponent->getHeading()) }}
            </h3>

            @if ($formComponent->getSubheading())
                <p class="text-gray-700 text-sm">
                    {{ __($formComponent->getSubheading()) }}
                </p>
            @endif
        </div>
    @endif

    <x-forms::layout :schema="$formComponent->getSchema()" :columns="$formComponent->getColumns()" />
</div>
