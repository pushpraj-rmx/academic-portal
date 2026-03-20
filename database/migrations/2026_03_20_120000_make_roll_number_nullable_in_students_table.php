<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            // MySQL: allow NULL so multiple students can exist without a roll number yet.
            DB::statement('ALTER TABLE students MODIFY roll_number VARCHAR(255) NULL');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            // Revert back to NOT NULL (empty string may still be allowed by your app).
            DB::statement('ALTER TABLE students MODIFY roll_number VARCHAR(255) NOT NULL');
        }
    }
};
