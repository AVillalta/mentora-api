<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class ProfessorScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (auth()->check() && auth()->user()->hasRole('professor')) {
            if ($model instanceof \App\Models\Course\Course) {
                $builder->whereHas('signature', function (Builder $query) {
                    $query->where('professor_id', auth()->id());
                });
            } elseif ($model instanceof \App\Models\Enrollment\Enrollment) {
                $builder->whereHas('course.signature', function (Builder $query) {
                    $query->where('professor_id', auth()->id());
                });
            }
        }
    }
}