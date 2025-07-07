@extends('layouts.admin_app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/show.css') }}?v={{ time() }}">
@endsection

@section('title', '修正申請承認・詳細画面（管理者）')

@section('content')
    <h2 class="text-center mb-4">勤怠詳細</h2>

    <div class="card mx-auto p-4" style="max-width: 600px;">
        <table class="table table-bordered">
            <tr>
                <th>名前</th>
                <td>{{ $detail['name'] }}</td>
            </tr>
            <tr>
                <th>日付</th>
                <td>{{ $detail['date'] }}</td>
            </tr>
            <tr>
                <th>出勤・退勤</th>
                <td>{{ $detail['start_time'] }} ～ {{ $detail['end_time'] }}</td>
            </tr>
            <tr>
                <th>休憩</th>
                <td>{{ $detail['break1_start'] }} ～ {{ $detail['break1_end'] }}</td>
            </tr>
            <tr>
                <th>休憩2</th>
                <td>
                    {{ $detail['break2_start'] && $detail['break2_end'] ? $detail['break2_start'].' ～ '.$detail['break2_end'] : '―' }}
                </td>
            </tr>
            <tr>
                <th>備考</th>
                <td>{{ $detail['note'] }}</td>
            </tr>
        </table>

        <div class="text-center mt-4">
            @if($detail['status'] === 'pending')
                <form action="{{ route('admin.approval.approve', $detail['id']) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-dark">承認</button>
                </form>
            @else
                <button class="btn btn-secondary" disabled>承認済み</button>
            @endif
        </div>
    </div>
@endsection
