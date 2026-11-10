<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('trackings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('period_id');
            $table->date('issued_date');
            $table->string('sender', 255);
            $table->string('recipient', 255);
            $table->string('subject', 255);
            //perbaikan - enum status yang lebih sederhana
            $table->enum('status', ['submission','approved'])->default('submission');
            $table->string('reviewer', 255);
            $table->timestamps();
            
            $table->foreign('period_id')->references('id')->on('periods')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('trackings');
    }
};
