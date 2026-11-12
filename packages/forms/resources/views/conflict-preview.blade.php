<div {{ $getExtraAttributeBag() }}>
    @foreach ($fields as $field)
        {!! $field->toSchemaHtml() !!}
    @endforeach
</div>
