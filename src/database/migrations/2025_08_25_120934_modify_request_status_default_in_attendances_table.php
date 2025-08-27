<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyRequestStatusDefaultInAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE attendances MODIFY request_status ENUM('pending', 'approved', 'rejected') DEFAULT 'approved'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE attendances MODIFY request_status ENUM('pending', 'approved', 'rejected') DEFAULT NULL");
    }
}
