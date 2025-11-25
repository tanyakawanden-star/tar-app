<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('travel_requests', function (Blueprint $table) {
            $table->string('tar_number')->unique()->after('id');

            $table->string('status')->default('DRAFT')->after('tar_number');

            // who is requester
            $table->unsignedBigInteger('requester_id')->nullable()->after('status');

            // approval timestamps
            $table->timestamp('approved_by_manager_at')->nullable()->after('requester_id');
            $table->timestamp('approved_by_finance_at')->nullable()->after('approved_by_manager_at');
            $table->timestamp('approved_by_director_at')->nullable()->after('approved_by_finance_at');
            $table->timestamp('rejected_at')->nullable()->after('approved_by_director_at');
            $table->text('rejected_reason')->nullable()->after('rejected_at');

            // type of travel – needed for domestic / overseas rules
            $table->enum('travel_type', ['domestic', 'overseas'])->default('domestic')->after('rejected_reason');
        });
    }

    public function down(): void
    {
        Schema::table('travel_requests', function (Blueprint $table) {
            $table->dropColumn([
                'tar_number',
                'status',
                'requester_id',
                'approved_by_manager_at',
                'approved_by_finance_at',
                'approved_by_director_at',
                'rejected_at',
                'rejected_reason',
                'travel_type',
            ]);
        });
    }
};
