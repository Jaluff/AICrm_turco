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
        Schema::create('business_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week'); // 1 = Lunes, ..., 7 = Domingo
            $table->boolean('enabled')->default(true);
            $table->time('start_time')->default('09:00:00');
            $table->time('end_time')->default('18:00:00');
            $table->timestamps();

            $table->index(['company_id', 'department_id', 'day_of_week']);
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->boolean('business_hours_enabled')->default(false);
            $table->text('away_message')->nullable();
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->boolean('business_hours_enabled')->default(false);
            $table->boolean('use_company_business_hours')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn(['business_hours_enabled', 'use_company_business_hours']);
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['business_hours_enabled', 'away_message']);
        });

        Schema::dropIfExists('business_hours');
    }
};
