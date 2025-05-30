<?php

namespace App\Models\Course;

use App\Models\Content\Content;
use App\Models\Enrollment\Enrollment;
use App\Models\Scopes\ProfessorScope;
use App\Models\Scopes\StudentScope;
use App\Models\Semester\Semester;
use App\Models\Signature\Signature;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'code',
        'schedule',
        'weighting',
        'signature_id',
        'semester_id'
    ];

    protected $casts = [
        'schedule' => 'array',
        'weighting' => 'array'
    ];

    public function signature()
    {
        return $this->belongsTo(Signature::class, 'signature_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function contents()
    {
        return $this->hasMany(Content::class);
    }

    protected static function booted()
    {
        parent::boot();
        static::addGlobalScope(new ProfessorScope);
        static::addGlobalScope(new StudentScope);
    }
}