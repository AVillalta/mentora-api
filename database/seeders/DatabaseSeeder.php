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
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

/**
 * @extends \Illuminate\Database\Seeder
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar medios antes de seeding
        Artisan::call('media:clear');
        Log::info('Media cleared before seeding.');

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
        try {
            $admin->withoutRelations()->assignRole('admin');
        } catch (\Exception $e) {
            Log::warning("No se pudo asignar rol 'admin' al usuario {$admin->id}: {$e->getMessage()}");
        }

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
        try {
            $professor->withoutRelations()->assignRole('professor');
        } catch (\Exception $e) {
            Log::warning("No se pudo asignar rol 'professor' al usuario {$professor->id}: {$e->getMessage()}");
        }

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
        try {
            $student->withoutRelations()->assignRole('student');
        } catch (\Exception $e) {
            Log::warning("No se pudo asignar rol 'student' al usuario {$student->id}: {$e->getMessage()}");
        }

        // Crear usuarios adicionales
        try {
            $additionalProfessors = User::factory()->professor()->count(9)->create()->each(function ($user) use ($faker) {
                try {
                    $user->addMediaFromUrl('https://picsum.photos/200')->toMediaCollection('profile_photo');
                } catch (\Exception $e) {
                    Log::warning("No se pudo añadir foto de perfil para usuario {$user->id}: {$e->getMessage()}");
                }
            });
        } catch (\Exception $e) {
            Log::error("Error al crear profesores adicionales: {$e->getMessage()}");
            throw $e;
        }

        try {
            $additionalStudents = User::factory()->student()->count(49)->create()->each(function ($user) use ($faker) {
                try {
                    $user->addMediaFromUrl('https://picsum.photos/200')->toMediaCollection('profile_photo');
                } catch (\Exception $e) {
                    Log::warning("No se pudo añadir foto de perfil para usuario {$user->id}: {$e->getMessage()}");
                }
            });
        } catch (\Exception $e) {
            Log::error("Error al crear estudiantes adicionales: {$e->getMessage()}");
            throw $e;
        }

        // Combinar usuarios
        $professors = collect([$professor])->merge($additionalProfessors);
        $students = collect([$student])->merge($additionalStudents);

        // Crear 3 semestres (2 no activos, 1 activo)
        $pastSemesters = Semester::factory()->count(2)->create(['is_active' => false]);
        $activeSemester = Semester::factory()->active()->create([
            'name' => Carbon::now()->year . '-' . (Carbon::now()->year + 1) . ' Primer Semestre',
        ]);
        $semesters = collect([$activeSemester])->merge($pastSemesters);

        // Crear una asignatura para Professor User en el semestre activo
        $professorSignature = Signature::factory()->create(['professor_id' => $professor->id]);
        if ($faker->boolean(20)) {
            try {
                $professorSignature->addMediaFromUrl('https://www.irs.gov/pub/irs-pdf/n1036.pdf')
                                  ->toMediaCollection('syllabus_pdf');
            } catch (\Exception $e) {
                Log::warning("No se pudo añadir medio para asignatura {$professorSignature->id}: {$e->getMessage()}");
            }
        }

        // Crear un curso activo para Professor User y matricular a Student User
        $activeCourse = Course::factory()->create([
            'semester_id' => $activeSemester->id,
            'signature_id' => $professorSignature->id,
        ]);
        Enrollment::factory()->create([
            'student_id' => $student->id,
            'course_id' => $activeCourse->id,
        ]);

        // Crear las 19 asignaturas restantes
        $signatures = collect([$professorSignature]);
        $signatureCount = 1;
        $professors = $professors->shuffle();
        $professorIndex = 0;
        while ($signatureCount < 20) {
            $professor = $professors[$professorIndex % $professors->count()];
            $numSignatures = $faker->numberBetween(1, 3);
            if ($signatureCount + $numSignatures > 20) {
                $numSignatures = 20 - $signatureCount;
            }
            for ($i = 0; $i < $numSignatures; $i++) {
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
                $signatureCount++;
            }
            $professorIndex++;
        }

        // Crear 29 cursos restantes, distribuidos equitativamente (9-10 por semestre), con asignaturas únicas por semestre
        $courses = collect([$activeCourse]);
        $semesters->each(function ($semester) use (&$courses, $signatures, $faker, $activeSemester) {
            $numCourses = $semester->id === $activeSemester->id ? 9 : 10; // 9 para activo (1 ya creado), 10 para inactivos
            $availableSignatures = $signatures->shuffle()->values();
            $usedSignatureIds = Course::where('semester_id', $semester->id)->pluck('signature_id')->toArray();
            $availableSignatures = $availableSignatures->filter(fn ($sig) => !in_array($sig->id, $usedSignatureIds));
            for ($i = 0; $i < $numCourses && $availableSignatures->isNotEmpty(); $i++) {
                $signature = $availableSignatures->shift();
                $course = Course::factory()->create([
                    'semester_id' => $semester->id,
                    'signature_id' => $signature->id,
                ]);
                $courses->push($course);
            }
        });

        // Crear matrículas, distribuyendo estudiantes entre semestres (1-2 cursos por estudiante, 2-3 semestres)
        $students->each(function ($student) use ($courses, $semesters, $faker, $activeCourse) {
            $numSemesters = $faker->numberBetween(2, 3); // 2-3 semestres por estudiante
            $selectedSemesters = $semesters->shuffle()->take($numSemesters);
            $selectedSemesters->each(function ($semester) use ($student, $courses, $faker, $activeCourse) {
                $semesterCourses = $courses->where('semester_id', $semester->id);
                if ($semesterCourses->isNotEmpty()) {
                    $numCourses = $faker->numberBetween(1, min(2, $semesterCourses->count()));
                    $selectedCourses = $semesterCourses->random($numCourses);
                    foreach ($selectedCourses as $course) {
                        // Evitar duplicar la matrícula del estudiante base en el curso activo
                        if ($student->email !== 'student@example.com' || $course->id !== $activeCourse->id) {
                            Enrollment::factory()->create([
                                'student_id' => $student->id,
                                'course_id' => $course->id,
                            ]);
                        }
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

        // Crear 30 tareas, distribuidos entre cursos
        Assignment::factory()->count(30)->withEnrollments()->when($faker->boolean(80), fn ($factory) => $factory->withSubmissions())->create([
            'course_id' => fn () => $courses->random()->id,
        ]);
    }
}