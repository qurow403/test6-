@extends('layouts.admin_app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}?v={{ time() }}">
@endsection

@section('title', '勤怠一覧画面(管理者)')

@section('content')
<div class="container">
    <h2>{{ $date->format('Y年n月j日') }}の勤怠</h2>

    <div class="navigation">
        <a href="{{ route('admin.attendance.index', ['date' => $date->copy()->subDay()->format('Y-m-d')]) }}">← 前日</a>
        <span>{{ $date->format('Y/m/d') }}</span>
        <a href="{{ route('admin.attendance.index', ['date' => $date->copy()->addDay()->format('Y-m-d')]) }}">翌日 →</a>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>名前</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $attendance)
                @php
                    // 休憩時間の合計（分）
                    $breakMinutes = $attendance->breaks->sum(function ($break) {
                        return ($break->break_start && $break->break_end)
                            ? \Carbon\Carbon::parse($break->break_end)->diffInMinutes($break->break_start): 0;
                    });

                    // 労働時間（出勤〜退勤 - 休憩）
                    $workedMinutes = ($attendance->clock_in && $attendance->clock_out)
                        ? \Carbon\Carbon::parse($attendance->clock_in)->diffInMinutes($attendance->clock_out) - $breakMinutes: null;
                @endphp

                <tr>
                    <td>{{ $attendance->user->name }}</td>

                    <!-- {{-- 出勤 --}} -->
                    <td>
                        {{ $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '-' }}
                    </td>

                    <!-- {{-- 退勤 --}} -->
                    <td>
                        {{ $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '-' }}
                    </td>

                    <!-- {{-- 休憩 --}} -->
                    <td>
                        {{ $breakMinutes ? floor($breakMinutes / 60) . ':' . str_pad($breakMinutes % 60, 2, '0', STR_PAD_LEFT) : '0:00' }}
                    </td>

                    <!-- {{-- 合計 --}} -->
                    <td>
                        {{ $workedMinutes !== null
                        ? floor($workedMinutes / 60) . ':' . str_pad($workedMinutes % 60, 2, '0', STR_PAD_LEFT): '0:00' }}
                    </td>

                    <td><a href="{{ route('admin.attendance.show', $attendance->id) }}">詳細</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
