<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('description');
            $table->uuid('course_id');
            $table->foreign('course_id')
                ->references('id')
                ->on('courses')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->uuid('grade_id')->nullable();
            $table->foreign('grade_id')
                ->references('id')
                ->on('grades')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->date('due_date');
            $table->integer('points');
            $table->integer('submissions')->default(0);
            $table->integer('total_students');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropForeign(['grade_id']);
        });

        Schema::dropIfExists('assignments');
    }
};