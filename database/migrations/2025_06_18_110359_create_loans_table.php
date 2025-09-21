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
        Schema::create('loans', function (Blueprint $table) {
    $table->id();
    $table->foreignId('member_id')->constrained()->onDelete('cascade');
    $table->decimal('amount', 10, 2);
    $table->decimal('interest_rate', 5, 2);
    $table->integer('term_months');
    $table->decimal('monthly_payment', 10, 2)->nullable();
    $table->string('status')->default('Pending');
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
        Schema::dropIfExists('loans');
    }
};
