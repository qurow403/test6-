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
        // 仮データ（承認 or 承認済みで切り替え）
        $detail = [
            'id' => $id,
            'name' => '西 伶奈',
            'date' => '2023年6月1日',
            'start_time' => '09:00',
            'end_time' => '18:00',
            'break1_start' => '12:00',
            'break1_end' => '13:00',
            'break2_start' => null,
            'break2_end' => null,
            'note' => '電車遅延のため',
            'status' => $id == 1 ? 'pending' : 'approved', // 仮ロジック
        ];

        return view('admin.approval.show', compact('detail'));
    }

    // 承認処理
    public function approve(Request $request, $id)
    {
        // 承認処理（実装例: データベースのstatusをapprovedに更新するなど）

        // 今回は確認用として一旦リダイレクト
        return redirect()->route('admin.approval.show', $id)->with('success', '承認しました');
    }
}
