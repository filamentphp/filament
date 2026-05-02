<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_messages', function (Blueprint $table): void {
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ticket_messages', function (Blueprint $table): void {
            $table->dropForeignIdFor('Filament\Tests\Fixtures\Models\Ticket');
        });
    }
};
