<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->date('birthday');
            $table->string('mobile_number')->unique();
            $table->string('occupation')->nullable();
            $table->string('photo')->nullable();
            $table->enum('role', ['Admin', 'Staff', 'Member'])->default('Member');

            $table->unsignedBigInteger('sponsor_id')->nullable()->index();
            $table->foreign('sponsor_id')->references('id')->on('members')->onDelete('set null');

            $table->unsignedBigInteger('voter_id')->nullable()->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
