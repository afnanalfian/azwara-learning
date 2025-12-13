<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('course_teacher', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['course_id', 'teacher_id']);
        });

        // Hapus kolom teacher_id lama
        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'teacher_id')) {
                $table->dropColumn('teacher_id');
            }
        });
    }

    public function down()
    {
        Schema::dropIfExists('course_teacher');
    }
};
