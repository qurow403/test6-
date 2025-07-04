@extends('layouts.admin_app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}?v={{ time() }}">
@endsection

@section('title', 'スタッフ別勤怠一覧画面(管理者)')

@section('content')
<div class="container py-4">
    <h3 class="mb-4">{{ $user->name }}さんの勤怠</h3>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('admin.staff_attendance.index', ['id' => $user->id, 'month' => $date->copy()->subMonth()->format('Y-m')]) }}">← 前月</a>
        <div>
            <strong>{{ $date->format('Y') }}/{{ $date->format('m') }}</strong>
        </div>
        <a href="{{ route('admin.staff_attendance.index', ['id' => $user->id, 'month' => $date->copy()->addMonth()->format('Y-m')]) }}">翌月 →</a>
    </div>

    <table class="table table-striped text-center">
        <thead>
            <tr>
                <th>日付</th>
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
                <td>
                    @php
                        $weekdays = ['日', '月', '火', '水', '木', '金', '土'];
                        $w = $attendance->date->dayOfWeek;
                    @endphp
                    {{ $attendance->date->format('m/d') }}({{ $weekdays[$w] }})
                </td>
                <td>{{ $attendance->clock_in }}</td>
                <td>{{ $attendance->clock_out }}</td>
                <td>{{ $attendance->break_time }}</td>
                <td>{{ $attendance->total_time }}</td>
                <td><a href="{{ route('admin.attendance.show', ['id' => $attendance->id]) }}">詳細</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="text-end mt-4">
        <a href="{{ route('admin.staff_attendance.csv', ['id' => $user->id, 'month' => $date->format('Y-m')]) }}" class="btn btn-dark">CSV出力</a>
    </div>
</div>
@endsection
