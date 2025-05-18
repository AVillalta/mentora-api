<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->dropForeign(['grade_id']);
            $table->dropColumn('grade_id');
        });
    }

    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->uuid('grade_id')->nullable()->after('course_id');
            $table->foreign('grade_id')
                  ->references('id')
                  ->on('grades')
                  ->nullOnDelete()
                  ->cascadeOnUpdate();
        });
    }
};