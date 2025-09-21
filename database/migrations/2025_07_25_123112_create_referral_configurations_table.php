<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referral_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('name');                     // Descriptive name for the configuration
            $table->decimal('total_allocation', 10, 2); // Total amount to distribute (e.g., P2500)
            $table->unsignedTinyInteger('max_level');   // Maximum referral level (e.g., 5)
            $table->json('level_bonuses')->nullable();  // Custom amounts for specific levels (can be empty)
            $table->boolean('is_active')->default(true); // Whether this configuration is active
            $table->text('description')->nullable();     // Optional description
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('referral_configurations');
    }
};
