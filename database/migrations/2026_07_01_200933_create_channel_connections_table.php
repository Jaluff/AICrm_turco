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
        Schema::create('channel_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('type')->default('whatsapp_cloud');
            $table->string('name');
            $table->string('status')->default('active');
            $table->string('external_business_id')->nullable();
            $table->string('external_phone_number_id')->nullable();
            $table->string('external_waba_id')->nullable();
            $table->string('phone_number')->nullable();
            $table->text('access_token')->nullable();
            $table->string('verify_token')->nullable();
            $table->text('app_secret')->nullable();
            $table->text('greeting_message')->nullable();
            $table->text('farewell_message')->nullable();
            $table->jsonb('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channel_connections');
    }
};
