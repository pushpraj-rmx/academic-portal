<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Livewire\EventBus;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->ensureStorageFrameworkDirectoriesExist();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function (User $user, string $ability) {
            return $user->hasRole(UserRole::SuperAdmin->value) ? true : null;
        });

        $this->normalizeLivewireEmptyFileUploadSnapshot();
    }

    /**
     * Fix "No synthesizer found for key: """ when a Livewire snapshot contains
     * a synthetic tuple with empty-string synth key (e.g. empty file upload).
     */
    protected function normalizeLivewireEmptyFileUploadSnapshot(): void
    {
        app(EventBus::class)->on('request', function (array $requestPayload) {
            return function ($forward) {
                if (! is_array($forward)) {
                    return $forward;
                }
                foreach ($forward as $index => $componentPayload) {
                    if (isset($componentPayload['snapshot']) && is_string($componentPayload['snapshot'])) {
                        $snapshot = json_decode($componentPayload['snapshot'], true);
                        if (is_array($snapshot) && array_key_exists('data', $snapshot)) {
                            $snapshot['data'] = $this->normalizeLivewireSnapshotData($snapshot['data']);
                            $forward[$index]['snapshot'] = json_encode($snapshot);
                        }
                    }
                    if (isset($componentPayload['updates']) && is_array($componentPayload['updates'])) {
                        $forward[$index]['updates'] = $this->normalizeLivewireSnapshotData($componentPayload['updates']);
                    }
                }

                return $forward;
            };
        });
    }

    /**
     * Recursively replace synthetic tuples with empty-string synth key by their value.
     */
    private function normalizeLivewireSnapshotData(mixed $data): mixed
    {
        if (is_array($data) && count($data) === 2 && isset($data[1]['s']) && $data[1]['s'] === '') {
            return $this->normalizeLivewireSnapshotData($data[0]);
        }
        if (is_array($data)) {
            $result = [];
            foreach ($data as $key => $value) {
                $result[$key] = $this->normalizeLivewireSnapshotData($value);
            }

            return $result;
        }

        return $data;
    }

    /**
     * Ensure storage/framework subdirectories exist so the view compiler and others get a valid path.
     * Required when deploying via zip (these dirs are excluded from the archive).
     */
    private function ensureStorageFrameworkDirectoriesExist(): void
    {
        $base = storage_path('framework');
        $dirs = ['cache/data', 'sessions', 'views'];
        foreach ($dirs as $dir) {
            $path = $base.DIRECTORY_SEPARATOR.$dir;
            if (! is_dir($path)) {
                mkdir($path, 0755, true);
            }
        }
    }
}
