<?php

namespace App\Console\Commands;

use App\Helpers\Helper;
use App\Jobs\CalculateFinalGradeJob;
use App\Models\Enrollment\Enrollment;
use App\Models\Semester\Semester;
use Illuminate\Console\Command;

class ProcessFinalGradesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-final-grades-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch jobs to calculate final grades for enrollments in the current semester.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Obtener el semestre actual (donde end_date es hoy)
        $semester = Semester::where('end_date', today())->first();

        if (!$semester) {
            $this->info('No current semester found (end_date is not today).');
            return;
        }

        // Verify if semester has been processed
        if ($semester->is_processed) {
            $this->info("Semester {$semester->id} has already been processed.");
            return;
        }

        $this->info("Initiating: The process of calculating final grades for semester {$semester->id}...");

        $batchSize = 100; // Tamaño del bloque
        $totalProcessed = 0;

        Enrollment::where('semester_id', $semester->id)
            ->chunk($batchSize, function ($enrollments) use (&$totalProcessed) {
                foreach ($enrollments as $enrollment) {
                    // Dispatched a job to calculate each enrollments final grade
                    CalculateFinalGradeJob::dispatch($enrollment->id);
                    $totalProcessed++;
                }
            });

        $this->info("Dispatched jobs for {$totalProcessed} enrollments.");

        $semester->update(['is_processed' => true]);
        $this->info("Semester {$semester->id} has been marked as processed.");

        $this->info('Completed: Final Grade Calculation Process.');
    }
}
