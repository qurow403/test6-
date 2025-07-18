@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/create.css') }}?v={{ time() }}">
@endsection

@section('title', '勤怠登録画面(一般ユーザー)')

@section('content')
<div class="container text-center py-5">
    <p class="status-label">
        @switch($status)
            @case('before')
                <span class="badge bg-secondary">勤務外</span>
                @break
            @case('working')
                <span class="badge bg-primary">出勤中</span>
                @break
            @case('on_break')
                <span class="badge bg-warning">休憩中</span>
                @break
            @case('finished')
                <span class="badge bg-success">退勤済み</span>
                @break
        @endswitch
    </p>

    <h2>{{ $now->format('Y年n月j日 (D)') }}</h2>
    <h1>{{ $now->format('H:i') }}</h1>

    <form method="POST" action="{{ route('attendance.action') }}">
        @csrf
        @switch($status)
            @case('before')
                <button type="submit" name="action" value="clock_in" class="btn btn-dark mt-4 px-5">出勤</button>
                @break
            @case('working')
                <button type="submit" name="action" value="clock_out" class="btn btn-dark mt-4 me-2 px-4">退勤</button>
                <button type="submit" name="action" value="break_in" class="btn btn-outline-dark mt-4 px-4">休憩入</button>
                @break
            @case('on_break')
                <button type="submit" name="action" value="break_out" class="btn btn-outline-dark mt-4 px-4">休憩戻</button>
                @break
            @case('finished')
                <p class="mt-4 fs-4">お疲れ様でした。</p>
                @break
        @endswitch
    </form>
</div>
@endsection
