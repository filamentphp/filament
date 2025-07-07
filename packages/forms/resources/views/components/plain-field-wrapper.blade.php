@props([
    'field' => null,
    'id' => null,
    'label' => null,
    'labelTag' => 'label',
])

@php
    use Illuminate\View\ComponentAttributeBag;

    if ($field) {
        $id ??= $field->getId();
        $label ??= $field->getLabel();
    }
@endphp

<div
    data-field-wrapper
    {{
        (new ComponentAttributeBag)
            ->merge($field?->getExtraFieldWrapperAttributes() ?? [], escape: false)
            ->class([
                'fi-fo-field',
            ])
    }}
>
    @if (filled($label))
        <{{ $labelTag }}
            @if ($labelTag === 'label')
                for="{{ $id }}"
            @else
                id="{{ $id }}-label"
            @endif
            class="fi-fo-field-label fi-sr-only"
        >
            {{ $label }}
        </{{ $labelTag }}>
    @endif

    {{ $slot }}
</div>
