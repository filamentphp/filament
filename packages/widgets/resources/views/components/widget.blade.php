<div
    {{ $attributes->gridColumn($this->getColumnSpan(), $this->getColumnStart())->class(['fi-wi-widget']) }}
>
    {{ $slot }}
</div>
