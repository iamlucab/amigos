<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();

            // Member association
            $table->foreignId('member_id')->constrained('members')->onDelete('cascade');

            // Transaction type
            $table->enum('type', ['credit', 'debit', 'transfer', 'payment']);

            // Transaction amount
            $table->decimal('amount', 10, 2);

            // Optional source or reason
            $table->string('source')->nullable(); // e.g., cashback, product_purchase, admin_adjustment

            // Optional description
            $table->text('description')->nullable();

            // Optional related member for transfers/referrals
            $table->unsignedBigInteger('related_member_id')->nullable();

            // Timestamps
            $table->timestamps();

            // Optional foreign key (if related_member_id is linked to members too)
            $table->foreign('related_member_id')->references('id')->on('members')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('wallet_transactions');
    }
}
