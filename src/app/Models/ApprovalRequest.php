<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalRequest extends Model
{
    use HasFactory;

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
