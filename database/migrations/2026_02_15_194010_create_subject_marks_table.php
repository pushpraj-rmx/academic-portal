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
        Schema::create('subject_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_session_id')->constrained('exam_sessions')->restrictOnDelete();
            $table->foreignId('student_id')->constrained('students')->restrictOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->restrictOnDelete();
            $table->decimal('marks_obtained', 8, 2)->nullable();
            $table->boolean('is_absent')->default(false);
            $table->timestamps();
        });

        Schema::table('subject_marks', function (Blueprint $table) {
            $table->unique(['exam_session_id', 'student_id', 'subject_id']);
            $table->index(['student_id', 'exam_session_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_marks');
    }
};
