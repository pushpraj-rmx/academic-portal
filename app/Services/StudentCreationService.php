<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class StudentCreationService
{
    /**
     * Create a User + Student pair from admin-provided data.
     *
     * @param  array<string, mixed>  $data
     */
    public function createFromArray(array $data): Student
    {
        return DB::transaction(function () use ($data): Student {
            $name = $data['name'] ?? null;
            $email = $data['email'] ?? null;
            $password = $data['password'] ?? null;

            if (! $name || ! $email || ! $password) {
                throw ValidationException::withMessages([
                    'email' => 'Name, email, and password are required to create a student user.',
                ]);
            }

            /** @var User $user */
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
            ]);

            $user->assignRole(UserRole::Student->value);

            $studentData = $data;
            unset($studentData['name'], $studentData['email'], $studentData['password']);

            $attempts = 0;

            while ($attempts < 2) {
                try {
                    if (empty($studentData['enrollment_id'] ?? null)) {
                        $studentData['enrollment_id'] = Student::generateEnrollmentId();
                    }

                    $studentData['user_id'] = $user->id;

                    /** @var Student $student */
                    $student = Student::create($studentData);

                    return $student;
                } catch (QueryException $e) {
                    $message = $e->getMessage();

                    $isEnrollmentIdCollision = Str::contains($message, 'enrollment_id') && (
                        Str::contains($message, 'Duplicate') ||
                        Str::contains(strtolower($message), 'unique') ||
                        Str::contains(strtolower($message), '23000')
                    );

                    // Retry only when the unique enrollment_id was collided.
                    if ($attempts === 0 && $isEnrollmentIdCollision) {
                        $studentData['enrollment_id'] = null;
                        $attempts++;

                        continue;
                    }

                    // If roll_number is empty but DB is still non-nullable, this will surface here.
                    if (Str::contains(strtolower($message), 'roll_number') && Str::contains(strtolower($message), 'null')) {
                        throw ValidationException::withMessages([
                            'roll_number' => 'Roll number is optional in the UI, but the database still requires it. Run the migration to make `students.roll_number` nullable.',
                        ]);
                    }

                    throw ValidationException::withMessages([
                        'enrollment_id' => 'Unable to create student. Please check the provided fields and try again.',
                    ]);
                }
            }

            throw ValidationException::withMessages([
                'enrollment_id' => 'Unable to create student after multiple attempts.',
            ]);
        });
    }
}
