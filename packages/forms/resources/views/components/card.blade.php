<div
    {{ $attributes->merge(array_merge(['id' => $getId()], $getExtraAttributes()))->class(['p-6 bg-white shadow rounded-xl']) }}>
    {{ $getChildComponentContainer() }}
</div>