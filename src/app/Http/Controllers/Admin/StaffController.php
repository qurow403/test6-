<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;


class StaffController extends Controller
{
    // スタッフ一覧画面(管理者)
    public function index()
    {
        // 一般ユーザーのみ取得（名前順）
        $staffs =  User::where('role', 'user')
            ->orderBy('name')
            ->get();

        return view('admin.staff.index', compact('staffs'));
    }
}
