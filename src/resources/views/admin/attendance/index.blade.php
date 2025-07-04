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
                <tr>
                    <td>{{ $attendance->user->name }}</td>
                    <td>{{ optional($attendance->clock_in)->format('H:i') }}</td>
                    <td>{{ optional($attendance->clock_out)->format('H:i') }}</td>
                    <td>{{ $attendance->break_duration ?? '0:00' }}</td>
                    <td>{{ $attendance->worked_duration ?? '0:00' }}</td>
                    <td><a href="{{ route('admin.attendance.show', $attendance->id) }}">詳細</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection