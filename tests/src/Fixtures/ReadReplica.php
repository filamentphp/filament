<?php

namespace Filament\Tests\Fixtures;

use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;
use PDO;

class ReadReplica
{
    public static function connection(string $schema): Connection
    {
        config(['database.connections.read-replica' => [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'sticky' => false,
        ]]);

        $connection = DB::connection('read-replica');
        $readPdo = new PDO('sqlite::memory:');
        $connection->getPdo()->exec($schema);
        $readPdo->exec($schema);
        $connection->setReadPdo($readPdo);

        return $connection;
    }
}
