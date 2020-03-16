<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsSuperAdminToUsersTable extends Migration
{
    /**
     * The name of the table for the migration.
     * 
     * @var string
     */
    protected $tableName = 'users';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn($this->tableName, 'is_super_admin')) {
            Schema::table($this->tableName, function (Blueprint $table) {
                $table->boolean('is_super_admin')->default(0)->after('email');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn($this->tableName, 'is_super_admin')) {
            Schema::table($this->tableName, function (Blueprint $table) {
                $table->dropColumn('is_super_admin');
            });
        }
    }
}
