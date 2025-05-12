<?php

namespace App\Models\Signature;

use App\Models\Course\Course;
use App\Models\Enrollment\Enrollment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Semester\Semester;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Signature extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, HasUuids;

    protected $fillable = [
        'name',
        'syllabus',
        'professor_id'
    ];

    protected $casts = [
        'syllabus' => 'array',
        'schedule' => 'array'
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('syllabus_pdf')
             ->singleFile() 
             ->useDisk('public') 
             ->setPath('syllabus_pdfs/' . $this->id); 
    }

    public function getSyllabusPdfAttribute()
    {
        $media = $this->getFirstMedia('syllabus_pdf');
        return $media ? $media->getUrl() : null;
    }

    public function professor()
    {
        return $this->belongsTo(User::class, 'professor_id');
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'signature_id');
    }
}
