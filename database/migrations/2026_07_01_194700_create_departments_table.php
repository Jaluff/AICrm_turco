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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('color')->nullable();
            $table->text('greeting_message')->nullable();
            $table->text('farewell_message')->nullable();
            $table->text('away_message')->nullable();
            $table->boolean('auto_assignment_enabled')->default(false);
            $table->boolean('assign_offline_enabled')->default(false);
            $table->boolean('redistribute_unavailable_enabled')->default(false);
            $table->boolean('ai_enabled')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
