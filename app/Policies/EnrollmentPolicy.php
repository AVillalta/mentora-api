<?php

namespace App\Policies;

use App\Models\Enrollment\Enrollment;
use App\Models\User\User;
use Illuminate\Auth\Access\Response;

class EnrollmentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin']) && $user->hasPermissionTo('view-enrollments');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Enrollment $enrollment): bool
    {
        return $user->hasAnyRole(['admin']) && $user->hasPermissionTo('view-enrollments');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin']) && $user->hasPermissionTo('create-enrollments');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Enrollment $enrollment): bool
    {
        return $user->hasAnyRole(['admin']) && $user->hasPermissionTo('edit-enrollments');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Enrollment $enrollment): bool
    {
        return $user->hasAnyRole(['admin']) && $user->hasPermissionTo('delete-enrollments');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Enrollment $enrollment): bool
    {
        return $user->hasAnyRole(['admin']) && $user->hasPermissionTo('edit-enrollments');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Enrollment $enrollment): bool
    {
        return $user->hasAnyRole(['admin']) && $user->hasPermissionTo('delete-enrollments');
    }
}