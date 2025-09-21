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
    Schema::table('products', function (Blueprint $table) {
        $table->decimal('discount_value', 8, 2)->nullable()->after('cashback_amount');
        $table->enum('discount_type', ['flat', 'percent'])->nullable()->after('discount_value');
        $table->string('promo_code')->nullable()->after('discount_type');
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
{
    Schema::table('products', function (Blueprint $table) {
        $table->dropColumn(['discount_value', 'discount_type', 'promo_code']);
    });
}
};
