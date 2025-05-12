<?php

namespace App\Models\Semester;

use App\Data\Semester\SemesterCalendarData;
use App\Models\Course\Course;
use Illuminate\Database\Eloquent\Model;
use App\Models\Signature\Signature;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Semester extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'calendar'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'calendar' => 'array'
    ];

    public function getCalendarAttribute($value)
    {
        $decodedValue = is_string($value) ? json_decode($value, true) : $value;

        return SemesterCalendarData::fromArray($decodedValue ?? []);
        //return SemesterCalendarData::fromArray($value ?? []);
    }

    public function setCalendarAttribute($value)
    {
        if (is_array($value)) {
            // Convierte el array en JSON antes de guardarlo
            $this->attributes['calendar'] = json_encode($value);
        } elseif ($value instanceof SemesterCalendarData) {
            // Si es un objeto, lo convertimos a un array y luego a JSON
            $this->attributes['calendar'] = json_encode($value->toArray());
        } else {
            // Si no es nada de lo anterior, lo dejamos como un JSON vacío
            $this->attributes['calendar'] = json_encode([]);
        }
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'semester_id');
    }
}
