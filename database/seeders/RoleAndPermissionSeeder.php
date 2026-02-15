<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $guardName = config('auth.defaults.guard');

        $permissionsByModule = [
            'Users' => ['user.view', 'user.create', 'user.update', 'user.delete'],
            'Roles' => ['role.view', 'role.create', 'role.update', 'role.delete'],
            'CMS' => [
                'page.view', 'page.create', 'page.update', 'page.delete',
                'announcement.view', 'announcement.create', 'announcement.update', 'announcement.delete',
            ],
            'Academic' => [
                'category.view', 'category.create', 'category.update', 'category.delete',
                'course.view', 'course.create', 'course.update', 'course.delete',
                'specialization.view', 'specialization.create', 'specialization.update', 'specialization.delete',
                'syllabus.view', 'syllabus.create', 'syllabus.update', 'syllabus.delete',
            ],
            'Examination' => [
                'exam-session.view', 'exam-session.create', 'exam-session.update', 'exam-session.delete',
                'result.view', 'result.create', 'result.update', 'result.publish',
            ],
            'Placement' => [
                'recruiter.view', 'recruiter.create', 'recruiter.update', 'recruiter.delete',
                'placement.view', 'placement.create', 'placement.update',
            ],
            'Student' => [
                'student.view', 'student.create', 'student.update', 'student.delete', 'student.verify',
                'student-document.view', 'student-document.create', 'student-document.delete',
            ],
        ];

        $allPermissions = [];
        foreach ($permissionsByModule as $permissions) {
            foreach ($permissions as $name) {
                $allPermissions[] = Permission::firstOrCreate(['name' => $name, 'guard_name' => $guardName]);
            }
        }

        $allPermissionNames = array_map(fn (Permission $p) => $p->name, $allPermissions);

        foreach (UserRole::cases() as $roleCase) {
            Role::firstOrCreate(
                ['name' => $roleCase->value, 'guard_name' => $guardName]
            );
        }

        $superAdmin = Role::findByName(UserRole::SuperAdmin->value, $guardName);
        $superAdmin->syncPermissions($allPermissionNames);

        $academicAdmin = Role::findByName(UserRole::AcademicAdmin->value, $guardName);
        $academicAdmin->syncPermissions([
            'category.view', 'category.create', 'category.update', 'category.delete',
            'course.view', 'course.create', 'course.update', 'course.delete',
            'specialization.view', 'specialization.create', 'specialization.update', 'specialization.delete',
            'syllabus.view', 'syllabus.create', 'syllabus.update', 'syllabus.delete',
            'student.view', 'student.create', 'student.update', 'student.delete', 'student.verify',
            'student-document.view', 'student-document.create', 'student-document.delete',
            'page.view', 'announcement.view', 'announcement.create', 'announcement.update', 'announcement.delete',
        ]);

        $examinationCell = Role::findByName(UserRole::ExaminationCell->value, $guardName);
        $examinationCell->syncPermissions([
            'exam-session.view', 'exam-session.create', 'exam-session.update', 'exam-session.delete',
            'result.view', 'result.create', 'result.update', 'result.publish',
            'student.view', 'student.verify',
        ]);

        $placementCell = Role::findByName(UserRole::PlacementCell->value, $guardName);
        $placementCell->syncPermissions([
            'recruiter.view', 'recruiter.create', 'recruiter.update', 'recruiter.delete',
            'placement.view', 'placement.create', 'placement.update',
        ]);

        $contentManager = Role::findByName(UserRole::ContentManager->value, $guardName);
        $contentManager->syncPermissions([
            'page.view', 'page.create', 'page.update', 'page.delete',
            'announcement.view', 'announcement.create', 'announcement.update', 'announcement.delete',
        ]);

        $student = Role::findByName(UserRole::Student->value, $guardName);
        $student->syncPermissions([]);
    }
}
