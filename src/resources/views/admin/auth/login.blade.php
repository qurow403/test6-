@extends('layouts.admin_app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ time() }}">
@endsection

@section('title', 'ログイン画面(管理者)')

@section('content')
    <div class="login-form__content">
        <div class="login-form__heading">
            <h2>管理者ログイン</h2>
        </div>

        <!-- {{-- ✅ 会員登録後の成功メッセージ表示 --}} -->
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- {{-- ❌ ログイン失敗などのエラーメッセージ --}} -->
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <form class="login-form" method="POST" action="{{ route('admin.auth.login') }}">
        @csrf
            <div class="login-form__group">
                <label for="email">メールアドレス</label>
                <input type="email" id="email" name="email" class="form-control"  value="{{ old('email') }}">
                @error('email')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="login-form__group">
                <label for="password">パスワード</label>
                <input type="password" id="password" name="password" class="form-control">
                @error('password')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="login-form__group">
            <button type="submit" class="form-control">管理者ログインする</button>
        </form>
    </div>
@endsection