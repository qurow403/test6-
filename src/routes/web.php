<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest; // メール認証機能
use Illuminate\Support\Facades\Route;

// 追加
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

// AttendanceController(勤怠画面 登録・一覧・詳細・詳細＿承認待ち)追加
use App\Http\Controllers\AttendanceController;

// RequestController(申請一覧画面)追加
use App\Http\Controllers\RequestController;

// LoginController(管理者ログイン画面)追加
use App\Http\Controllers\Admin\Auth\LoginController;

// StaffController(スタッフ一覧画面(管理者))追加
use App\Http\Controllers\Admin\StaffController;

// 申請一覧画面（一般ユーザー・管理者） /stamp_correction_request/listのルーティング適用
use App\Http\Controllers\RequestController as UserRequestController;
use App\Http\Controllers\Admin\RequestController as AdminRequestController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// メール認証画面
// 本番では外す、一時的な設定
Route::get('/email/verify', function () {
    return view('auth.verify-email');
});

require __DIR__.'/auth.php';
// ログインできていないとhttp://localhost/loginに強制的にバックする

// 勤怠登録画面(一般ユーザー) 出勤前・出勤後・休憩中・退勤後 勤怠登録処理
Route::middleware('auth')->group(function () {
    // Route::get('/attendance', [AttendanceController::class, 'create'])->name('attendance.create');
    // Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clock_in');
    Route::post('/attendance/break-start', [AttendanceController::class, 'breakStart'])->name('attendance.break_start');
    Route::post('/attendance/break-end', [AttendanceController::class, 'breakEnd'])->name('attendance.break_end');
    Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clock_out');
});

// 勤怠登録画面(一般ユーザー)
Route::get('/attendance', [AttendanceController::class, 'create'])->name('attendance.create');
Route::post('/attendance/action', [AttendanceController::class, 'handleAction'])->name('attendance.action');
// 勤怠一覧画面(一般ユーザー)
Route::get('/attendance/list', [AttendanceController::class, 'index'])->name('attendance.index');
// 勤怠詳細画面＿承認待ち(一般ユーザー)
Route::get('/attendance/pending/{id}', [AttendanceController::class, 'pending'])->name('attendance.pending');


// 勤怠詳細一覧画面(一般ユーザー・管理者)
// Route::get('/attendance/{id}', function (Request $request, $id) {
//     if (Auth::guard('admin')->check()) {
//         // 管理者ログイン中なら管理者コントローラーに処理を委譲
//         return app(\App\Http\Controllers\Admin\AttendanceController::class)->show($id);
//     } elseif (Auth::check()) {
//         // 一般ユーザーログイン中なら一般ユーザーコントローラー
//         return app(\App\Http\Controllers\AttendanceController::class)->show($id);
//     } else {
//         // ログインしていない場合はログイン画面へ
//         return redirect()->route('login');
//     }
// })->name('attendance.show');
Route::get('/attendance/{id}', [\App\Http\Controllers\AttendanceController::class, 'show'])->name('attendance.show');
// Route::get('/attendance/{id}', [\App\Http\Controllers\Admin\AttendanceController::class, 'show'])
// ->where('id', '[0-9]+')
// ->name('admin.attendance.show');

// 勤怠詳細一覧画面(一般ユーザー・管理者)  更新ルート（PUT）
// Route::put('/attendance/{id}', function (Request $request, $id) {
//     if (Auth::guard('admin')->check()) {
//         return app(\App\Http\Controllers\Admin\AttendanceController::class)->update($request, $id);
//     } elseif (Auth::check()) {
//         return app(\App\Http\Controllers\AttendanceController::class)->update($request, $id);
//     } else {
//         return redirect()->route('login');
//     }
// })->name('attendance.update');
Route::put('/attendance/{id}', [\App\Http\Controllers\AttendanceController::class, 'update'])->name('attendance.update');
// Route::put('/attendance/{id}', [\App\Http\Controllers\Admin\AttendanceController::class, 'update'])->name('admin.attendance.update');

// 申請一覧画面(一般ユーザー・管理者)
Route::get('/stamp_correction_request/list', function (Request $request) {
    // if (Auth::guard('admin')->check()) {
    //     // 管理者ログイン中
    //     return app(AdminRequestController::class)->index();
    // } elseif (Auth::guard('web')->check()) {
    //     // 一般ユーザーログイン中
    //     return app(UserRequestController::class)->index();
    // } else {
    //     // 未ログイン（どちらでもない）
    //     return redirect('/login'); // もしくはエラーページ
    // }
    return app(\App\Http\Controllers\Admin\RequestController::class)->index($request);
})->name('admin.stamp_correction_request.index');
// ->name('stamp_correction_request.index');


// ログイン画面(管理者)
Route::get('admin/login', [\App\Http\Controllers\Admin\Auth\LoginController::class, 'showLoginForm'])->name('admin.auth.login');
Route::post('admin/login', [\App\Http\Controllers\Admin\Auth\LoginController::class, 'login']);
Route::post('admin/logout', [\App\Http\Controllers\Admin\Auth\LoginController::class, 'logout'])->name('admin.auth.logout');

// 管理者ログイン後（要ミドルウェア）
// あとで解除する
// Route:: middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('attendance/list', [App\Http\Controllers\Admin\AttendanceController::class, 'index'])->name('attendance.index'); // 勤怠一覧画面（管理者）
    Route::post('logout', [Admin\Auth\LoginController::class, 'logout'])->name('logout');
});

// 勤怠一覧画面（管理者）
Route::get('admin/attendance/list', [App\Http\Controllers\Admin\AttendanceController::class, 'index'])->name('admin.attendance.index');
//  スタッフ一覧画面（管理者）
Route::get('admin/staff/list', [App\Http\Controllers\Admin\StaffController::class, 'index'])->name('admin.staff.index');
//  スタッフ別勤怠一覧画面（管理者）
Route::get('admin/attendance/staff/{id}', [App\Http\Controllers\Admin\StaffAttendanceController::class, 'index'])->name('admin.staff_attendance.index');
// スタッフ別勤怠一覧画面（管理者）CSV出力
Route::get('admin/staff/attendance/{id}/csv', [App\Http\Controllers\Admin\StaffAttendanceController::class, 'exportCsv'])->name('admin.staff_attendance.csv');
// 修正申請承認・詳細画面（管理者）
Route::get('stamp_correction_request/approve/{attendance_correct_request}', [App\Http\Controllers\Admin\RequestController::class, 'show'])->name('admin.approval.show');
// 修正申請承認・詳細画面（管理者） 承認処理
Route::post('stamp_correction_request/approve/{attendance_correct_request}',
[App\Http\Controllers\Admin\RequestController::class, 'approve'])->name('admin.approval.approve');
