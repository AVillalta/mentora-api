<?php

namespace App\Models\Content;

use App\Models\Course\Course;
use App\Models\Grade\Grade;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'description',
        'bibliography',
        'order',
        'course_id',
        'grade_id'
    ];

    protected $casts = [];

    public function course()
    {
        return $this->belongsTo(Course::class); 
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class); 
    }
}
