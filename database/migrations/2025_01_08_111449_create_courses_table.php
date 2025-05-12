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
        Schema::create('courses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->json('schedule')->nullable();
            $table->json('weighting');
            $table->uuid('signature_id')->nullable();
            $table->foreign('signature_id')
                ->references('id')
                ->on('signatures')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->uuid('semester_id')->nullable();
            $table->foreign('semester_id')
                ->references('id')
                ->on('semesters')
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
        Schema::table('courses', function (Blueprint $table) {
            
            $table->dropForeign(['signature_id']);
            $table->dropForeign(['semester_id']);
        });

        Schema::dropIfExists('courses');
    }
};
