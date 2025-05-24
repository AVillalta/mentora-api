<?php

namespace App\Models\Enrollment;

use App\Models\Scopes\StudentScope;
use App\Models\Course\Course;
use App\Models\Evaluation\Evaluation;
use App\Models\Grade\Grade;
use App\Models\Scopes\ProfessorScope;
use App\Models\Signature\Signature;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'enrollment_date',
        'final_grade',
        'course_id',
        'student_id'
    ];

    protected $casts = [
        'enrollment_date' => 'date',
    ];

    /**
     * Get the signature associated with the enrollment.
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    /**
     * Get the student associated with the enrollment.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function grades()
    {
        return $this->hasMany(Grade::class, 'enrollment_id');
    }

    protected static function booted()
    {
        parent::boot();

        static::addGlobalScope(new StudentScope);

        static::addGlobalScope(new ProfessorScope);
    }
}
