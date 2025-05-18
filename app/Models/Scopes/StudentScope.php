<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class StudentScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $user = auth()->user();
        if ($user && $user->hasRole('student')) {
            if ($model instanceof \App\Models\Enrollment\Enrollment) {
                $builder->where('student_id', $user->id);
            } elseif ($model instanceof \App\Models\Course\Course) {
                $builder->whereHas('enrollments', function (Builder $query) use ($user) {
                    $query->where('student_id', $user->id);
                });
            }
        }
    }
}