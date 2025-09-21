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
    Schema::table('wallet_transactions', function (Blueprint $table) {
        if (!Schema::hasColumn('wallet_transactions', 'member_id')) {
            $table->unsignedBigInteger('member_id')->nullable()->after('wallet_id');
        }
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropForeign(['member_id']);
            $table->dropIndex(['member_id']);
            $table->dropColumn('member_id');
        });
    }
};
