<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsSystemToPermissionsTable extends Migration
{
    /**
     * The name of the table for the migration.
     * 
     * @var string
     */
    protected $tableName = 'permissions';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tableName, function (Blueprint $table) {
            if (!Schema::hasColumn($this->tableName, 'is_system')) {
                $table->boolean('is_system')->default(0)->after('guard_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->tableName, function (Blueprint $table) {
            if (Schema::hasColumn($this->tableName, 'avatar')) {
                $table->dropColumn('is_system');
            }
        });
    }
}
