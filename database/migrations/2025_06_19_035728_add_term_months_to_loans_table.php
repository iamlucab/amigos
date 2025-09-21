<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTermMonthsToLoansTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('loans', 'term_months')) {
            Schema::table('loans', function (Blueprint $table) {
                $table->integer('term_months')->default(6);
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('loans', 'term_months')) {
            Schema::table('loans', function (Blueprint $table) {
                $table->dropColumn('term_months');
            });
        }
    }
}
