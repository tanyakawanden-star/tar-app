<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('travel_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('travel_request_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('action'); // e.g. created, submitted, approved_manager
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('travel_request_id')->references('id')->on('travel_requests')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('travel_logs');
    }
};
