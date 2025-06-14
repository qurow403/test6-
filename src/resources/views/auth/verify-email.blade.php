@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/verify-email.css') }}?v={{ time() }}">
@endsection

@section('title', 'メール認証誘導画面')

@section('content')
<div class="verify-container">
    <p class="verify-message">
        登録していただいたメールアドレスに認証メールを送付しました。<br>
        メール認証を完了してください。
    </p>

    <div class="verify-button">
        <a href="/" class="btn">認証はこちらから</a>
    </div>

    <div class="resend-link">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="resend-btn">認証メールを再送する</button>
        </form>
    </div>
</div>
@endsection

