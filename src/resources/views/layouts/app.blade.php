<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/layouts/app.css') }}?v={{ time() }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <div class="header__logo">COACHTECH</div>

            <!-- 認証済みユーザー用メニュー -->
            @auth
                <nav class="space-x-4">
                    <a href="{{ route('attendance.create') }}">勤怠</a>
                    <a href="{{ route('attendance.index') }}">勤怠一覧</a>
                    <a href="{{ route('requests.index') }}">申請</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-sm hover:underline">ログアウト</button>
                    </form>
                </nav>
            @endauth
        </div>
    </header>

    <main>
        @yield('content')
    </main>
</body>
</html>
