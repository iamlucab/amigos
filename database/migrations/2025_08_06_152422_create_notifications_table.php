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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // membership_code_request, cashin_request, loan_request, etc.
            $table->text('message');
            $table->boolean('read')->default(false);
            $table->string('notifiable_type')->nullable(); // For linking to specific records
            $table->unsignedBigInteger('notifiable_id')->nullable(); // For linking to specific records
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};
