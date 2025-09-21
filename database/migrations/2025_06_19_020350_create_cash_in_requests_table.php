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
       Schema::create('cash_in_requests', function (Blueprint $table) {
    $table->id();
    $table->foreignId('member_id')->constrained();
    $table->decimal('amount', 10, 2);
    $table->string('note')->nullable();
    $table->string('status')->default('pending'); // pending, approved, rejected
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
        Schema::dropIfExists('cash_in_requests');
    }
};
