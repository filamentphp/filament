<ol>
    @foreach ($options as $option)
        <li>
            {{ $option->render() }}
        </li>
    @endforeach
</ol>