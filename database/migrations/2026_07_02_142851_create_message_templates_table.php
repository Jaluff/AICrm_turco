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
        Schema::create('message_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('channel_connection_id')->constrained()->cascadeOnDelete();
            $table->string('external_template_id')->nullable();
            $table->string('name');
            $table->string('language')->default('es');
            $table->string('category')->nullable();
            $table->string('status')->default('APPROVED');
            $table->json('components')->nullable();
            $table->json('variables')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'channel_connection_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_templates');
    }
};
