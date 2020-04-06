<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Contracts\Permission as PermissionContract;

class FilamentPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = require(Filament::databasePath('data/permissions.php'));
        $permissionClass = app(PermissionContract::class);
        foreach($permissions as $permission) {
            $permissionClass::firstOrCreate($permission);
        }
    }
}