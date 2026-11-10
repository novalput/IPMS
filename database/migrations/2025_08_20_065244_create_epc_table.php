<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('epcs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('period_id');
            $table->string('description', 255);
            $table->decimal('weight_factor', 5, 2);
            $table->decimal('cum_last_plan', 8, 2)->default(0);
            $table->decimal('cum_last_actual', 8, 2)->default(0);
            $table->decimal('cum_last_variance', 8, 2)->default(0);
            $table->decimal('this_plan', 8, 2)->default(0);
            $table->decimal('this_actual', 8, 2)->default(0);
            $table->decimal('cum_now_plan', 8, 2)->default(0);
            $table->decimal('cum_now_actual', 8, 2)->default(0);
            $table->decimal('cum_now_variance', 8, 2)->default(0);
            $table->enum('status', ['on schedule','delayed'])->default('on schedule');
            $table->boolean('is_total')->default(0);
            $table->timestamps();

            $table->foreign('period_id')->references('id')->on('periods')->onDelete('cascade');
            $table->index(['period_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('epcs');
    }
};
