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
        // $credentials = $request->validate([
        //     'email' => ['required', 'email'],
        //     'password' => ['required'],
        // ]);

        // if (auth()->guard('admin')->attempt($credentials)) {
        //     $request->session()->regenerate();
        //     return redirect()->intended(route('admin.attendance.index')); // 管理者TOPなどに
        // }

        // return back()->withErrors([
        //     'email' => 'メールアドレスまたはパスワードが正しくありません。',
        // ])->onlyInput('email');

        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            return redirect()->route('admin.stamp_correction_request.index');
        }

        // ログイン失敗時のエラーメッセージ
        return back()->withErrors([
            'email' => 'ログイン情報が登録されていません',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        auth('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.auth.login')->with('success', 'ログアウトしました');
    }
}
