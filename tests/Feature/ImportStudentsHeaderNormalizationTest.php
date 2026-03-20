<?php

use App\Filament\Pages\ImportStudents;

test('import students header normalization trims BOM and whitespace', function () {
    $page = app(ImportStudents::class);

    $method = new ReflectionMethod($page, 'normalizeHeaderKey');
    $method->setAccessible(true);

    expect($method->invoke($page, "\u{FEFF}name"))->toBe('name');
    expect($method->invoke($page, '  Email  '))->toBe('email');
    expect($method->invoke($page, 'roll-number'))->toBe('roll_number');
    expect($method->invoke($page, 'date of birth'))->toBe('date_of_birth');
});
