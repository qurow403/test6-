<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// モデル追加
use App\Models\User;

class StaffController extends Controller
{
    // スタッフ一覧画面(管理者)
    public function index()
    {
        $staffs =  User::all();
        return view('admin.staff.index', compact('staffs'));
    }
}
