<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTitleToGradesTable extends Migration
{
    public function up()
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->string('title')->nullable()->after('grade_type');
        });
    }

    public function down()
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->dropColumn('title');
        });
    }
}
