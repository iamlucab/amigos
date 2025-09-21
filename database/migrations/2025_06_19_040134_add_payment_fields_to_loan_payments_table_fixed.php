<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentFieldsToLoanPaymentsTableFixed extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if columns exist before adding them
        if (!Schema::hasColumn('loan_payments', 'payment_method')) {
            Schema::table('loan_payments', function (Blueprint $table) {
                $table->string('payment_method')->nullable()->after('is_paid');
            });
        }
        
        if (!Schema::hasColumn('loan_payments', 'reference_number')) {
            Schema::table('loan_payments', function (Blueprint $table) {
                $table->string('reference_number')->nullable()->after('payment_method');
            });
        }
        
        if (!Schema::hasColumn('loan_payments', 'payment_proof')) {
            Schema::table('loan_payments', function (Blueprint $table) {
                $table->string('payment_proof')->nullable()->after('reference_number');
            });
        }
        
        if (!Schema::hasColumn('loan_payments', 'is_verified')) {
            Schema::table('loan_payments', function (Blueprint $table) {
                $table->boolean('is_verified')->default(false)->after('payment_proof');
            });
        }
        
        if (!Schema::hasColumn('loan_payments', 'verified_at')) {
            Schema::table('loan_payments', function (Blueprint $table) {
                $table->timestamp('verified_at')->nullable()->after('is_verified');
            });
        }
        
        if (!Schema::hasColumn('loan_payments', 'verified_by')) {
            Schema::table('loan_payments', function (Blueprint $table) {
                $table->unsignedBigInteger('verified_by')->nullable()->after('verified_at');
                $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
            });
        }
        
        if (!Schema::hasColumn('loan_payments', 'notes')) {
            Schema::table('loan_payments', function (Blueprint $table) {
                $table->text('notes')->nullable()->after('note');
            });
        }
        
        if (!Schema::hasColumn('loan_payments', 'status')) {
            Schema::table('loan_payments', function (Blueprint $table) {
                $table->string('status')->default('Pending')->after('is_paid');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loan_payments', function (Blueprint $table) {
            if (Schema::hasColumn('loan_payments', 'verified_by')) {
                $table->dropForeign(['verified_by']);
            }
            
            $columns = [
                'payment_method',
                'reference_number',
                'payment_proof',
                'is_verified',
                'verified_at',
                'verified_by',
                'notes',
                'status'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('loan_payments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}