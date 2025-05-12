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
        Schema::create('signatures', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->json('syllabus')->nullable();
            $table->string('syllabus_pdf')->nullable();
            $table->uuid('professor_id')->nullable();
            $table->foreign('professor_id')
                ->references('id')
                ->on('users')
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
        Schema::table('signatures', function (Blueprint $table) {
            
            $table->dropForeign(['professor_id']);
        });

        Schema::dropIfExists('signatures');
    }
};
