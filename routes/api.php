<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Content\ContentController;
use App\Http\Controllers\Enrollment\EnrollmentController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Semester\SemesterController;
use App\Http\Controllers\Signature\SignatureController;
use App\Http\Controllers\Course\CourseController;
use App\Http\Controllers\Grade\GradeController;
use App\Http\Controllers\Assignment\AssignmentController;
use App\Http\Controllers\Country\CountryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return [
        'app' => 'UniApi',
        'version' => 'x.x.x'
    ];
});

Route::prefix('v1')->group(function () {
    // Rutas públicas
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    // Rutas protegidas por autenticación
    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::get('/countries', [CountryController::class, 'index']);

        // Rutas exclusivas para admin
        Route::middleware('\Spatie\Permission\Middleware\RoleMiddleware:admin')->group(function () {
            Route::apiResource('users', UserController::class);
            Route::apiResource('semesters', SemesterController::class);
            Route::apiResource('signatures', SignatureController::class);
            Route::apiResource('courses', CourseController::class);
            Route::put('courses/{course}', [CourseController::class, 'update']);
            Route::delete('courses/{course}', [CourseController::class, 'destroy']);
            Route::apiResource('enrollments', EnrollmentController::class);
            Route::apiResource('grades', GradeController::class);
            Route::apiResource('contents', ContentController::class);
            Route::apiResource('assignments', AssignmentController::class);
            Route::post('assignments/{assignment}/submissions/{submissionId}/grade', [AssignmentController::class, 'gradeSubmission']);
        });

        // Rutas accesibles para admin, student y professor
        Route::middleware('\App\Http\Middleware\MultiRoleMiddleware:admin,student,professor')->group(function () {
            Route::get('/courses', [CourseController::class, 'index']);
            Route::get('/courses/{course}', [CourseController::class, 'show']);
            Route::get('/courses/{course}/grades', [GradeController::class, 'indexByCourse']);
            Route::get('/courses/{course}/contents', [ContentController::class, 'indexByCourse']);
            Route::get('/courses/{course}/assignments', [AssignmentController::class, 'indexByCourse']);
            Route::get('/grades', [GradeController::class, 'index']);
            Route::get('/contents', [ContentController::class, 'index']);
            Route::get('/assignments', [AssignmentController::class, 'index']);
        });

        // Rutas exclusivas para admin y professor
        Route::middleware('\App\Http\Middleware\MultiRoleMiddleware:admin,professor')->group(function () {
            Route::post('/grades', [GradeController::class, 'store']);
            Route::put('/grades/{grade}', [GradeController::class, 'update']);
            Route::delete('/grades/{grade}', [GradeController::class, 'destroy']);
            Route::post('/contents', [ContentController::class, 'store']);
            Route::put('/contents/{content}', [ContentController::class, 'update']);
            Route::delete('/contents/{content}', [ContentController::class, 'destroy']);
            Route::post('/assignments', [AssignmentController::class, 'store']);
            Route::put('/assignments/{assignment}', [AssignmentController::class, 'update']);
            Route::delete('/assignments/{assignment}', [AssignmentController::class, 'destroy']);
            Route::get('/enrollments', [EnrollmentController::class, 'index']);
        });

        // Rutas exclusivas para student
        Route::middleware('\App\Http\Middleware\MultiRoleMiddleware:student')->group(function () {
            Route::post('/assignments/{assignment}/submit', [AssignmentController::class, 'submit']);
        });
    });
});