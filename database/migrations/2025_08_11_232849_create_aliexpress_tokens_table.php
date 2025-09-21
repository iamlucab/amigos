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
        Schema::create('aliexpress_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // Link to user if needed
            $table->string('access_token');
            $table->string('refresh_token')->nullable();
            $table->string('token_type')->default('Bearer');
            $table->integer('expires_in')->nullable(); // Token expiration time in seconds
            $table->timestamp('expires_at')->nullable(); // When the token expires
            $table->text('scope')->nullable(); // Permissions granted
            $table->string('aliexpress_user_id')->nullable(); // AliExpress user ID
            $table->string('aliexpress_login_id')->nullable(); // AliExpress login ID
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('aliexpress_user_id');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aliexpress_tokens');
    }
};
