<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

// コントローラー
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\Auth\VerifyEmailCheckController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\RequestController as AdminRequestController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\StaffAttendanceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 認証用ルート（auth.php へ委譲）
require __DIR__.'/auth.php';

//-------------------------------------------
// 一般ユーザー向け（auth ミドルウェア）
//-------------------------------------------
Route::middleware('auth', 'verified')->group(function () {

    // 勤怠登録画面（出勤・休憩開始・休憩終了・退勤）
    Route::get('/attendance', [AttendanceController::class, 'create'])->name('attendance.create');
    Route::post('/attendance/action', [AttendanceController::class, 'handleAction'])->name('attendance.action');
    Route::post('/attendance/break-start', [AttendanceController::class, 'breakStart'])->name('attendance.break_start');
    Route::post('/attendance/break-end', [AttendanceController::class, 'breakEnd'])->name('attendance.break_end');
    Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clock_out');

    // 勤怠一覧／詳細／承認待ち
    Route::get('/attendance/list', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/pending/{id}', [AttendanceController::class, 'pending'])->name('attendance.pending');
    Route::get('/attendance/{id}', [AttendanceController::class, 'show'])->name('attendance.show');
    Route::put('/attendance/{id}', [AttendanceController::class, 'update'])->name('attendance.update');

    // 修正申請一覧（共通）
    Route::get('/stamp_correction_request/list', [RequestController::class, 'index'])->name('requests.index');
});

Route::get('/email/verify-check', [VerifyEmailCheckController::class, 'check'])
    ->middleware('auth')
    ->name('verification.verify-check');

//-------------------------------------------
// 管理者向けルート
//-------------------------------------------
Route::prefix('admin')->name('admin.')->group(function () {

    // 管理者ログイン関連
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('auth.login');
    Route::post('login', [LoginController::class, 'login']);
    Route::post('logout', [LoginController::class, 'logout'])->name('auth.logout');

    // 管理者ログイン後
    Route::middleware('auth:admin')->group(function () {

        // 勤怠一覧（全体）
        Route::get('attendance/list', [AdminAttendanceController::class, 'index'])->name('attendance.index');

        // 勤怠詳細
        Route::get('attendance/{id}', [AdminAttendanceController::class, 'show'])->name('attendance.show')->where('id', '[0-9]+');
        Route::put('attendance/{id}', [AdminAttendanceController::class, 'update'])->name('attendance.update');

        // スタッフ関連
        Route::get('staff/list', [StaffController::class, 'index'])->name('staff.index');
        Route::get('attendance/staff/{id}', [StaffAttendanceController::class, 'index'])->name('staff_attendance.index');
        Route::get('staff/attendance/{id}/csv', [StaffAttendanceController::class, 'exportCsv'])->name('staff_attendance.csv');

        // 修正申請承認
        Route::get('stamp_correction_request/approve/{attendance_correct_request}', [AdminRequestController::class, 'show'])->name('approval.show');
        Route::post('stamp_correction_request/approve/{attendance_correct_request}', [AdminRequestController::class, 'approve'])->name('approval.approve');
    });
});
