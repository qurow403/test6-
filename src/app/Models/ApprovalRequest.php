<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class approvalRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'clock_in',
        'clock_out',
        'breaks',
        'status',
        'note',
    ];

    protected $casts = [
        'breaks' => 'array',
    ];

    // userとのリレーション(多対１)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // attendanceとのリレーション(多対１)
    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
}
