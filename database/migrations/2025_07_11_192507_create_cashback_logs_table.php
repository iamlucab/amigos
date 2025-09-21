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
       Schema::create('cashback_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('member_id')->constrained()->onDelete('cascade');
    $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
    $table->decimal('amount', 10, 2);
    $table->string('description');
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
        Schema::dropIfExists('cashback_logs');
    }
};
