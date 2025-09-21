<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserIdToWalletsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('wallets', 'user_id')) {
            Schema::table('wallets', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');

                // Add foreign key constraint
                $table->foreign('user_id')
                      ->references('id')->on('users')
                      ->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('wallets', 'user_id')) {
            Schema::table('wallets', function (Blueprint $table) {
                // Drop foreign key first
                $table->dropForeign(['user_id']);
                // Then drop the column
                $table->dropColumn('user_id');
            });
        }
    }
}
