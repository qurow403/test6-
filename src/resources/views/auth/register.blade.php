@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}?v={{ time() }}">
@endsection

@section('title', '会員登録画面')

@section('content')
    <div class="register-form__content">
        <div class="register-form__heading">
            <h2>会員登録</h2>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form class="register-form" action="{{ route('register') }}">
        @csrf
            <div class="register-form__group">
                <label for="name">名前</label>
                <input type="text" id="name" name="name" class="form-control"  value="{{ old('name') }}">
                @error('name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="register-form__group">
                <label for="email">メールアドレス</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}">
                @error('email')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="register-form__group">
                <label for="password">パスワード</label>
                <input type="password" id="password" name="password" class="form-control" value="{{ old('password') }}">
                @error('password')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="register-form__group">
                <label for="password_confirmation">パスワード確認</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" value="{{ old('password_confirmation') }}">
                @error('password_confirmation')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="register-form__group">
                <button type="submit" class="form-control">登録する</button>
                <a href="{{ route('login') }}">ログインはこちら</a>
            </div>

        </form>
    </div>
@endsection
