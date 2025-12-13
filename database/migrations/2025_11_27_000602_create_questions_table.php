<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained('question_materials')->cascadeOnDelete();
            $table->enum('type', ['mcq', 'mcma', 'truefalse']);
            $table->longText('question_text');
            $table->string('image')->nullable();
            $table->longText('explanation')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
