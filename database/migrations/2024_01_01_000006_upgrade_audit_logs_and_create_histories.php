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
        Schema::table('audit_logs', function (Blueprint $table) {
            // Rename 'event' to 'event_type' or keep 'event' as alias? Task says 'event_type'.
            // Existing 'event' column exists. I will rename it or just add 'event_type' and migrate data?
            // Since it's a new system phase, I'll assume I can just use 'event' as 'event_type' or add 'event_type'.
            // Task says "Minimum Field: event_type". I'll add 'event_type' and drop 'event' if needed, or just rename.
            // Let's rename 'event' to 'event_type'.
            $table->renameColumn('event', 'event_type');

            $table->json('old_values')->nullable()->after('properties');
            $table->json('new_values')->nullable()->after('old_values');
            $table->string('actor_role')->nullable()->after('actor_id');
            $table->string('actor_type')->default('INTERNAL')->after('actor_role'); // INTERNAL / EXTERNAL / AUDITOR
            $table->string('ip_address')->nullable()->after('actor_type');
            $table->string('user_agent')->nullable()->after('ip_address');
        });

        Schema::create('program_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('programs');
            $table->string('from_status');
            $table->string('to_status');
            $table->foreignId('changed_by')->nullable()->constrained('users');
            $table->string('changed_by_role')->nullable();
            $table->timestamp('changed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->renameColumn('event_type', 'event');
            $table->dropColumn(['old_values', 'new_values', 'actor_role', 'actor_type', 'ip_address', 'user_agent']);
        });

        Schema::dropIfExists('program_status_histories');
    }
};
