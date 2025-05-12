<?php

namespace App\Services\Enrollment;

use App\Models\Enrollment\Enrollment;
use App\Models\Grade\Grade;
use App\Models\Semester\Semester;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class FinalGradeService{

    /**
     * Calculate the final grade for a given enrollment.
     *
     * @param Enrollment $enrollment
     * @return float
     */
    public function calculateFinalGrade(Enrollment $enrollment): float
    {
        return DB::transaction(function() use ($enrollment) {
            try {
                $ordinaryGrade = $this->calculateOrdinaryGrade($enrollment);
                $extraordinaryGrade = $this->getExtraordinaryGrade($enrollment);

                // Determine the final grade: max between ordinary and extraordinary
                $finalGrade = max($ordinaryGrade, $extraordinaryGrade);

                // Save the final grade in the enrollment
                $enrollment->update(['final_grade' => $finalGrade]);

                return $finalGrade;
            } catch (\Exception $e) {
                Log::error("Error calculando la nota final para el enrollment {$enrollment->id}: " . $e->getMessage());
                throw $e;
            }
        });
        
    }

    /**
     * Calculate the ordinary grade based on course weightings and grades.
     *
     * @param Enrollment $enrollment
     * @return float
     */
    private function calculateOrdinaryGrade(Enrollment $enrollment): float
    {
        return DB::transaction(function() use ($enrollment) {
            $grades = $enrollment->grades;
            $course = $enrollment->course;
            $weighting = $course->weighting;
            $ordinaryGrade = 0;

            foreach ($weighting as $type => $weight) { 
                $typeGrades = $grades->where('grade_type', $type);
                
                if ($typeGrades->isNotEmpty()) {            
                    $averageTypeGrade = $typeGrades->avg('grade_value');  
                    $ordinaryGrade += $averageTypeGrade * $weight;
                }
            }

            $ordinaryGrade = round($ordinaryGrade, 2);

            $existingOrdinaryGrade = $grades->firstWhere('grade_type', 'ordinary');

            if (!$existingOrdinaryGrade) {
                $enrollment->grades()->create([
                    'grade_type' => 'ordinary',
                    'grade_value' => $ordinaryGrade,
                    'grade_date' => now(),
                ]);
            } else {
                $existingOrdinaryGrade->update([
                    'grade_value' => $ordinaryGrade,
                    'grade_date' => now(),
                ]);
            }
            return $ordinaryGrade;
        });
    }

    /**
     * Get the extraordinary grade if available.
     *
     * @param Enrollment $enrollment
     * @return float
     */
    private function getExtraordinaryGrade(Enrollment $enrollment): float
    {
        return $enrollment->grades()
            ->where('grade_type', 'extraordinary')
            ->max('grade_value') ?? 0.00;
    }
    

}