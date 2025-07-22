<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// 時間表記で追加
use Illuminate\Support\Carbon;

// ApprovalRequest(承認申請)モデル追加
use App\Models\ApprovalRequest;

class RequestController extends Controller
{
    // 申請一覧画面（一般ユーザー）
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending'); // デフォルト: pending

        // 現在のログインユーザーの申請のみ取得（最新順）
        $requests = ApprovalRequest::with(['user', 'attendance'])
            ->where('user_id', Auth::id())
            ->where('status', $status)
            ->orderByDesc('created_at')
            ->get();

        return view('requests.index', [
            'requests' => $requests,
        ]);
    }
}
