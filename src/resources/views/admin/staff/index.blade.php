@extends('layouts.admin_app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/staff/index.css') }}?v={{ time() }}">
@endsection

@section('title', 'スタッフ一覧画面(管理者)')

@section('content')
<div class="container py-5">
    <h4 class="mb-4">スタッフ一覧</h4>

    <div class="table-responsive">
        <table class="table table-bordered text-center bg-white rounded">
            <thead class="table-light">
                <tr>
                    <th>名前</th>
                    <th>メールアドレス</th>
                    <th>月次勤怠</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($staffs as $staff)
                <tr>
                    <td>{{ $staff->name }}</td>
                    <td>{{ $staff->email }}</td>
                    <td>
                        <a href="{{ route('admin.staff_attendance.index', ['id' => $staff->id]) }}">詳細</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
