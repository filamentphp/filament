<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->after('password', function (Blueprint $table): void {
                $table->json('json')->nullable();
                $table->boolean('has_email_authentication')->default(false);
                $table->string('app_authentication_secret')->nullable();
                $table->text('app_authentication_recovery_codes')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn([
                'json',
                'has_email_authentication',
                'app_authentication_secret',
                'app_authentication_recovery_codes',
            ]);
        });
    }
};
