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
        Schema::create('membership_code_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('amount_per_code', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->string('payment_method'); // GCash, Bank, Wallet
            $table->string('status')->default('pending'); // pending, approved, rejected, cancelled
            $table->string('proof_path')->nullable();
            $table->text('note')->nullable();
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
        Schema::dropIfExists('membership_code_requests');
    }
};
