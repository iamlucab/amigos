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
      Schema::create('loan_payments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('loan_id')->constrained()->onDelete('cascade');
    $table->date('due_date');
    $table->decimal('amount', 10, 2);
    $table->boolean('is_paid')->default(false);
    $table->timestamp('paid_at')->nullable();
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
        Schema::dropIfExists('loan_payments');
    }
};
