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
    Schema::table('order_items', function (Blueprint $table) {
        $table->decimal('cashback_amount', 10, 2)->default(0)->after('price');
    });
}

public function down()
{
    Schema::table('order_items', function (Blueprint $table) {
        $table->dropColumn('cashback_amount');
    });
}

};
