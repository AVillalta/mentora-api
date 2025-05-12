<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Content\ContentController;
use App\Http\Controllers\Enrollment\EnrollmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Semester\SemesterController;
use App\Http\Controllers\Signature\SignatureController;
use App\Http\Controllers\Course\CourseController;
use App\Http\Controllers\Grade\GradeController;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Middleware\RoleMiddleware;

Route::get('/', function () {
    return [
        'app' => 'UniApi',
        'version' => 'x.x.x'
    ];
});

Route::prefix('v1')->group(function () {

    // Public Routes
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
        
    // Protected Routes
    Route::middleware('auth:api')->group(function () {

        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']); 

        //Route::middleware([RoleMiddleware::class . ':admin'])->group(function () {
            Route::apiResource('users', UserController::class);
            Route::apiResource('semesters', SemesterController::class);
            Route::apiResource('signatures', SignatureController::class);
            Route::apiResource('courses', CourseController::class);
            Route::apiResource('enrollments', EnrollmentController::class);
            Route::apiResource('grades', GradeController::class);
            Route::apiResource('contents', ContentController::class);  
        //});

        /**Route::middleware([RoleMiddleware::class . ':professor'])->group(function () {
            Route::apiResource('courses', CourseController::class)->except(['destroy']);
            Route::apiResource('enrollments', EnrollmentController::class)->except(['destroy']);
            //Route::apiResource('grades', GradeController::class)->except(['destroy']);
            Route::apiResource('contents', ContentController::class)->except(['destroy']);
        });

        Route::middleware([RoleMiddleware::class . ':student'])->group(function () {
            Route::get('/courses', [CourseController::class, 'index']);
            //Route::get('/enrollments', [EnrollmentController::class, 'index']);
            Route::get('/grades', [GradeController::class, 'index']);
            Route::get('/contents', [ContentController::class, 'index']);
        });**/
    });

});
