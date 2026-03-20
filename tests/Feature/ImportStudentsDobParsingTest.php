<?php

use App\Filament\Pages\ImportStudents;

test('import students parses DOB from YYYY-MM-DD string', function () {
    $page = app(ImportStudents::class);

    $method = new ReflectionMethod($page, 'parseDobValue');
    $method->setAccessible(true);

    expect($method->invoke($page, '2003-05-14'))->toBe('2003-05-14');
});

test('import students parses DOB from excel serial', function () {
    $page = app(ImportStudents::class);

    $method = new ReflectionMethod($page, 'parseDobValue');
    $method->setAccessible(true);

    $excelSerial = \PhpOffice\PhpSpreadsheet\Shared\Date::dateTimeToExcel(new DateTime('2002-11-02'));

    expect($method->invoke($page, $excelSerial))->toBe('2002-11-02');
});

test('import students parses DOB from dd slash mm slash yyyy string', function () {
    $page = app(ImportStudents::class);

    $method = new ReflectionMethod($page, 'parseDobValue');
    $method->setAccessible(true);

    expect($method->invoke($page, '14/05/2003'))->toBe('2003-05-14');
});

test('import students parses DOB from dd dash mm dash yyyy string', function () {
    $page = app(ImportStudents::class);

    $method = new ReflectionMethod($page, 'parseDobValue');
    $method->setAccessible(true);

    expect($method->invoke($page, '02-11-2002'))->toBe('2002-11-02');
});
