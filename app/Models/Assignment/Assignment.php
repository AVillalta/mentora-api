<?php

namespace App\Models\Assignment;

use App\Models\Course\Course;
use App\Models\Grade\Grade;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Assignment extends Model implements HasMedia
{
    use HasFactory, HasUuids, InteractsWithMedia;

    protected $fillable = [
        'title',
        'description',
        'course_id',
        'due_date',
        'submissions',
        'total_students',
    ];

    protected $casts = [
        'due_date' => 'date',
        'submissions' => 'integer',
        'total_students' => 'integer',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('assignment_submissions')
             ->useDisk('public');
    }

    public function addSubmission(UploadedFile $file, User $student)
    {
        // Eliminar todos los archivos existentes del estudiante
        $existingSubmissions = $this->getMedia('assignment_submissions')
            ->where('custom_properties.student_id', $student->id);

        foreach ($existingSubmissions as $submission) {
            $submission->delete();
        }

        return $this->addMedia($file)
                    ->withCustomProperties(['student_id' => $student->id])
                    ->toMediaCollection('assignment_submissions');
    }

    public function getSubmissionsFilesAttribute()
    {
        return $this->getMedia('assignment_submissions')->map(function ($media) {
            return [
                'id' => $media->id,
                'file_name' => $media->file_name,
                'url' => $media->getUrl(),
                'size' => $media->size,
                'student_id' => $media->getCustomProperty('student_id'),
                'created_at' => $media->created_at->toIso8601String(),
            ];
        });
    }
}