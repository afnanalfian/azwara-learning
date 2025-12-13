<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            $table->longText('option_text');
            $table->boolean('is_correct')->default(false);
            $table->string('image')->nullable();
            $table->integer('order')->default(1); // urutan A-D atau pernyataan urutan 1,2,3
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_options');
    }
};
