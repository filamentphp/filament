<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilamentUsersTable extends Migration
{
    public function up()
    {
        Schema::create('filament_users', function (Blueprint $table) {
            $table->id();
            $table->string('avatar')->nullable();
            $table->string('email')->unique();
            $table->string('name');
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('filament_users');
    }
}
