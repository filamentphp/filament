<?php

trait LocalTrait
{
}

class LocalRecord
{
    use LocalTrait;
}

describe('records', function () {
    $record = new LocalRecord;

    it('has a record', fn () => expect($record)
        ->toBeInstanceOf(LocalRecord::class));
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

describe('records', function () {
    $record = new LocalRecord;

    it('has a record', fn () => expect($record)
        ->toBeInstanceOf(LocalRecord::class));
});
