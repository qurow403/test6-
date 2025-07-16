<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    // 申請一覧画面（管理者）
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');

        $pending = [
            [
                'id' => 1,
                'status' => '承認待ち',
                'name' => '西 伶奈',
                'target_date' => '2023/06/01',
                'reason' => '遅延のため',
                'applied_at' => '2023/06/02'
            ],
            [
                'id' => 2,
                'status' => '承認待ち',
                'name' => '山田 太郎',
                'target_date' => '2023/06/01',
                'reason' => '遅延のため',
                'applied_at' => '2023/08/02'
            ],
            [
                'id' => 3,
                'status' => '承認待ち',
                'name' => '山田 花子',
                'target_date' => '2023/06/02',
                'reason' => '遅延のため',
                'applied_at' => '2023/07/02'
            ],
        ];

        $approved = [
            [
                'id' => 4,
                'status' => '承認済み',
                'name' => '佐藤 花',
                'target_date' => '2023/06/05',
                'reason' => '記録忘れのため',
                'applied_at' => '2023/06/06'
            ],
        ];

        return view('admin.stamp_correction_request.index', [
            'status' => $status,
            'pending' => $pending,
            'approved' => $approved
        ]);
    }

    // 修正申請承認・詳細画面（管理者）
    public function show($id)
    {
        // セッションに更新データがあるか確認
    $updated = session("attendance_update.{$id}");

    if ($updated) {
        $detail = $updated;
    } else {
        // 仮のデフォルトデータ
        $requests = [
            1 => [
                'id' => 1,
                'name' => '西 伶奈',
                'date' => '2023年6月1日',
                'start_time' => '09:00',
                'end_time' => '18:00',
                'break1_start' => '12:00',
                'break1_end' => '13:00',
                'break2_start' => null,
                'break2_end' => null,
                'note' => '電車遅延のため',
            ],
            2 => [
                'id' => 2,
                'name' => '山田 太郎',
                'date' => '2023年6月1日',
                'start_time' => '10:00',
                'end_time' => '19:00',
                'break1_start' => '13:00',
                'break1_end' => '14:00',
                'break2_start' => null,
                'break2_end' => null,
                'note' => '寝坊のため',
            ],
        ];

        if (!isset($requests[$id])) {
            abort(404, '申請が見つかりません');
        }

        $detail = $requests[$id];
        $isApproved = session("approved_ids.{$id}", false);
        $detail['status'] = $isApproved ? 'approved' : 'pending';
    }


        return view('admin.approval.show', compact('detail'));
    }

    // 承認処理
    public function approve(Request $request, $id)
    {
        // 本番環境で解除する
        // $detail = AttendanceCorrectionRequest::findOrFail($id);
        // $detail->status = 'approved';
        // $detail->save();

        // セッションで承認状態を保存
        session()->put("approved_ids.{$id}", true);

        // 今回は確認用として一旦リダイレクト
        return redirect()->route('admin.approval.show', $id)->with('success', '承認しました');
    }
}
