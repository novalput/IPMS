<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('procurement_services', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('period_id');
            $table->string('service_name', 255);
            $table->string('po_number')->nullable();
            $table->date('po_issued')->nullable();
            $table->date('mobilization')->nullable();
            $table->date('field_work')->nullable();
            $table->date('reporting')->nullable();
            $table->timestamps();

            $table->foreign('period_id')->references('id')->on('periods')->onDelete('cascade');
            $table->index(['period_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('procurement_services');
    }
};
