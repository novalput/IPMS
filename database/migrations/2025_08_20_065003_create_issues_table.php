<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('issues', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('period_id');
            $table->string('name', 255)->nullable();
            $table->string('location', 255)->nullable();
            $table->string('equipment', 255)->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['ongoing','done'])->nullable();
            $table->timestamps();

            $table->foreign('period_id')->references('id')->on('periods')->onDelete('cascade');
            $table->index(['period_id','status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('issues');
    }
};
