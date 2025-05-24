<?php

namespace Database\Seeders;

use App\Models\Assignment\Assignment;
use App\Models\Content\Content;
use App\Models\Country\Country;
use App\Models\Course\Course;
use App\Models\Enrollment\Enrollment;
use App\Models\Grade\Grade;
use App\Models\Semester\Semester;
use App\Models\Signature\Signature;
use App\Models\User\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * @extends \Illuminate\Database\Seeder
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $this->call([
            CountriesTableSeeder::class,
            RolesTableSeeder::class,
            PermissionSeeder::class,
            RolesAssigmenSeeder::class,
        ]);

        // Crear usuarios base
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password1234'),
            'phone_number' => $faker->e164PhoneNumber(),
            'document' => $faker->unique()->numerify('########') . $faker->randomLetter(),
            'city' => $faker->city(),
            'postal_code' => $faker->postcode(),
            'address' => $faker->streetAddress(),
            'date_of_birth' => $faker->dateTimeBetween('-50 years', '-30 years')->format('Y-m-d'),
            'country_id' => Country::inRandomOrder()->first()->id ?? Country::factory()->create()->id,
        ]);
        $admin->withoutRelations()->assignRole('admin');

        $professor = User::factory()->create([
            'name' => 'Professor User',
            'email' => 'professor@example.com',
            'password' => bcrypt('password1234'),
            'phone_number' => $faker->e164PhoneNumber(),
            'document' => $faker->unique()->numerify('########') . $faker->randomLetter(),
            'city' => $faker->city(),
            'postal_code' => $faker->postcode(),
            'address' => $faker->streetAddress(),
            'date_of_birth' => $faker->dateTimeBetween('-60 years', '-30 years')->format('Y-m-d'),
            'country_id' => Country::inRandomOrder()->first()->id ?? Country::factory()->create()->id,
        ]);
        $professor->withoutRelations()->assignRole('professor');

        $student = User::factory()->create([
            'name' => 'Student User',
            'email' => 'student@example.com',
            'password' => bcrypt('password1234'),
            'phone_number' => $faker->e164PhoneNumber(),
            'document' => $faker->unique()->numerify('########') . $faker->randomLetter(),
            'city' => $faker->city(),
            'postal_code' => $faker->postcode(),
            'address' => $faker->streetAddress(),
            'date_of_birth' => $faker->dateTimeBetween('-25 years', '-16 years')->format('Y-m-d'),
            'country_id' => Country::inRandomOrder()->first()->id ?? Country::factory()->create()->id,
        ]);
        $student->withoutRelations()->assignRole('student');

        // Crear usuarios adicionales
        $additionalProfessors = User::factory()->professor()->count(9)->create()->each(function ($user) use ($faker) {
            try {
                $user->addMediaFromUrl('https://picsum.photos/200')->toMediaCollection('profile_photo');
            } catch (\Exception $e) {
                Log::warning("No se pudo añadir foto de perfil para usuario {$user->id}: {$e->getMessage()}");
            }
        });
        $additionalStudents = User::factory()->student()->count(49)->create()->each(function ($user) use ($faker) {
            try {
                $user->addMediaFromUrl('https://picsum.photos/200')->toMediaCollection('profile_photo');
            } catch (\Exception $e) {
                Log::warning("No se pudo añadir foto de perfil para usuario {$user->id}: {$e->getMessage()}");
            }
        });

        // Combinar usuarios
        $professors = collect([$professor])->merge($additionalProfessors);
        $students = collect([$student])->merge($additionalStudents);

        // Crear 3 semestres (2 no activos, 1 activo)
        $pastSemesters = Semester::factory()->count(2)->create(['is_active' => false]);
        $activeSemester = Semester::factory()->active()->create([
            'name' => Carbon::now()->year . '-' . (Carbon::now()->year + 1) . ' Primer Semestre',
        ]);
        $semesters = collect([$activeSemester])->merge($pastSemesters);

        // Crear 20 asignaturas, distribuidas entre profesores (2 por profesor en promedio)
        $signatures = collect();
        $professors->each(function ($professor) use (&$signatures, $faker) {
            $numSignatures = $faker->numberBetween(1, 3);
            if ($signatures->count() + $numSignatures > 20) {
                $numSignatures = 20 - $signatures->count();
            }
            for ($i = 0; $i < $numSignatures && $signatures->count() < 20; $i++) {
                $signature = Signature::factory()->create(['professor_id' => $professor->id]);
                if ($faker->boolean(20)) {
                    try {
                        $signature->addMediaFromUrl('https://www.irs.gov/pub/irs-pdf/n1036.pdf')
                                  ->toMediaCollection('syllabus_pdf');
                    } catch (\Exception $e) {
                        Log::warning("No se pudo añadir medio para asignatura {$signature->id}: {$e->getMessage()}");
                    }
                }
                $signatures->push($signature);
            }
        });

        // Crear 30 cursos, distribuidos equitativamente (10 por semestre), con asignaturas únicas por semestre
        $courses = collect();
        $semesters->each(function ($semester) use (&$courses, $signatures, $faker) {
            $numCourses = 10;
            $availableSignatures = $signatures->shuffle()->values(); // Mezclar asignaturas
            $usedSignatureIds = Course::where('semester_id', $semester->id)->pluck('signature_id')->toArray();
            $availableSignatures = $availableSignatures->filter(fn ($sig) => !in_array($sig->id, $usedSignatureIds));
            for ($i = 0; $i < $numCourses && $availableSignatures->isNotEmpty(); $i++) {
                $signature = $availableSignatures->shift(); // Tomar la primera asignatura disponible
                $course = Course::factory()->create([
                    'semester_id' => $semester->id,
                    'signature_id' => $signature->id,
                ]);
                $courses->push($course);
            }
        });

        // Crear matrículas, distribuyendo estudiantes entre semestres (1-3 cursos por estudiante por semestre)
        $students->each(function ($student) use ($courses, $semesters, $faker) {
            $semesters->each(function ($semester) use ($student, $courses, $faker) {
                $semesterCourses = $courses->where('semester_id', $semester->id);
                if ($semesterCourses->isNotEmpty()) {
                    $numCourses = $faker->numberBetween(1, min(3, $semesterCourses->count()));
                    $selectedCourses = $semesterCourses->random($numCourses);
                    foreach ($selectedCourses as $course) {
                        Enrollment::factory()->create([
                            'student_id' => $student->id,
                            'course_id' => $course->id,
                        ]);
                    }
                }
            });
        });

        // Crear 50 contenidos, distribuidos entre cursos
        Content::factory()->count(50)->create([
            'course_id' => fn () => $courses->random()->id,
        ])->each(function ($content) use ($faker) {
            if ($faker->boolean(20)) {
                try {
                    $fileUrl = (new \Database\Factories\Content\ContentFactory())->getFileUrl($content->type, $content->format);
                    $content->addMediaFromUrl($fileUrl)->toMediaCollection('content_files');
                } catch (\Exception $e) {
                    Log::warning("No se pudo añadir medio para contenido {$content->id}: {$e->getMessage()}");
                }
            }
        });

        // Crear 30 tareas, distribuidas entre cursos
        Assignment::factory()->count(30)->withEnrollments()->when($faker->boolean(80), fn ($factory) => $factory->withSubmissions())->create([
            'course_id' => fn () => $courses->random()->id,
        ]);
    }
}