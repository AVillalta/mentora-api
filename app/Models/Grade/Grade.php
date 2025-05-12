<?php

namespace App\Models\Grade;

use App\Models\Content\Content;
use App\Models\Enrollment\Enrollment;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id',
        'grade_type',
        'grade_value',
        'grade_date',
        'enrollment_id',
    ];

    protected $casts = [
        'grade_value' => 'decimal:2',
        'grade_date' => 'date',
    ];

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function contents()
    {
        return $this->hasMany(Content::class); 
    }
}
