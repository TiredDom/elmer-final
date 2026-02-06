<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budget_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('month');
            $table->integer('year');
            $table->decimal('budget_limit', 10, 2);
            $table->decimal('total_approved', 10, 2);
            $table->decimal('remaining_budget', 10, 2);
            $table->integer('total_expenses_count')->default(0);
            $table->integer('approved_count')->default(0);
            $table->integer('rejected_count')->default(0);
            $table->timestamp('reset_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_histories');
    }
};

