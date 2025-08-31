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

        $requests = ApprovalRequest::whereHas('attendance', function ($query) {
            $query->where('user_id', 1);
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
