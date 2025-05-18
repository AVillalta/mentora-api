<?php

namespace App\Services\Enrollment;

use App\Models\Enrollment\Enrollment;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class EnrollmentService
{
    /**
     * Get all enrollments based on user role.
     *
     * @return \Illuminate\Database\Eloquent\Collection|\App\Models\Enrollment\Enrollment[]
     */
    public function getAllEnrollments()
    {
        $user = auth()->user();

        if ($user->hasRole('student')) {
            return Enrollment::where('student_id', $user->id)->get();
        } elseif ($user->hasRole('professor')) {
            return Enrollment::whereHas('course.signature', function ($query) use ($user) {
                $query->where('professor_id', $user->id);
            })->get();
        }

        return Enrollment::withoutGlobalScopes()->get();
    }

    /**
     * Create new enrollment.
     *
     * @param array $data
     * @return \App\Models\Enrollment\Enrollment|array
     */
    public function saveEnrollment(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Verificar si la matrícula ya existe
            $existingEnrollment = Enrollment::where('student_id', $data['student_id'])
                                           ->where('course_id', $data['course_id'])
                                           ->first();
            if ($existingEnrollment) {
                return [
                    'message' => 'El estudiante ya está matriculado en este curso.',
                    'status' => Response::HTTP_CONFLICT
                ];
            }

            try {
                return Enrollment::create([
                    'enrollment_date' => $data['enrollment_date'],
                    'course_id' => $data['course_id'],
                    'student_id' => $data['student_id'],
                ]);
            } catch (\Exception $e) {
                return [
                    'message' => 'Error al crear la matrícula: ' . $e->getMessage(),
                    'status' => Response::HTTP_INTERNAL_SERVER_ERROR
                ];
            }
        });
    }

    /**
     * Get enrollment by id.
     *
     * @param array $data
     * @return \App\Models\Enrollment\Enrollment|array
     */
    public function showEnrollment(array $data)
    {
        try {
            return Enrollment::findOrFail($data['enrollment_id']);
        } catch (\Exception $e) {
            return [
                'message' => 'Matrícula no encontrada.',
                'status' => Response::HTTP_NOT_FOUND
            ];
        }
    }

    /**
     * Update enrollment.
     *
     * @param array $data
     * @param string $id
     * @return \App\Models\Enrollment\Enrollment|array
     */
    public function updateEnrollment(array $data, $id)
    {
        return DB::transaction(function () use ($data, $id) {
            try {
                $enrollment = Enrollment::findOrFail($id);

                $updates = [
                    'enrollment_date' => $data['enrollment_date'] ?? $enrollment->enrollment_date,
                    'course_id' => $data['course_id'] ?? $enrollment->course_id,
                    'student_id' => $data['student_id'] ?? $enrollment->student_id,
                ];

                $enrollment->update($updates);
                return $enrollment;
            } catch (\Exception $e) {
                return [
                    'message' => 'Error al actualizar la matrícula: ' . $e->getMessage(),
                    'status' => Response::HTTP_BAD_REQUEST
                ];
            }
        });
    }

    /**
     * Delete enrollment.
     *
     * @param string $id
     * @return bool|array
     */
    public function deleteEnrollment($id)
    {
        return DB::transaction(function () use ($id) {
            try {
                $enrollment = Enrollment::findOrFail($id);
                $enrollment->delete();
                return true;
            } catch (\Exception $e) {
                return [
                    'message' => 'Error al eliminar la matrícula: ' . $e->getMessage(),
                    'status' => Response::HTTP_NOT_FOUND
                ];
            }
        });
    }
}