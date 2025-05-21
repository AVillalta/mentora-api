<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemovePointsFromAssignments extends Migration
{
    public function up()
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropColumn('points');
        });
    }

    public function down()
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->integer('points')->nullable()->after('due_date');
        });
    }
}
