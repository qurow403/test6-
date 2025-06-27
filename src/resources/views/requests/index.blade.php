@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}?v={{ time() }}">
@endsection

@section('title', '申請一覧画面')

@section('content')
<div class="container">
    <h1 class="title">申請一覧</h1>

    {{-- タブ切り替え --}}
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link {{ request('status') !== 'approved' ? 'active' : '' }}"
               href="{{ route('admin.requests.index', ['status' => 'pending']) }}">承認待ち</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request('status') === 'approved' ? 'active' : '' }}"
               href="{{ route('admin.requests.index', ['status' => 'approved']) }}">承認済み</a>
        </li>
    </ul>

    <table class="table table-bordered bg-white rounded">
        <thead>
            <tr>
                <th>状態</th>
                <th>名前</th>
                <th>対象日時</th>
                <th>申請理由</th>
                <th>申請日時</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($requests as $request)
                <tr>
                    <td>{{ $request->status === 'pending' ? '承認待ち' : '承認済み' }}</td>
                    <td>{{ $request->user->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($request->attendance->date)->format('Y/m/d') }}</td>
                    <td>{{ $request->note }}</td>
                    <td>{{ \Carbon\Carbon::parse($request->created_at)->format('Y/m/d') }}</td>
                    <td>
                        <a href="{{ route('admin.requests.show', ['id' => $request->id]) }}">詳細</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">該当する申請はありません。</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection