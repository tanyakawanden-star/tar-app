<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpensesTable extends Migration
{
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('travel_request_id')->constrained('travel_requests')->onDelete('cascade');
            $table->decimal('accommodation', 15, 2)->default(0);
            $table->decimal('meals', 15, 2)->default(0);
            $table->decimal('transport', 15, 2)->default(0);
            $table->decimal('airfare', 15, 2)->default(0);
            $table->decimal('others', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('expenses');
    }
}
