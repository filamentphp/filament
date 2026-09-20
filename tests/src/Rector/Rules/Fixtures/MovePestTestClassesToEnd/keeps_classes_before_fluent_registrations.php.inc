<?php

trait LocalTrait
{
}

class LocalRecord
{
    use LocalTrait;
}

it('throws', function () {
    throw new RuntimeException;
})->throwsIf(
    fn () => (new LocalRecord) instanceof LocalRecord,
    RuntimeException::class,
);
-----
<?php

trait LocalTrait
{
}

class LocalRecord
{
    use LocalTrait;
}

it('throws', function () {
    throw new RuntimeException;
})->throwsIf(
    fn () => (new LocalRecord) instanceof LocalRecord,
    RuntimeException::class,
);
