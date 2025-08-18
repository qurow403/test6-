<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

// モデル追加（AttendanceCorrectionRequest(勤怠修正申請)）
use App\Models\AttendanceCorrectionRequest;

class RequestController extends Controller
{
    // 申請一覧画面（管理者）
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');

        $pending = AttendanceCorrectionRequest::with('user')
            ->where('status', 'pending')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'status' => '承認待ち',
                    'name' => $item->user->name ?? '未設定',
                    'target_date' => $item->target_date->format('Y/m/d'),
                    'reason' => $item->reason,
                    'applied_at' => $item->applied_at->format('Y/m/d'),
                ];
            });

        $approved = AttendanceCorrectionRequest::with('user')
            ->where('status', 'approved')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'status' => '承認済み',
                    'name' => $item->user->name ?? '未設定',
                    'target_date' => $item->target_date->format('Y/m/d'),
                    'reason' => $item->reason,
                    'applied_at' => $item->applied_at->format('Y/m/d'),
                ];
            });

        return view('admin.stamp_correction_request.index', [
            'currentStatus' => $status,
            'pending' => $pending,
            'approved' => $approved,
        ]);
    }

    // 修正申請承認・詳細画面（管理者）
    public function show($id)
    {
        // 修正申請データを取得
        $request = AttendanceCorrectionRequest::with(['user', 'breakCorrections'])->findOrFail($id);

        // 表示用データ整形
        $detail = [
            'id' => $request->id,
            'name' => $request->user->name,
            'date' => Carbon::parse($request->target_date)->format('Y年n月j日'),
            'clock_in' => $request->clock_in ? Carbon::parse($request->target_date.' '.$request->clock_in)->format('H:i') : null,
            'clock_out' => $request->clock_out ? Carbon::parse($request->target_date.' '.$request->clock_out)->format('H:i') : null,
            'note' => $request->reason,
            'status' => $request->status,
            'breaks' => $request->breakCorrections->map(function ($break) {
                return [
                    'start' => optional($break->break_start)->format('H:i'),
                    'end'   => optional($break->break_end)->format('H:i'),
                ];
            })->toArray(),
        ];

        // break1, break2 のフォーマット保証（最大2件までに調整）
        $detail['breaks'] = array_pad($detail['breaks'], 2, ['start' => null, 'end' => null]);

        return view('admin.approval.show', compact('detail'));
    }

    // 承認処理（ステータス更新）
    public function approve(Request $request, $id)
    {
        $correction = AttendanceCorrectionRequest::findOrFail($id);

        if ($correction->status === 'approved') {
            return redirect()->route('admin.approval.show', $id)
                ->with('message', 'すでに承認済みです。');
        }

        // 対象日の勤怠データを取得（無ければ新規作成）
        $attendance = \App\Models\Attendance::firstOrCreate(
            [
                'user_id' => $correction->user_id,
                'date'    => $correction->target_date,
            ]
        );

        // 出勤・退勤時間を更新（修正申請のデータを反映）
        if ($correction->clock_in) {
            $attendance->clock_in = Carbon::parse($correction->target_date . ' ' . $correction->clock_in);
        }
        if ($correction->clock_out) {
            $attendance->clock_out = Carbon::parse($correction->target_date . ' ' . $correction->clock_out);
        }

        // --- 休憩時間の反映処理 ---
        $approval = \App\Models\ApprovalRequest::where('attendance_id', $attendance->id)->first();

        if ($approval && $approval->breaks) {
            $breaks = json_decode($approval->breaks, true);
            $totalBreakMinutes = 0;

            foreach ($breaks as $break) {
                if (!empty($break['start']) && !empty($break['end'])) {
                    $start = Carbon::parse($correction->target_date . ' ' . $break['start']);
                    $end   = Carbon::parse($correction->target_date . ' ' . $break['end']);
                    $totalBreakMinutes += $start->diffInMinutes($end);
                }
            }

            $attendance->break_duration = $totalBreakMinutes;
        }

        // 実働時間の再計算
        if ($attendance->clock_in && $attendance->clock_out) {
            $attendance->worked_minutes =
                $attendance->clock_in->diffInMinutes($attendance->clock_out)
                - ($attendance->break_duration ?? 0);
        }

        $attendance->save();

        $correction->status = 'approved';
        $correction->save();

        return redirect()->route('admin.approval.show', $id)
            ->with('success', '修正申請を承認しました。');
    }
}
