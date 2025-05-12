<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class ProfessorScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Verificamos si el usuario es un profesor autenticado
        if (auth()->check() && auth()->user()->hasRole('professor')) {
            // Si es profesor, limitamos la consulta a los cursos en los que está asignado como profesor
            $builder->whereHas('signatures', function (Builder $query) {
                $query->where('professor_id', auth()->id());
            });
        }
    }
}
