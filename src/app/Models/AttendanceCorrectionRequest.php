<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceCorrectionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'target_date',
        'clock_in',
        'clock_out',
        'reason',
        'status',
        'applied_at',
    ];

    protected $casts = [
        'target_date' => 'date',
        'clock_in' => 'datetime:H:i',
        'clock_out' => 'datetime:H:i',
        'applied_at' => 'datetime',
    ];

    // ユーザーとのリレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 修正申請に紐づく休憩時間（最大2件）
    public function breakCorrections()
    {
        return $this->hasMany(BreakCorrection::class);
    }
}
