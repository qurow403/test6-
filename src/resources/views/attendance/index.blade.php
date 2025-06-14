@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}?v={{ time() }}">
@endsection

@section('title', '勤怠一覧画面')

@section('content')
<div class="container">
    <h2 class="text-xl font-bold mb-4">勤怠一覧</h2>

    {{-- 月の移動と表示 --}}
    <div class="flex items-center justify-between mb-4">
        <a href="{{ route('attendance.index', ['month' => $prevMonth]) }}" class="btn btn-secondary">前月</a>
        <h3 class="text-lg font-semibold">{{ \Carbon\Carbon::parse($currentMonth)->format('Y年n月') }}</h3>
        <a href="{{ route('attendance.index', ['month' => $nextMonth]) }}" class="btn btn-secondary">翌月</a>
    </div>

    {{-- 勤怠一覧表示 --}}
    <table class="table-auto w-full text-left border-collapse border">
        <thead>
            <tr class="bg-gray-100">
                <th class="border px-4 py-2">日付</th>
                <th class="border px-4 py-2">出勤</th>
                <th class="border px-4 py-2">退勤</th>
                <th class="border px-4 py-2">休憩</th>
                <th class="border px-4 py-2">合計</th>
                <th class="border px-4 py-2">詳細</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($attendances as $attendance)
                <tr>
                    <td class="border px-4 py-2">{{ \Carbon\Carbon::parse($attendance->date)->format('Y年n月j日（'.$weekdays[\Carbon\Carbon::parse($attendance->date)->dayOfWeek].'）') }}</td>
                    <td class="border px-4 py-2">{{ $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : 'ー' }}</td>
                    <td class="border px-4 py-2">{{ $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : 'ー' }}</td>
                    <td class="border px-4 py-2">
                    @if (isset($attendance->break_duration))
                    {{ floor($attendance->break_duration / 60) }}:{{ str_pad($attendance->break_duration % 60, 2, '0', STR_PAD_LEFT) }}
                    @else
                        ー
                    @endif
                    </td>
                    <td class="border px-4 py-2">
                        @if ($attendance->clock_in && $attendance->clock_out)
                            {{ \Carbon\Carbon::parse($attendance->clock_out)->diff(\Carbon\Carbon::parse($attendance->clock_in))->format('%H:%I') }}
                        @else
                            ー
                        @endif
                    </td>
                    <td class="border px-4 py-2">
                        <a href="{{ route('attendance.show', $attendance->id) }}" class="text-blue-500 hover:underline">詳細</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-4">この月の勤怠データはありません。</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection