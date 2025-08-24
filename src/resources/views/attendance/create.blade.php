@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance/create.css') }}?v={{ time() }}">
@endsection

@section('title', '勤怠登録画面(一般ユーザー)')

@section('content')
<div class="container">
    <p class="status-label {{ $status }}">
        @switch($status)
            @case('before')
                勤務外
                @break
            @case('working')
                出勤中
                @break
            @case('on_break')
                休憩中
                @break
            @case('finished')
                退勤済み
                @break
        @endswitch
    </p>

    @php
        $weekdays = ['日', '月', '火', '水', '木', '金', '土'];
        $weekday = $weekdays[$now->dayOfWeek]; // Carbonの日番号(0=日曜～6=土曜)
    @endphp

    <h2 class="date-label">
        {{ $now->format('Y年n月j日') }} ({{ $weekday }})
    </h2>
    <h1 class="time-label">{{ $now->format('H:i') }}</h1>

    <form method="POST" action="{{ route('attendance.action') }}">
        @csrf
        @switch($status)
            @case('before')
                <button type="submit" name="action" value="clock_in" class="btn-attendance">出勤</button>
                @break
            @case('working')
                <button type="submit" name="action" value="clock_out" class="btn-attendance">退勤</button>
                <button type="submit" name="action" value="break_in" class="btn-outline-attendance">休憩入</button>
                @break
            @case('on_break')
                <button type="submit" name="action" value="break_out" class="btn-outline-attendance">休憩戻</button>
                @break
            @case('finished')
                <p class="finish-message">お疲れ様でした。</p>
                @break
        @endswitch
    </form>
</div>
@endsection
