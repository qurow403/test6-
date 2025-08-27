<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeClockColumnsToTimeInAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE attendances MODIFY clock_in TIME NULL");
        DB::statement("ALTER TABLE attendances MODIFY clock_out TIME NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE attendances MODIFY clock_in TIMESTAMP NULL");
        DB::statement("ALTER TABLE attendances MODIFY clock_out TIMESTAMP NULL");
    }
}
