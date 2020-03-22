<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLoginFieldsToUsersTable extends Migration
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
        Schema::table($this->tableName, function (Blueprint $table) {
            if (!Schema::hasColumn($this->tableName, 'last_login_at')) {
                $table->datetime('last_login_at')->nullable();
            }

            if (!Schema::hasColumn($this->tableName, 'last_login_ip')) {
                $table->string('last_login_ip')->nullable();
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
            if (!Schema::hasColumn($this->tableName, 'last_login_at')) {
                $table->dropColumn('last_login_at');
            }

            if (!Schema::hasColumn($this->tableName, 'last_login_ip')) {
                $table->dropColumn('last_login_ip');
            }
        });
    }
}
