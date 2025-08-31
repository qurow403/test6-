@extends('layouts.admin_app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/staff_attendance/index.css') }}?v={{ time() }}">
@endsection

@section('title', 'スタッフ別勤怠一覧画面(管理者)')

@section('content')
<div class="container py-4">
    <h3 class="mb-4">{{ $user->name }}さんの勤怠</h3>

    <div class="month-nav d-flex align-items-center mb-3">
        <div class="prev">
            <a href="{{ route('admin.staff_attendance.index', ['id' => $user->id, 'month' => $date->copy()->subMonth()->format('Y-m')]) }}">← 前月</a>
        </div>
        <div class="current">
            <strong>{{ $date->format('Y') }}/{{ $date->format('m') }}</strong>
        </div>
        <div class="next">
            <a href="{{ route('admin.staff_attendance.index', ['id' => $user->id, 'month' => $date->copy()->addMonth()->format('Y-m')]) }}">翌月 →</a>
        </div>
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
        @forelse($attendances as $attendance)
                @php
                    $weekdays = ['日', '月', '火', '水', '木', '金', '土'];
                    $w = $attendance->date->dayOfWeek;
                @endphp

                <tr>
                    <td>{{ $attendance->date->format('m/d') }} ({{ $weekdays[$w] }})</td>
                    <td>{{ $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : 'ー' }}</td>
                    <td>{{ $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : 'ー' }}</td>
                    <td>{{ $attendance->break_time ?? '0:00' }}</td>
                    <td>{{ $attendance->total_time ?? '0:00' }}</td>

                <td>
                    @if ($attendance->id)
                        <a href="{{ route('admin.attendance.show', ['id' => $attendance->id]) }}">詳細</a>
                    @else
                        ー
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center">データがありません。</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="text-end mt-4">
        <a href="{{ route('admin.staff_attendance.csv', ['id' => $user->id, 'month' => $date->format('Y-m')]) }}" class="btn btn-dark">CSV出力</a>
    </div>
</div>
@endsection
