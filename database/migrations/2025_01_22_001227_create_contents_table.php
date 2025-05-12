<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description');
            $table->text('bibliography')->nullable();
            $table->integer('order');
            $table->uuid('course_id')->nullable(); 
            $table->foreign('course_id')
                ->references('id')
                ->on('courses')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->uuid('grade_id')->nullable(); 
            $table->foreign('grade_id')
                ->references('id')
                ->on('grades')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            
            $table->dropForeign(['course_id']);
            $table->dropForeign(['grade_id']);
        });

        Schema::dropIfExists('contents');
    }
};
