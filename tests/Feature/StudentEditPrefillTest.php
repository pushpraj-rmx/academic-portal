<?php

use App\Filament\Resources\StudentResource\Pages\EditStudent;
use App\Models\Student;
use App\Models\User;

test('edit student pre-fills name/email from related user', function () {
    $user = User::factory()->create([
        'name' => 'Rahul Parent Name',
        'email' => 'rahul.parent@example.com',
    ]);

    $student = Student::factory()->create([
        'user_id' => $user->id,
        'father_name' => 'Father X',
        'mother_name' => 'Mother Y',
    ]);

    /** @var EditStudent $page */
    $page = app(EditStudent::class);
    $page->record = $student;

    $method = new ReflectionMethod($page, 'mutateFormDataBeforeFill');
    $method->setAccessible(true);

    $data = $student->attributesToArray();
    $result = $method->invoke($page, $data);

    expect($result['name'])->toBe('Rahul Parent Name');
    expect($result['email'])->toBe('rahul.parent@example.com');
});
