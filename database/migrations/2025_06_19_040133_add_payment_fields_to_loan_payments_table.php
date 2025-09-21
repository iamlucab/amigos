<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentFieldsToLoanPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_payments', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('is_paid');
            $table->string('reference_number')->nullable()->after('payment_method');
            $table->string('payment_proof')->nullable()->after('reference_number');
            $table->boolean('is_verified')->default(false)->after('payment_proof');
            $table->timestamp('verified_at')->nullable()->after('is_verified');
            $table->unsignedBigInteger('verified_by')->nullable()->after('verified_at');
            $table->text('notes')->nullable()->after('note');
            $table->string('status')->default('Pending')->after('is_paid');
            
            $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loan_payments', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn([
                'payment_method',
                'reference_number',
                'payment_proof',
                'is_verified',
                'verified_at',
                'verified_by',
                'notes',
                'status'
            ]);
        });
    }
}