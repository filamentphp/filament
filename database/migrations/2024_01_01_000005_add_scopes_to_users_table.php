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
        Schema::table('users', function (Blueprint $table) {
            $table->json('pemda_scope')->nullable()->after('role'); // Stores ['lokasi' => 'Jawa Barat'] or list of locations
            $table->json('kl_scope')->nullable()->after('pemda_scope'); // Stores ['sektor' => 'SDA'] or list of sectors
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['pemda_scope', 'kl_scope']);
        });
    }
};
