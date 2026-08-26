<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fo_query_builder_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('parent_id')->nullable();
            $table->unsignedInteger('length');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fo_query_builder_items');
    }
};
