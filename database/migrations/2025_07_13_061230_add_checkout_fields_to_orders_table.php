<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCheckoutFieldsToOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_method')->nullable();
            $table->string('delivery_type')->nullable();
            $table->text('delivery_address')->nullable();
            $table->string('contact_number')->nullable();

            $table->string('reference_image')->nullable(); // proof of payment
            $table->string('gcash_note')->nullable();
            $table->string('bank_note')->nullable();
            $table->decimal('amount_sent', 10, 2)->nullable();
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method', 'delivery_type', 'delivery_address',
                'contact_number', 'reference_image', 'gcash_note',
                'bank_note', 'amount_sent'
            ]);
        });
    }
}
