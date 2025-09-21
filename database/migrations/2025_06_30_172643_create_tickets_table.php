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
        Schema::create('tickets', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('member_id');
    $table->string('subject');
    $table->text('message');
    $table->enum('status', ['pending', 'in_process', 'closed'])->default('pending');
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
        Schema::dropIfExists('tickets');
    }
};
