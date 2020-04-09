<?php

use Illuminate\Database\Seeder;
use Filament\Models\Permission;

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
        foreach($permissions as $permission) {
            Permission::firstOrCreate($permission);
        }
    }
}