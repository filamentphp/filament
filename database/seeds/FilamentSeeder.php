<?php

use Illuminate\Database\Seeder;

class FilamentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(FilamentPermissionSeeder::class);
    }
}