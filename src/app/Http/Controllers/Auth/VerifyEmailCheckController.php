<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // 追加

class VerifyEmailCheckController extends Controller
{
    // メール認証後
    public function check()
    {
        if (Auth::user()->hasVerifiedEmail()) {
            // 認証済みなら勤怠ページへ
            return redirect()->route('attendance.create');
        }

        // 未認証なら元に戻す
        return redirect()->route('verification.notice')->with('error', 'まだメール認証が完了していません。');
    }
}
