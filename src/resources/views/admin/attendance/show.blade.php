@extends('layouts.admin_app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}?v={{ time() }}">
@endsection

@section('title', '勤怠詳細画面(管理者)')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">勤怠詳細</h2>

    <form action="{{ route('admin.attendance.update', $attendance->id) }}" method="POST">
        @csrf
        @method('PUT')

        <table class="table table-bordered text-center" style="border-radius: 10px; overflow: hidden;">
            <tr>
                <th>名前</th>
                <td colspan="2">{{ $attendance->user->name ?? '未設定' }}</td>
            </tr>
            <tr>
                <th>日付</th>
                <td colspan="2">
                    {{ \Carbon\Carbon::parse($attendance->date)->format('Y年') }}
                    {{ \Carbon\Carbon::parse($attendance->date)->format('n月j日') }}
                </td>
            </tr>
            <tr>
                <th>出勤・退勤</th>
                <td>
                    <input type="time" name="clock_in" value="{{ \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') }}" required>
                </td>
                <td>
                    <input type="time" name="clock_out" value="{{ \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') }}" required>
                </td>
            </tr>
            @foreach ($attendance->breaks as $index => $break)
            <tr>
                <th>休憩{{ $index + 1 }}</th>
                <td>
                    <input type="time" name="breaks[{{ $index }}][start]" value="{{ \Carbon\Carbon::parse($break->break_start)->format('H:i') }}">
                </td>
                <td>
                    <input type="time" name="breaks[{{ $index }}][end]" value="{{ \Carbon\Carbon::parse($break->break_end)->format('H:i') }}">
                </td>
            </tr>
            @endforeach
            <!-- {{-- 空の休憩欄を1行追加 --}} -->
            <tr>
                <th>休憩{{ count($attendance->breaks) + 1 }}</th>
                <td><input type="time" name="breaks[{{ count($attendance->breaks) }}][start]"></td>
                <td><input type="time" name="breaks[{{ count($attendance->breaks) }}][end]"></td>
            </tr>

            <tr>
                <th>備考</th>
                <td colspan="2">
                    <textarea name="note" rows="2" style="width: 100%;">{{ $attendance->note }}</textarea>
                </td>
            </tr>
        </table>

        <div class="text-center mt-3">
            <button type="submit" class="btn btn-dark px-4">修正</button>
        </div>
    </form>
</div>
@endsection