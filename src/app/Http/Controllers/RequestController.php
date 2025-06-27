<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// 時間表記で追加
use Illuminate\Support\Carbon;

// ApprovalRequest(承認申請)モデル追加
use App\Models\ApprovalRequest;

class RequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending'); // デフォルト: pending

        // ダミーユーザーオブジェクト
        $user = (object)[
            'name' => '山田 太郎',
        ];

        // ダミー勤怠オブジェクト
        $attendance = (object)[
            'date' => Carbon::parse('2025-06-10'),
        ];

        // 状態に応じてフィルタされたダミーリスト
        $dummyRequests = collect([
            (object)[
                'id' => 1,
                'status' => 'pending',
                'note' => '出勤時間修正のため',
                'created_at' => Carbon::now()->subDays(1),
                'user' => $user,
                'attendance' => $attendance,
            ],
            (object)[
                'id' => 2,
                'status' => 'approved',
                'note' => '退勤漏れがあったため',
                'created_at' => Carbon::now()->subDays(2),
                'user' => $user,
                'attendance' => $attendance,
            ],
        ]);

        // フィルタ
        $requests = $dummyRequests->filter(function ($item) use ($status) {
            return $item->status === $status;
        });

        return view('requests.index', compact('requests'));
    }
}
