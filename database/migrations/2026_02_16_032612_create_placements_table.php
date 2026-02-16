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
        Schema::create('placements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->restrictOnDelete();
            $table->foreignId('recruiter_id')->constrained('recruiters')->restrictOnDelete();
            $table->string('academic_year');
            $table->string('placement_type');
            $table->string('designation')->nullable();
            $table->decimal('package_amount', 10, 2)->nullable();
            $table->string('status');
            $table->date('offer_date')->nullable();
            $table->timestamps();
        });

        Schema::table('placements', function (Blueprint $table) {
            $table->unique(['student_id', 'recruiter_id', 'academic_year']);
            $table->index('academic_year');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('placements');
    }
};
