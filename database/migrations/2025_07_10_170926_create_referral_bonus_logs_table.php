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
        Schema::create('referral_bonus_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('member_id')->constrained(); // recipient
    $table->foreignId('referred_member_id')->constrained('members'); // who triggered the bonus
    $table->tinyInteger('level'); // 1, 2, or 3
    $table->decimal('amount', 10, 2);
    $table->string('description')->nullable();
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
        Schema::dropIfExists('referral_bonus_logs');
    }
};
