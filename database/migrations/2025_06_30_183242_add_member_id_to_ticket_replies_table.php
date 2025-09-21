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
    Schema::table('ticket_replies', function (Blueprint $table) {
        $table->unsignedBigInteger('member_id')->nullable()->after('replied_by');
        $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ticket_replies', function (Blueprint $table) {
            //
        });
    }
};
