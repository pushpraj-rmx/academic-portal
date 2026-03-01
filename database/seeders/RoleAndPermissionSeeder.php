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
                'testimonial.view', 'testimonial.create', 'testimonial.update', 'testimonial.delete',
                'hero-slide.view', 'hero-slide.create', 'hero-slide.update', 'hero-slide.delete',
                'stat-counter.view', 'stat-counter.create', 'stat-counter.update', 'stat-counter.delete',
                'certification.view', 'certification.create', 'certification.update', 'certification.delete',
                'faq.view', 'faq.create', 'faq.update', 'faq.delete',
                'downloadable-form.view', 'downloadable-form.create', 'downloadable-form.update', 'downloadable-form.delete',
                'contact-submission.view', 'contact-submission.create', 'contact-submission.update', 'contact-submission.delete',
                'nav-item.view', 'nav-item.create', 'nav-item.update', 'nav-item.delete',
            ],
            'Academic' => [
                'category.view', 'category.create', 'category.update', 'category.delete',
                'course.view', 'course.create', 'course.update', 'course.delete',
                'specialization.view', 'specialization.create', 'specialization.update', 'specialization.delete',
                'syllabus.view', 'syllabus.create', 'syllabus.update', 'syllabus.delete',
                'subject.view', 'subject.create', 'subject.update', 'subject.delete',
            ],
            'Examination' => [
                'exam-session.view', 'exam-session.create', 'exam-session.update', 'exam-session.delete',
                'exam-form.view', 'exam-form.create', 'exam-form.update', 'exam-form.delete',
                'subject-mark.view', 'subject-mark.create', 'subject-mark.update',
                'grading-rule.view', 'grading-rule.create', 'grading-rule.update', 'grading-rule.delete',
            ],
            'Placement' => [
                'recruiter.view', 'recruiter.create', 'recruiter.update', 'recruiter.delete',
                'placement.view', 'placement.create', 'placement.update', 'placement.delete',
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
            'subject.view', 'subject.create', 'subject.update', 'subject.delete',
            'student.view', 'student.create', 'student.update', 'student.delete', 'student.verify',
            'student-document.view', 'student-document.create', 'student-document.delete',
            'page.view', 'announcement.view', 'announcement.create', 'announcement.update', 'announcement.delete',
            'testimonial.view', 'testimonial.create', 'testimonial.update', 'testimonial.delete',
            'hero-slide.view', 'hero-slide.create', 'hero-slide.update', 'hero-slide.delete',
            'stat-counter.view', 'stat-counter.create', 'stat-counter.update', 'stat-counter.delete',
            'certification.view', 'certification.create', 'certification.update', 'certification.delete',
            'faq.view', 'faq.create', 'faq.update', 'faq.delete',
            'downloadable-form.view', 'downloadable-form.create', 'downloadable-form.update', 'downloadable-form.delete',
            'contact-submission.view', 'contact-submission.update', 'contact-submission.delete',
        ]);

        $examinationCell = Role::findByName(UserRole::ExaminationCell->value, $guardName);
        $examinationCell->syncPermissions([
            'exam-session.view', 'exam-session.create', 'exam-session.update', 'exam-session.delete',
            'exam-form.view', 'exam-form.create', 'exam-form.update', 'exam-form.delete',
            'subject-mark.view', 'subject-mark.create', 'subject-mark.update',
            'grading-rule.view', 'grading-rule.create', 'grading-rule.update', 'grading-rule.delete',
            'student.view', 'student.verify',
        ]);

        $placementCell = Role::findByName(UserRole::PlacementCell->value, $guardName);
        $placementCell->syncPermissions([
            'recruiter.view', 'recruiter.create', 'recruiter.update', 'recruiter.delete',
            'placement.view', 'placement.create', 'placement.update', 'placement.delete',
        ]);

        $contentManager = Role::findByName(UserRole::ContentManager->value, $guardName);
        $contentManager->syncPermissions([
            'page.view', 'page.create', 'page.update', 'page.delete',
            'announcement.view', 'announcement.create', 'announcement.update', 'announcement.delete',
            'testimonial.view', 'testimonial.create', 'testimonial.update', 'testimonial.delete',
            'hero-slide.view', 'hero-slide.create', 'hero-slide.update', 'hero-slide.delete',
            'stat-counter.view', 'stat-counter.create', 'stat-counter.update', 'stat-counter.delete',
            'certification.view', 'certification.create', 'certification.update', 'certification.delete',
            'faq.view', 'faq.create', 'faq.update', 'faq.delete',
            'downloadable-form.view', 'downloadable-form.create', 'downloadable-form.update', 'downloadable-form.delete',
            'contact-submission.view', 'contact-submission.update', 'contact-submission.delete',
            'nav-item.view', 'nav-item.create', 'nav-item.update', 'nav-item.delete',
        ]);

        $student = Role::findByName(UserRole::Student->value, $guardName);
        $student->syncPermissions([]);
    }
}
