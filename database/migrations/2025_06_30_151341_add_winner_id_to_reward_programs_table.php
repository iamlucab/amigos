<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWinnerIdToRewardProgramsTable extends Migration
{
    public function up()
    {
        Schema::table('reward_programs', function (Blueprint $table) {
            $table->foreignId('winner_id')->nullable()->constrained('reward_winners')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('reward_programs', function (Blueprint $table) {
            $table->dropForeign(['winner_id']);
            $table->dropColumn('winner_id');
        });
    }
}
