@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/create.css') }}?v={{ time() }}">
@endsection

@section('title', '勤怠登録画面(一般ユーザー)')

@section('content')
<div>
    <!-- {{-- 日付表示 --}} -->
    @php
        $carbon = \Carbon\Carbon::now();
        $weekdays = ['日', '月', '火', '水', '木', '金', '土'];
    @endphp

    <p>現在日時: {{ $carbon->year }}年{{ $carbon->month }}月{{ $carbon->day }}日（{{ $weekdays[$carbon->dayOfWeek] }}）</p>

    <!-- {{-- 勤務ステータス表示 --}} -->
    <p>現在のステータス:
        @switch($attendance->status ?? 'off_duty')
            @case('off_duty')
                出勤前（勤務外）
                @break
            @case('working')
                出勤中（勤務中）
                @break
            @case('on_break')
                休憩中（勤務中）
                @break
            @case('finished')
                退勤済み（勤務外）
                @break
            @default
                未登録
        @endswitch
    </p>

    <!-- {{-- 勤務ステータスによるフォーム切替 --}} -->
    <div>
        @if(($attendance->status ?? 'before') === 'before')
            <form method="POST" action="{{ route('attendance.clock_in') }}">
                @csrf
                <button type="submit">出勤する</button>
            </form>

        @elseif($attendance->status === 'working')
            <form method="POST" action="{{ route('attendance.break_start', $attendance->id) }}">
                @csrf
                <button type="submit">休憩</button>
            </form>
            <form method="POST" action="{{ route('attendance.clock_out') }}">
                @csrf
                <button type="submit">退勤する</button>
            </form>

        @elseif($attendance->status === 'on_break')
            <form method="POST" action="{{ route('attendance.break_end', $attendance->id) }}">
                @csrf
                <button type="submit">休憩終了</button>
            </form>

        @elseif($attendance->status === 'finished')
            <p>本日の勤務は終了しています。</p>
            <p>お疲れ様でした。</p>
        @endif
    </div>
</div>
@endsection