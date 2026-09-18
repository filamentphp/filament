<?php

function helper(): string
{
    return 'value';
}

class Probe
{
    public function value(): string
    {
        return helper();
    }
}

it('calls the helper', fn () => expect((new Probe)->value())->toBe('value'));
-----
<?php

function helper(): string
{
    return 'value';
}

class Probe
{
    public function value(): string
    {
        return helper();
    }
}

it('calls the helper', fn () => expect((new Probe)->value())->toBe('value'));
