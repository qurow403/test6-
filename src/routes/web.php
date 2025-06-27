<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest; // メール認証機能
use Illuminate\Support\Facades\Route;

// AttendanceController追加
use App\Http\Controllers\Admin\AttendanceController;

// RequestController(申請一覧画面)追加
use App\Http\Controllers\RequestController;

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


// 本番では外す、一時的な設定
Route::get('/email/verify', function () {
    return view('auth.verify-email');
}); // メール認証
Route::get('/attendance', [App\Http\Controllers\Admin\AttendanceController::class, 'create'])->name('attendance.create'); // 勤怠登録

require __DIR__.'/auth.php';
// ログインできていないとhttp://localhost/loginに強制的にバックする


// 勤怠一覧画面
Route::get('/attendance/list', [AttendanceController::class, 'index'])->name('attendance.index');
// 勤怠詳細画面
Route::get('/attendance/{id}', [AttendanceController::class, 'show'])->name('attendance.show');
Route::put('/attendance/{id}', [AttendanceController::class, 'update'])->name('attendance.update');
// 勤怠詳細画面＿承認待ち
Route::get('/attendance/pending/{id}', [AttendanceController::class, 'pending'])->name('attendance.pending');

// 申請一覧画面
Route::get('/stamp_correction_request/list', [RequestController::class, 'index'])->name('requests.index');


Route::middleware('auth')->group(function () {
    Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clock_in');
    Route::post('/attendance/break-start', [AttendanceController::class, 'breakStart'])->name('attendance.break_start');
    Route::post('/attendance/break-end', [AttendanceController::class, 'breakEnd'])->name('attendance.break_end');
    Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clock_out');
    // Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
});


// 管理者ログイン前
Route::get('admin/login', [Admin\Auth\LoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('admin/login', [Admin\Auth\LoginController::class, 'login']);

// 管理者ログイン後（要ミドルウェア）
Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('attendances', [Admin\AttendanceController::class, 'index'])->name('attendances.index');
    Route::get('staffs', [Admin\StaffController::class, 'index'])->name('staffs.index');
    Route::get('stamp-correction-requests', [Admin\RequestController::class, 'index'])->name('stamp_correction_requests.index');
    Route::post('logout', [Admin\Auth\LoginController::class, 'logout'])->name('logout');
});