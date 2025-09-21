<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToWalletsTable extends Migration
{
    public function up()
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->string('type')->default('main')->after('wallet_id'); // main | cashback | etc.
        });
    }

    public function down()
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
