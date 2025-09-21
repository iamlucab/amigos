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
    $table->string('thumbnail')->nullable();
    $table->json('gallery')->nullable();
    $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
    $table->foreignId('unit_id')->nullable()->constrained()->nullOnDelete();
    $table->json('attributes')->nullable(); // {"color":"Red","size":"L"}
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
            //
        });
    }
};
