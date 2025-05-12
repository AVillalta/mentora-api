<?php

namespace App\Jobs;

use App\Models\Enrollment\Enrollment;
use App\Models\Semester\Semester;
use App\Services\Enrollment\FinalGradeService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CalculateFinalGradeJob implements ShouldQueue
{
    use Dispatchable , InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $enrollmentId)
    {
    }

    public function handle(FinalGradeService $finalGradeService): void
    {
        // Find the enrollment by ID
        $enrollment = Enrollment::find($this->enrollmentId);

        if ($enrollment) {
            // Calculate the final grade for the enrollment
            $finalGradeService->calculateFinalGrade($enrollment);
        } else {
            // Log an error if the enrollment is not found
            Log::error("Enrollment not found: {$this->enrollmentId}");
        }
    }
}
