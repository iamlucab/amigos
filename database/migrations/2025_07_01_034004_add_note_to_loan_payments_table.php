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
    Schema::table('loan_payments', function (Blueprint $table) {
        $table->string('note')->nullable()->after('paid_at');
    });
}

public function down()
{
    Schema::table('loan_payments', function (Blueprint $table) {
        $table->dropColumn('note');
    });
}

};
