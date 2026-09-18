<?php

trait LocalTrait
{
}

class LocalRecord
{
    use LocalTrait;

    public static $factory;
}

LocalRecord::$factory = fn () => new LocalRecord;

it('has a factory', fn () => expect(LocalRecord::$factory)
    ->toBeInstanceOf(Closure::class));
-----
<?php

trait LocalTrait
{
}

class LocalRecord
{
    use LocalTrait;

    public static $factory;
}

LocalRecord::$factory = fn () => new LocalRecord;

it('has a factory', fn () => expect(LocalRecord::$factory)
    ->toBeInstanceOf(Closure::class));
