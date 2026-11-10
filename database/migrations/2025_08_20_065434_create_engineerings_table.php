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
        Schema::create('engineerings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('period_id');
            $table->string('kategori', 100)->nullable();
            $table->string('subkategori', 100)->nullable();
            $table->string('sub', 100)->nullable();
            $table->string('doc_part1', 50);
            $table->string('doc_part2', 50);
            $table->string('doc_part3', 50);
            $table->string('doc_part4', 50);
            $table->string('doc_part5', 50);
            $table->string('doc_part6', 50);
            $table->string('document_name', 255);
            $table->string('approval_status', 20)->default('ongoing'); // ongoing/done/delayed
            $table->timestamps();

            $table->foreign('period_id')->references('id')->on('periods')->onDelete('cascade');
            $table->index(['period_id','approval_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('engineerings');
    }
};
