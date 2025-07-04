@extends('layouts.app')

@section('title', '勤怠詳細画面_承認待ち(一般ユーザー)')

@section('content')
<div class="container">
    <h1 class="title">勤怠詳細</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="form-group">
        <label>名前</label>
        <p>{{ $attendance->user_name ?? '未設定' }}</p>
    </div>

    <div class="form-group">
        <label>日付:</label>
        <p>{{ $attendance->date ?? '未設定' }}</p>
    </div>

    <div class="form-group">
        <label>出勤・退勤</label>
        <p>
            {{ $attendance->clock_in ?? '未入力' }} 〜 {{ $attendance->clock_out ?? '未入力' }}
        </p>
    </div>

    <div class="form-group">
        <label>休憩時間:</label>
        @forelse ($attendance->breaks as $index => $break)
            <p>
                休憩{{ $index + 1 }}：{{ $break['start'] ?? '-' }} ～ {{ $break['end'] ?? '-' }}
            </p>
        @empty
            <p>休憩なし</p>
        @endforelse
    </div>

    <div class="form-group">
        <label>備考:</label>
        <p>{{ $attendance->note ?? 'なし' }}</p>
    </div>

    <div class="form-group">
        <p class="text-muted">{{ $attendance->message ?? '' }}</p>
    </div>

    <a href="{{ route('attendance.index') }}" class="btn btn-secondary mt-3">一覧に戻る</a>
</div>
@endsection