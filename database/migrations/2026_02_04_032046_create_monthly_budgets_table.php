<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_budgets', function (Blueprint $table) {
            $table->id();
            $table->integer('month');
            $table->integer('year');
            $table->decimal('budget_limit', 10, 2)->default(10000.00);
            $table->decimal('total_approved', 10, 2)->default(0.00);
            $table->timestamps();
            $table->unique(['month', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_budgets');
    }
};
