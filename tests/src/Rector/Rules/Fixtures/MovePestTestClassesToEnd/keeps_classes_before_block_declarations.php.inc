<?php

trait LocalTrait
{
}

class LocalRecord
{
    use LocalTrait;
}

declare(ticks=1) {
    $record = new LocalRecord;
}

it('has a record', function () use ($record) {
    expect($record)->toBeInstanceOf(LocalRecord::class);
});
-----
<?php

trait LocalTrait
{
}

class LocalRecord
{
    use LocalTrait;
}

declare(ticks=1) {
    $record = new LocalRecord;
}

it('has a record', function () use ($record) {
    expect($record)->toBeInstanceOf(LocalRecord::class);
});
