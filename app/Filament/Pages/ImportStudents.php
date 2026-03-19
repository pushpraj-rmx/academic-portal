<?php

namespace App\Filament\Pages;

use App\Imports\StudentsArrayImport;
use App\Models\Course;
use App\Services\StudentCreationService;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ImportStudents extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-tray';

    protected static ?string $navigationGroup = 'Data Import';

    protected static ?string $navigationLabel = 'Import students';

    protected static ?string $title = 'Import students';

    protected static string $view = 'filament.pages.import-students';

    public ?string $file = null;

    public ?int $default_course_id = null;

    public bool $dry_run = true;

    /** @var array<int, array<string, mixed>>|null */
    public ?array $previewRows = null;

    /** @var array<int, array<string>>|null */
    public ?array $previewErrors = null;

    public ?array $summary = null;

    public function mount(): void
    {
        $this->form->fill([
            'file' => $this->file,
            'default_course_id' => $this->default_course_id,
            'dry_run' => $this->dry_run,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('file')
                    ->label('Student Excel file')
                    ->disk('local')
                    ->directory('imports/students')
                    ->acceptedFileTypes([
                        'text/csv',
                        'text/plain',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    ])
                    ->required(),
                Select::make('default_course_id')
                    ->label('Default course (optional)')
                    ->options(Course::query()->orderBy('name')->pluck('name', 'id'))
                    ->searchable(),
                Toggle::make('dry_run')
                    ->label('Dry run (validate only, do not create records)')
                    ->default(true),
            ])
            ->statePath('data');
    }

    public array $data = [];

    public function submit(): void
    {
        $state = $this->form->getState();

        $this->file = $state['file'] ?? null;
        $this->default_course_id = $state['default_course_id'] ?? null;
        $this->dry_run = (bool) ($state['dry_run'] ?? true);

        $this->previewRows = [];
        $this->previewErrors = [];
        $this->summary = null;

        if (! $this->file) {
            Notification::make()
                ->title('Please upload an Excel/CSV file.')
                ->danger()
                ->send();

            return;
        }

        $path = storage_path('app/'.$this->file);

        if (! file_exists($path)) {
            Notification::make()
                ->title('Uploaded file not found on server.')
                ->danger()
                ->send();

            return;
        }

        $import = new StudentsArrayImport();

        try {
            Excel::import($import, $path);
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Unable to read Excel file.')
                ->body($e->getMessage())
                ->danger()
                ->send();

            return;
        }

        if ($import->rows === []) {
            Notification::make()
                ->title('The uploaded file appears to be empty.')
                ->warning()
                ->send();

            return;
        }

        $rows = $import->rows;
        $header = array_map(
            fn ($value) => Str::of((string) $value)->lower()->replace([' ', '-', '.'], '_')->value(),
            Arr::wrap($rows[0] ?? [])
        );

        $dataRows = array_slice($rows, 1);

        $validCount = 0;
        $invalidCount = 0;

        foreach ($dataRows as $index => $row) {
            $lineNumber = $index + 2; // account for header row
            $assoc = [];
            foreach ($header as $i => $key) {
                if ($key === '') {
                    continue;
                }
                $assoc[$key] = $row[$i] ?? null;
            }

            [$clean, $errors] = $this->validateStudentRow($assoc, $lineNumber);

            $this->previewRows[] = $clean;
            $this->previewErrors[] = $errors;

            if ($errors === []) {
                $validCount++;
            } else {
                $invalidCount++;
            }
        }

        $this->summary = [
            'valid' => $validCount,
            'invalid' => $invalidCount,
        ];

        Notification::make()
            ->title("Validation complete: {$validCount} valid, {$invalidCount} invalid.")
            ->success()
            ->send();
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array{0: array<string, mixed>, 1: array<int, string>}
     */
    protected function validateStudentRow(array $row, int $lineNumber): array
    {
        $errors = [];

        $name = trim((string) ($row['name'] ?? ''));
        $email = trim((string) ($row['email'] ?? ''));
        $rollNumber = trim((string) ($row['roll_number'] ?? ''));
        $dobRaw = $row['dob'] ?? $row['date_of_birth'] ?? null;
        $courseSlugOrName = trim((string) ($row['course_slug'] ?? $row['course'] ?? ''));

        if ($name === '') {
            $errors[] = "Line {$lineNumber}: name is required.";
        }

        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Line {$lineNumber}: valid email is required.";
        }

        $courseId = $this->resolveCourseId($courseSlugOrName);
        if (! $courseId && $this->default_course_id) {
            $courseId = (int) $this->default_course_id;
        }
        if (! $courseId) {
            $errors[] = "Line {$lineNumber}: course not found (by slug/name) and no default course selected.";
        }

        if ($rollNumber === '') {
            $errors[] = "Line {$lineNumber}: roll_number is required.";
        }

        $dob = null;
        if ($dobRaw !== null && $dobRaw !== '') {
            try {
                // Excel may give a DateTime or numeric serial; let Carbon handle strings.
                $dob = \Carbon\Carbon::parse($dobRaw)->toDateString();
            } catch (\Throwable $e) {
                $errors[] = "Line {$lineNumber}: invalid date of birth value.";
            }
        }

        // Basic DB-level uniqueness checks (best-effort; final guarantee is DB constraints)
        if ($email !== '' && \App\Models\User::where('email', $email)->exists()) {
            $errors[] = "Line {$lineNumber}: email '{$email}' is already in use.";
        }

        if ($courseId && $rollNumber !== '' && \App\Models\Student::where('course_id', $courseId)->where('roll_number', $rollNumber)->exists()) {
            $errors[] = "Line {$lineNumber}: roll number '{$rollNumber}' is already used for this course.";
        }

        $clean = [
            'name' => $name,
            'email' => $email,
            'roll_number' => $rollNumber,
            'course_id' => $courseId,
            'date_of_birth' => $dob,
            'phone' => trim((string) ($row['phone'] ?? '')),
            'alternate_phone' => trim((string) ($row['alternate_phone'] ?? '')),
            'verification_status' => 'pending',
            'notes' => null,
        ];

        return [$clean, $errors];
    }

    protected function resolveCourseId(?string $slugOrName): ?int
    {
        $value = trim((string) $slugOrName);
        if ($value === '') {
            return null;
        }

        $course = Course::query()
            ->where('slug', $value)
            ->orWhere('name', $value)
            ->first();

        return $course?->id;
    }

    public function confirmImport(): void
    {
        if ($this->summary === null || ($this->summary['valid'] ?? 0) === 0) {
            Notification::make()
                ->title('Nothing to import. Please validate a file first.')
                ->warning()
                ->send();

            return;
        }

        if (! $this->file) {
            Notification::make()
                ->title('Original file path missing; please re-upload and validate.')
                ->danger()
                ->send();

            return;
        }

        $path = storage_path('app/'.$this->file);

        if (! file_exists($path)) {
            Notification::make()
                ->title('Uploaded file not found on server.')
                ->danger()
                ->send();

            return;
        }

        $import = new StudentsArrayImport();

        try {
            Excel::import($import, $path);
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Unable to re-read Excel file.')
                ->body($e->getMessage())
                ->danger()
                ->send();

            return;
        }

        $rows = $import->rows;
        if ($rows === []) {
            Notification::make()
                ->title('The uploaded file appears to be empty.')
                ->warning()
                ->send();

            return;
        }

        $header = array_map(
            fn ($value) => Str::of((string) $value)->lower()->replace([' ', '-', '.'], '_')->value(),
            Arr::wrap($rows[0] ?? [])
        );
        $dataRows = array_slice($rows, 1);

        $created = 0;
        $failed = 0;
        $errors = [];

        /** @var StudentCreationService $service */
        $service = app(StudentCreationService::class);

        foreach ($dataRows as $index => $row) {
            $lineNumber = $index + 2;
            $assoc = [];
            foreach ($header as $i => $key) {
                if ($key === '') {
                    continue;
                }
                $assoc[$key] = $row[$i] ?? null;
            }

            [$clean, $rowErrors] = $this->validateStudentRow($assoc, $lineNumber);

            if ($rowErrors !== []) {
                $failed++;
                $errors = array_merge($errors, $rowErrors);

                continue;
            }

            try {
                $payload = [
                    'name' => $clean['name'],
                    'email' => $clean['email'],
                    // Simple default password policy for imports; admins can force reset later.
                    'password' => 'password',
                    'course_id' => $clean['course_id'],
                    'enrollment_id' => null,
                    'roll_number' => $clean['roll_number'],
                    'date_of_birth' => $clean['date_of_birth'],
                    'phone' => $clean['phone'],
                    'alternate_phone' => $clean['alternate_phone'],
                    'verification_status' => $clean['verification_status'],
                    'notes' => $clean['notes'],
                ];

                $service->createFromArray($payload);
                $created++;
            } catch (\Throwable $e) {
                $failed++;
                $errors[] = "Line {$lineNumber}: ".$e->getMessage();
            }
        }

        $this->summary = [
            'valid' => $this->summary['valid'] ?? 0,
            'invalid' => $this->summary['invalid'] ?? 0,
            'created' => $created,
            'failed' => $failed,
        ];

        $this->previewErrors = $errors === [] ? [] : [$errors];

        Notification::make()
            ->title("Import completed: {$created} created, {$failed} failed.")
            ->success()
            ->send();
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return $user->can('student.create');
    }
}

