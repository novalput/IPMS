<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('period_id');
            $table->string('scope', 10)->nullable();
            $table->string('location', 255)->nullable();
            $table->text('achievement_this_week')->nullable();
            $table->decimal('progress', 5, 2)->nullable();
            $table->text('plan_next_week')->nullable();
            $table->enum('status', ['ongoing', 'done', 'delayed'])->nullable();
            $table->timestamps();

            $table->foreign('period_id')->references('id')->on('periods')->onDelete('cascade');
            $table->index(['period_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
