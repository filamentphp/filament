<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilamentRoleUserTable extends Migration
{
    public function up()
    {
        Schema::create('filament_role_user', function (Blueprint $table) {
            $table->id();
            $table->string('role');
            $table->foreignId('user_id')->constrained('filament_users')->cascadeOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('filament_role_user');
    }
}
