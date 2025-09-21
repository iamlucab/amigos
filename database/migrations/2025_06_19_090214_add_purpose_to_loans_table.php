<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPurposeToLoansTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('loans', 'purpose')) {
            Schema::table('loans', function (Blueprint $table) {
                $table->string('purpose')->nullable()->after('amount');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('loans', 'purpose')) {
            Schema::table('loans', function (Blueprint $table) {
                $table->dropColumn('purpose');
            });
        }
    }
}

