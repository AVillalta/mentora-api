<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAssigmenSeeder extends Seeder
{
    public function run(): void
    {
        $studentRole = Role::findByName('student');
        $professorRole = Role::findByName('professor');
        $adminRole = Role::findByName('admin');

        $roles = [
            'admin' => Permission::all(),
            'professor' => [
                'view-users',
                'view-semesters',
                'view-signatures',
                'view-courses', 'create-courses', 'edit-courses',
                'view-enrollments', 'create-enrollments', 'edit-enrollments',
                'view-grades', 'create-grades', 'edit-grades', 'delete-grades',
                'view-contents', 'create-contents', 'edit-contents', 'delete-contents',
                'view-assignments', 'create-assignments', 'edit-assignments', 'delete-assignments',
            ],
            'student' => [
                'view-users',
                'view-semesters',
                'view-signatures',
                'view-courses',
                'view-enrollments',
                'view-grades',
                'view-contents',
                'view-assignments',
            ],
        ];

        foreach ($roles as $roleName => $permissions) {
            $role = Role::findByName($roleName);
            $role->syncPermissions($permissions);
        }
    }
}
