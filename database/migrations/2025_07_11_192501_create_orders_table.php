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
       Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->foreignId('member_id')->constrained('members')->onDelete('cascade');
    $table->decimal('total_amount', 10, 2);
    $table->decimal('total_cashback', 10, 2);
    $table->string('status')->default('pending'); // pending, paid, cancelled
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
        Schema::dropIfExists('orders');
    }
};
