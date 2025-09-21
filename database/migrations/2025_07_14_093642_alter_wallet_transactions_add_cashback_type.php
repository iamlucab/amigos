<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterWalletTransactionsAddCashbackType extends Migration
{
    public function up()
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            DB::statement("ALTER TABLE wallet_transactions MODIFY COLUMN type ENUM('credit', 'debit', 'transfer', 'payment', 'cashback')");
        });
    }

    public function down()
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            DB::statement("ALTER TABLE wallet_transactions MODIFY COLUMN type ENUM('credit', 'debit', 'transfer', 'payment')");
        });
    }
}
