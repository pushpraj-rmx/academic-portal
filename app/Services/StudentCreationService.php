<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
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
                    if ($attempts === 0) {
                        $studentData['enrollment_id'] = null;
                        $attempts++;

                        continue;
                    }

                    throw ValidationException::withMessages([
                        'enrollment_id' => 'Could not create student. Check that enrollment ID and roll number are unique for the course.',
                    ]);
                }
            }

            throw ValidationException::withMessages([
                'enrollment_id' => 'Unable to create student after multiple attempts.',
            ]);
        });
    }
}

