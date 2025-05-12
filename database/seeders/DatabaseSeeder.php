<?php

namespace Database\Seeders;

use App\Models\User\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CountriesTableSeeder::class,
            RolesTableSeeder::class,
            PermissionSeeder::class,
            RolesAssigmenSeeder::class,
        ]);

        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password1234'),
            'phone_number' => '+1234567890',  // Añadir el campo phone_number
            'document' => '1234567890',  // Si tienes otros campos, añádelos también
            'city' => 'Test City',
            'postal_code' => '12345',
            'address' => 'Test Address',
            'date_of_birth' => '1990-01-01',
            'country_id' => 1 ,
        ]);
        $admin->assignRole('admin');

        $professor = User::factory()->create([
            'name' => 'Professor User',
            'email' => 'professor@example.com',
            'password' => bcrypt('password1234'),
            'phone_number' => '+9876543210',
            'document' => '0987654321',
            'city' => 'Los Angeles',
            'postal_code' => '90001',
            'address' => '456 Elm St',
            'date_of_birth' => '1975-05-15',
            'country_id' => 10,
        ]);
        $professor->assignRole('professor');

        $student = User::factory()->create([
            'name' => 'Student User',
            'email' => 'student@example.com',
            'password' => bcrypt('password1234'),
            'phone_number' => '+1122334455',
            'document' => '1122334455',
            'city' => 'Chicago',
            'postal_code' => '60601',
            'address' => '789 Oak St',
            'date_of_birth' => '2000-12-25',
            'country_id' => 20,
        ]);
        $student->assignRole('student');
    }
}
