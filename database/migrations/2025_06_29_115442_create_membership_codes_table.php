<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembershipCodesTable extends Migration
{
    public function up()
    {
        Schema::create('membership_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 8)->unique();
            $table->boolean('used')->default(false);
            $table->foreignId('used_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('membership_codes');
    }

};
