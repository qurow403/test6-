@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance/show.css') }}?v={{ time() }}">
@endsection

@section('title', '勤怠詳細画面(一般ユーザー)')

@section('content')
<div class="container">
    <h2 class="title">勤怠詳細</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('attendance.update', $attendance->id) }}" method="POST">
        @csrf
        @method('PUT')

        <table class="table">
            <tr>
                <th>名前</th>
                <td>{{ $attendance->user->name ?? '―' }}</td>
            </tr>
            <tr>
                <th>日付</th>
                <td>{{ \Carbon\Carbon::parse($attendance->date)->format('Y年n月j日') }}</td>
            </tr>
            <tr>
                <th>出勤・退勤</th>
                <td>
                    <input type="time" name="clock_in" value="{{ old('clock_in', $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '') }}">
                    〜
                    <input type="time" name="clock_out" value="{{ old('clock_out', $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '') }}">
                </td>
            </tr>
            <tr>
                <th>休憩</th>
                <td>
                    <input type="time" name="breaks[0][start]"
                        value="{{ old('breaks.0.start', isset($attendance->breaks[0]) && $attendance->breaks[0]->break_start ? \Carbon\Carbon::parse($attendance->breaks[0]->break_start)->format('H:i') : '') }}">
                    〜
                    <input type="time" name="breaks[0][end]"
                        value="{{ old('breaks.0.end', isset($attendance->breaks[0]) && $attendance->breaks[0]->break_end ? \Carbon\Carbon::parse($attendance->breaks[0]->break_end)->format('H:i') : '') }}">
                </td>
            </tr>
            <tr>
                <th>休憩2</th>
                <td>
                    <input type="time" name="breaks[1][start]"
                        value="{{ old('breaks.1.start', isset($attendance->breaks[1]) && $attendance->breaks[1]->break_start ? \Carbon\Carbon::parse($attendance->breaks[1]->break_start)->format('H:i') : '') }}">
                    〜
                    <input type="time" name="breaks[1][end]"
                        value="{{ old('breaks.1.end', isset($attendance->breaks[1]) && $attendance->breaks[1]->break_end ? \Carbon\Carbon::parse($attendance->breaks[1]->break_end)->format('H:i') : '') }}">
                </td>
            </tr>
            <tr>
                <th>備考</th>
                <td>
                    <textarea name="note">{{ old('note', $attendance->note ?? '') }}</textarea>
                </td>
            </tr>
        </table>

        <div class="text-center mt-4">
            <button type="submit" class="btn btn-dark">修正</button>
        </div>
    </form>
</div>
@endsection
