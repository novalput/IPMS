<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('procurement_materials', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('period_id');
            $table->string('material_name', 255);
            $table->string('po_number')->nullable();
            $table->date('po_issued')->nullable();
            $table->date('submit_tpg')->nullable();
            $table->date('approve_tpg')->nullable();
            $table->date('manufacture')->nullable();
            $table->date('fat')->nullable();
            $table->date('delivery')->nullable();
            $table->date('material_on_site')->nullable();
            $table->timestamps();

            $table->foreign('period_id')->references('id')->on('periods')->onDelete('cascade');
            $table->index(['period_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('procurement_materials');
    }
};
