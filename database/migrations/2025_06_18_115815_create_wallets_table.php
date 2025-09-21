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
    Schema::create('wallets', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('member_id');
        $table->decimal('balance', 10, 2)->default(0);
        $table->timestamps();

        $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wallets');
    }
};
