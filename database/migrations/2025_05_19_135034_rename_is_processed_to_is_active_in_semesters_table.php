<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('semesters', function (Blueprint $table) {
            $table->renameColumn('is_processed', 'is_active');
        });

        // Invertir valores: is_processed false → is_active true, true → false
        DB::table('semesters')->update([
            'is_active' => DB::raw('NOT is_active')
        ]);
    }

    public function down(): void
    {
        Schema::table('semesters', function (Blueprint $table) {
            $table->renameColumn('is_active', 'is_processed');
        });

        DB::table('semesters')->update([
            'is_processed' => DB::raw('NOT is_processed')
        ]);
    }
};