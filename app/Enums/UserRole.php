<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super-admin';
    case AcademicAdmin = 'academic-admin';
    case ExaminationCell = 'examination-cell';
    case PlacementCell = 'placement-cell';
    case ContentManager = 'content-manager';
    case Student = 'student';
}
