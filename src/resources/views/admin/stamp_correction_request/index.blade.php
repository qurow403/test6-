@extends('layouts.admin_app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}?v={{ time() }}">
@endsection

@section('title', '申請一覧画面(管理者)')

@section('content')
    <h2>申請一覧</h2>

    {{-- タブ切り替え --}}
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link {{ request('status') !== 'approved' ? 'active' : '' }}"
                href="{{ route('admin.stamp_correction_request.index', ['status' => 'pending']) }}">
                承認待ち
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request('status') === 'approved' ? 'active' : '' }}"
                href="{{ route('admin.stamp_correction_request.index', ['status' => 'approved']) }}">
                承認済み
            </a>
        </li>
    </ul>

    <table class="table">
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
            @forelse(($status === 'approved' ? $approved : $pending) as $item)
            <tr>
                <td>{{ $item['status'] }}</td>
                <td>{{ $item['name'] }}</td>
                <td>{{ $item['target_date'] }}</td>
                <td>{{ $item['reason'] }}</td>
                <td>{{ $item['applied_at'] }}</td>
                <td><a href="{{ route('admin.approvals.show', $item['id']) }}">詳細</a></td>
            </tr>
            @empty
            <tr>
                <td colspan="6">表示できる申請はありません。</td>
            </tr>
            @endforelse
        </tbody>
    </table>
@endsection
