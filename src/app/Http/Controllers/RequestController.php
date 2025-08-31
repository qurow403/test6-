<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

// 時間表記で追加
use Illuminate\Support\Carbon;

// ApprovalRequest(承認申請)モデル追加
use App\Models\approvalRequest;

class RequestController extends Controller
{
    // 申請一覧画面（一般ユーザー）
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending'); // デフォルト: pending

        // attendances 経由で user_id を指定
        // $requests = ApprovalRequest::whereHas('attendance', function ($query) {
        //         $query->where('user_id', Auth::id());
        //     })
        //     ->where('status', $status)
        //     ->with('attendance') // attendancesも一緒に取得
        //     ->orderByDesc('created_at')
        //     ->get();

        $requests = ApprovalRequest::whereHas('attendance', function ($query) {
            $query->where('user_id', 1); // ←固定ID
        })
        ->where('status', $status)
        ->with('attendance')
        ->orderByDesc('created_at')
        ->get();

        return view('requests.index', [
            'requests' => $requests,
        ]);
    }
}
