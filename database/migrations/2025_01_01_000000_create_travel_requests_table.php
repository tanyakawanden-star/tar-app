<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('travel_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            $table->string('tar_number')->unique();

            $table->string('destination_city');
            $table->string('destination_country')->nullable();
            $table->boolean('is_overseas')->default(false);

            $table->text('purpose')->nullable();

            $table->date('departure_date');
            $table->date('return_date');

            $table->decimal('estimated_transport_cost', 15, 2)->default(0);
            $table->decimal('estimated_hotel_cost', 15, 2)->default(0);
            $table->decimal('estimated_meals_cost', 15, 2)->default(0);
            $table->decimal('estimated_other_cost', 15, 2)->default(0);

            $table->string('status')->default('SUBMITTED');

            $table->timestamp('approved_by_manager_at')->nullable();
            $table->timestamp('approved_by_finance_at')->nullable();
            $table->timestamp('approved_by_director_at')->nullable();

            $table->timestamp('rejected_at')->nullable();
            $table->text('rejected_reason')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('travel_requests');
    }
};
