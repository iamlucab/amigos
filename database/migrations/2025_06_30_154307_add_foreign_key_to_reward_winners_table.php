<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToRewardWinnersTable extends Migration
{
    public function up()
    {
        Schema::table('reward_winners', function (Blueprint $table) {
            // Make sure the column exists and isn't already a foreign key
            if (!Schema::hasColumn('reward_winners', 'reward_program_id')) return;

            $table->unsignedBigInteger('reward_program_id')->change(); // ensure unsigned
           $table->foreign('reward_program_id', 'fk_rwinners_program')
            ->references('id')->on('reward_programs')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('reward_winners', function (Blueprint $table) {
            $table->dropForeign(['reward_program_id']);
        });
    }
}
