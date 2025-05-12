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
        Schema::create('grades', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('grade_type', ['ordinary', 'extraordinary', 'work', 'partial', 'final']);
            $table->decimal('grade_value', 5, 2)->nullable();
            $table->date('grade_date');
            $table->uuid('enrollment_id')->nullable(); 
            $table->foreign('enrollment_id')
                ->references('id')
                ->on('enrollments')
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
        Schema::table('grades', function (Blueprint $table) {
            
            $table->dropForeign(['enrollment_id']);
        });

        Schema::dropIfExists('grades');
    }
};
