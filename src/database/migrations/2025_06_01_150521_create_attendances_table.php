<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date'); // 勤務日
            $table->timestamp('clock_in')->nullable();  // 出勤時間
            $table->timestamp('clock_out')->nullable(); // 退勤時間
            $table->enum('status', ['off_duty', 'working', 'on_break', 'finished'])->default('off_duty');
            $table->integer('worked_minutes')->nullable(); // 実働時間（分）
            $table->timestamps();
            // statusカラム →  off_duty(勤務外),working(出勤中),on_break(休憩中),finished(退勤済)
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendances');
    }
}
