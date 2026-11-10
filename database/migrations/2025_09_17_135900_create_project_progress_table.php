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
        Schema::create('project_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->unique()->constrained('periods')->onDelete('cascade');
            $table->decimal('progress_percentage', 5, 2)->default(0.00)->comment('Progress aktual dalam persen (0-100)');
            $table->decimal('weight', 3, 2)->default(0.00)->comment('Bobot periode (0-1, misal 0.7 untuk 70%)');
            $table->text('notes')->nullable()->comment('Catatan progress');
            $table->timestamps();
            
            $table->index(['period_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_progress');
    }
};
