<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('meeting_post_test_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_test_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->boolean('is_submitted')->default(false);
            $table->timestamps();
            $table->unique(['post_test_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_post_test_attempts');
    }
};
