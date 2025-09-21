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
        Schema::create('membership_code_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membership_code_request_id')->constrained('membership_code_requests')->onDelete('cascade');
            $table->foreignId('membership_code_id')->constrained('membership_codes')->onDelete('cascade');
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
        Schema::dropIfExists('membership_code_request_items');
    }
};