<?php

namespace App\Models\Content;

use App\Models\Course\Course;
use App\Models\Grade\Grade;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Content extends Model implements HasMedia
{
    use HasUuids, InteractsWithMedia;

    protected $fillable = [
        'name',
        'description',
        'bibliography',
        'order',
        'type',
        'format',
        'views',
        'downloads',
        'duration',
        'course_id',
        'grade_id',
    ];

    protected $casts = [
        'views' => 'integer',
        'downloads' => 'integer',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('content_files')
            ->singleFile()
            ->useDisk('public');
    }

    public function getFilePathAttribute()
    {
        $media = $this->getFirstMedia('content_files');
        return $media ? $media->getUrl() : null;
    }

    public function getSizeAttribute()
    {
        $media = $this->getFirstMedia('content_files');
        return $media ? $media->size : null;
    }
}