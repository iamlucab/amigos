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
        Schema::table('membership_codes', function (Blueprint $table) {
            $table->boolean('reserved')->default(false)->after('used_at');
            $table->foreignId('reserved_by')->nullable()->constrained('users')->onDelete('set null')->after('reserved');
            $table->timestamp('reserved_at')->nullable()->after('reserved_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('membership_codes', function (Blueprint $table) {
            $table->dropColumn(['reserved', 'reserved_by', 'reserved_at']);
        });
    }
};
