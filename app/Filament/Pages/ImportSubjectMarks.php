<?php

namespace App\Filament\Pages;

use App\Models\ExamSession;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SubjectMark;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ImportSubjectMarks extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-tray';

    protected static ?string $navigationGroup = 'Examination';

    protected static ?string $navigationLabel = 'Import subject marks';

    // Ensure exam sessions appear before this page in the Examination menu.
    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Import subject marks';

    protected static string $view = 'filament.pages.import-subject-marks';

    public ?int $exam_session_id = null;

    public ?string $payload = null;

    public bool $dry_run = false;

    public ?array $summary = null;

    public function mount(): void
    {
        $this->form->fill([
            'exam_session_id' => $this->exam_session_id,
            'payload' => $this->payload,
            'dry_run' => $this->dry_run,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('exam_session_id')
                    ->label('Exam session')
                    ->options(
                        ExamSession::query()
                            ->orderByDesc('start_date')
                            ->pluck('name', 'id')
                    )
                    ->searchable()
                    ->required(),
                Textarea::make('payload')
                    ->label('Paste tab- or comma-separated data')
                    ->rows(12)
                    ->placeholder("enrollment_id\tsubject_code\tmarks\tabsent\nIVIMT-2026-0001\tMATH101\t85\t0\nIVIMT-2026-0002\tMATH101\t72\t0")
                    ->required(),
                Toggle::make('dry_run')
                    ->label('Dry run (validate only, do not save)')
                    ->default(false),
            ])
            ->statePath('data');
    }

    public array $data = [];

    public function submit(): void
    {
        $state = $this->form->getState();

        $this->exam_session_id = (int) ($state['exam_session_id'] ?? 0);
        $this->payload = $state['payload'] ?? '';
        $this->dry_run = (bool) ($state['dry_run'] ?? false);

        $session = ExamSession::find($this->exam_session_id);
        if (! $session) {
            Notification::make()
                ->title('Selected exam session not found.')
                ->danger()
                ->send();

            return;
        }

        $lines = preg_split('/\R/u', (string) $this->payload) ?: [];

        $imported = 0;
        $skipped = 0;
        $errors = [];

        $sessionSubjectIds = $session->examSessionSubjects()
            ->pluck('subject_id')
            ->all();

        $subjectsByCode = Subject::query()
            ->whereIn('id', $sessionSubjectIds)
            ->get()
            ->keyBy(fn (Subject $subject): string => strtoupper(trim((string) $subject->code)));

        foreach ($lines as $index => $rawLine) {
            $lineNumber = $index + 1;
            $line = trim($rawLine);

            if ($line === '') {
                $skipped++;

                continue;
            }

            $columns = preg_split('/[\t,]\s*/', $line);
            if (! $columns || count($columns) < 4) {
                $errors[] = "Line {$lineNumber}: expected 4 columns, got ".(is_array($columns) ? count($columns) : 0).'.';
                $skipped++;

                continue;
            }

            [$enrollmentId, $subjectCode, $marksRaw, $absentRaw] = array_map('trim', array_slice($columns, 0, 4));
            $subjectCode = strtoupper(trim((string) $subjectCode));

            $student = Student::where('enrollment_id', $enrollmentId)->first();
            if (! $student) {
                $errors[] = "Line {$lineNumber}: student not found for enrollment ID '{$enrollmentId}'.";
                $skipped++;

                continue;
            }

            $subject = $subjectsByCode[$subjectCode] ?? null;
            if (! $subject) {
                $errors[] = "Line {$lineNumber}: subject with code '{$subjectCode}' is not part of this exam session.";
                $skipped++;

                continue;
            }

            if ($subject->course_id !== $student->course_id) {
                $errors[] = "Line {$lineNumber}: subject '{$subjectCode}' does not belong to the student's course.";
                $skipped++;

                continue;
            }

            if (! $student->examForms()
                ->where('exam_session_id', $session->id)
                ->where('status', 'approved')
                ->exists()) {
                $errors[] = "Line {$lineNumber}: student has no approved exam form for this exam session.";
                $skipped++;

                continue;
            }

            $isAbsent = in_array(strtolower($absentRaw), ['1', 'true', 'yes', 'y'], true);

            $marksObtained = null;
            if (! $isAbsent) {
                if (! is_numeric($marksRaw)) {
                    $errors[] = "Line {$lineNumber}: marks '{$marksRaw}' is not numeric.";
                    $skipped++;

                    continue;
                }

                $marksObtained = (float) $marksRaw;

                if ($marksObtained < 0) {
                    $errors[] = "Line {$lineNumber}: marks cannot be negative.";
                    $skipped++;

                    continue;
                }

                if (isset($subject->max_marks) && $subject->max_marks !== null && $marksObtained > (float) $subject->max_marks) {
                    $errors[] = "Line {$lineNumber}: marks {$marksObtained} exceed subject max ({$subject->max_marks}).";
                    $skipped++;

                    continue;
                }
            }

            if ($this->dry_run) {
                $imported++;

                continue;
            }

            SubjectMark::updateOrCreate(
                [
                    'exam_session_id' => $session->id,
                    'student_id' => $student->id,
                    'subject_id' => $subject->id,
                ],
                [
                    'marks_obtained' => $isAbsent ? null : $marksObtained,
                    'is_absent' => $isAbsent,
                ]
            );

            $imported++;
        }

        $this->summary = [
            'imported' => $imported,
            'skipped' => $skipped,
            'errors' => $errors,
        ];

        $title = $this->dry_run
            ? "Dry run complete: {$imported} valid, {$skipped} skipped."
            : "Import complete: {$imported} rows saved, {$skipped} skipped.";

        Notification::make()
            ->title($title)
            ->success()
            ->send();
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return $user->can('subject-mark.create') || $user->can('subject-mark.update');
    }
}
