<div {{ $attributes->merge($getExtraAttributes(), escape: true)->class(['filament-forms-builder-component-block py-8']) }}>
    {{ $getChildComponentContainer() }}
</div>
