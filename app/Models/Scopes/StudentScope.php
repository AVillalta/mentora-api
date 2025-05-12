<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;


class StudentScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $user = request()->user();
        if ($user && $user->hasRole('student')) {
            // If the user is a student, apply a filter to the query
            // Only return courses where the authenticated student is enrolled
            $builder->whereHas('enrollments', function (Builder $query) use ($user) {
                $query->where('student_id', $user->id); // Filter enrollments by student ID
            });
        }
    }
}