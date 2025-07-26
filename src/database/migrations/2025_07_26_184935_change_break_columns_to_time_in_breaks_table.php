<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeBreakColumnsToTimeInBreaksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('breaks', function (Blueprint $table) {
            $table->time('break_start')->nullable()->change();
            $table->time('break_end')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('breaks', function (Blueprint $table) {
            $table->dateTime('break_start')->nullable()->change();
            $table->dateTime('break_end')->nullable()->change();
        });
    }
}
