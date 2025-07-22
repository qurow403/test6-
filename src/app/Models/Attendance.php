<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    // 勤怠ステータス定数
    public const STATUS_BEFORE = 'before';
    public const STATUS_WORKING = 'working';
    public const STATUS_ON_BREAK = 'on_break';
    public const STATUS_FINISHED = 'finished';

    protected $fillable = [
        'user_id',
        'date',
        'clock_in',
        'clock_out',
        'status',
        'worked_minutes',
    ];

    // userとのリレーション（多対1）
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // breaksとのリレーション（1対多）
    public function breaks()
    {
        return $this->hasMany(BreakTime::class);
    }

    // ApprovalRequestsとのリレーション（1対多）
    public function ApprovalRequests()
    {
        return $this->hasMany(ApprovalRequest::class);
    }

}
