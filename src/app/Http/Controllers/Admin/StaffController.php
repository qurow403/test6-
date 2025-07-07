<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index()
    {
        // ダミースタッフデータ（通常はDBから取得）
        $staffs = collect([
            (object)['id' => 1, 'name' => '西 伶奈', 'email' => 'reina.n@coachtech.com'],
            (object)['id' => 2, 'name' => '山田 太郎', 'email' => 'taro.y@coachtech.com'],
            (object)['id' => 3, 'name' => '増田 一世', 'email' => 'issei.m@coachtech.com'],
            (object)['id' => 4, 'name' => '山本 敬吉', 'email' => 'keikichi.y@coachtech.com'],
            (object)['id' => 5, 'name' => '秋田 朋美', 'email' => 'tomomi.a@coachtech.com'],
            (object)['id' => 6, 'name' => '中西 教夫', 'email' => 'norio.n@coachtech.com'],
        ]);

        return view('admin.staff.index', compact('staffs'));
    }
}
