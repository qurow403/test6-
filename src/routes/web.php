<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest; // メール認証機能
use Illuminate\Support\Facades\Route;

// AttendanceController(勤怠画面 登録・一覧・詳細・詳細＿承認待ち)追加
use App\Http\Controllers\AttendanceController;

// RequestController(申請一覧画面)追加
use App\Http\Controllers\RequestController;

// LoginController(管理者ログイン画面)追加
use App\Http\Controllers\Admin\Auth\LoginController;

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


// 勤怠登録画面(一般ユーザー)
Route::get('/attendance', [AttendanceController::class, 'create'])->name('attendance.create');
// 勤怠一覧画面(一般ユーザー)
Route::get('/attendance/list', [AttendanceController::class, 'index'])->name('attendance.index');
// 勤怠詳細画面(一般ユーザー)
Route::get('/attendance/{id}', [AttendanceController::class, 'show'])->name('attendance.show');
Route::put('/attendance/{id}', [AttendanceController::class, 'update'])->name('attendance.update');
// 勤怠詳細画面＿承認待ち(一般ユーザー)
Route::get('/attendance/pending/{id}', [AttendanceController::class, 'pending'])->name('attendance.pending');

// 申請一覧画面(一般ユーザー)
Route::get('/stamp_correction_request/list', [RequestController::class, 'index'])->name('requests.index');


Route::middleware('auth')->group(function () {
    Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clock_in');
    Route::post('/attendance/break-start', [AttendanceController::class, 'breakStart'])->name('attendance.break_start');
    Route::post('/attendance/break-end', [AttendanceController::class, 'breakEnd'])->name('attendance.break_end');
    Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clock_out');
    // Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
});


// ログイン画面(管理者)
Route::get('admin/login', [\App\Http\Controllers\Admin\Auth\LoginController::class, 'showLoginForm'])->name('admin.auth.login');
Route::post('admin/login', [\App\Http\Controllers\Admin\Auth\LoginController::class, 'login']);
Route::post('admin/logout', [\App\Http\Controllers\Admin\Auth\LoginController::class, 'logout'])->name('admin.auth.logout');

// 管理者ログイン後（要ミドルウェア）
// あとで解除する
// Route:: middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('attendance/list', [App\Http\Controllers\Admin\AttendanceController::class, 'index'])->name('attendance.index'); // 勤怠一覧画面（管理者）
    Route::get('stamp-correction-requests', [Admin\RequestController::class, 'index'])->name('stamp_correction_requests.index');
    Route::post('logout', [Admin\Auth\LoginController::class, 'logout'])->name('logout');
});

// 勤怠一覧画面（管理者）
Route::get('admin/attendance/list', [App\Http\Controllers\Admin\AttendanceController::class, 'index'])->name('admin.attendance.index');
// 勤怠詳細画面（管理者）
Route::get('attendance/{id}', [App\Http\Controllers\Admin\AttendanceController::class, 'show'])->name('admin.attendance.show');
Route::put('attendance/{id}', [App\Http\Controllers\Admin\AttendanceController::class, 'update'])->name('admin.attendance.update');
//  スタッフ別勤怠一覧画面（管理者）
Route::get('admin/attendance/staff/{id}', [App\Http\Controllers\Admin\StaffAttendanceController::class, 'index'])->name('admin.staff_attendance.index');
// スタッフ別勤怠一覧画面（管理者）CSV出力
Route::get('admin/staff/attendance/{id}/csv', [App\Http\Controllers\Admin\StaffAttendanceController::class, 'exportCsv'])->name('admin.staff_attendance.csv');
//  スタッフ一覧画面（管理者）
Route::get('admin/attendance/staff/list', [App\Http\Controllers\Admin\StaffAttendanceController::class, 'index'])->name('admin.staff_attendance.index');
