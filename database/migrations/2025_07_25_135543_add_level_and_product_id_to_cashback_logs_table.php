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
        Schema::table('cashback_logs', function (Blueprint $table) {
            $table->unsignedTinyInteger('level')->nullable()->after('amount');
            $table->foreignId('product_id')->nullable()->after('order_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cashback_logs', function (Blueprint $table) {
            $table->dropColumn('level');
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');
        });
    }
};
