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
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sent_by')->nullable(); // Admin who sent the SMS
            $table->string('recipient_type')->default('single'); // single, bulk, all_users, all_members, role_based
            $table->json('recipients'); // Array of mobile numbers or user IDs
            $table->text('message');
            $table->string('sender_name')->nullable();
            $table->integer('total_recipients')->default(1);
            $table->integer('successful_sends')->default(0);
            $table->integer('failed_sends')->default(0);
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->string('status')->default('pending'); // pending, sending, completed, failed
            $table->json('semaphore_response')->nullable(); // Store API response
            $table->json('message_ids')->nullable(); // Store Semaphore message IDs
            $table->string('campaign_name')->nullable(); // Optional campaign identifier
            $table->text('notes')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->foreign('sent_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['recipient_type', 'status']);
            $table->index('sent_at');
            $table->index('sent_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sms_logs');
    }
};
