<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// 管理者ログインセッションに guard('admin') を使うために追加
use Illuminate\Support\Facades\Auth;

// バリデーション適用(AdminLoginRequest.php)
use App\Http\Requests\Admin\Auth\AdminLoginRequest;

class LoginController extends Controller
{
    // ログイン画面(管理者)
    public function showLoginForm()
    {
        return view('admin.auth.login'); // 管理者ログイン画面
    }

    public function login(AdminLoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            // 認証成功時にセッションを再生成
            $request->session()->regenerate();

            return redirect()->route('admin.attendance.index');
        }

        // ログイン失敗時のエラーメッセージ
        return back()->with('error', 'ログイン情報が登録されていません')->withInput();
    }

    // ログアウト処理
    public function logout(Request $request)
    {
        auth('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.auth.login')->with('success', 'ログアウトしました');
    }
}
