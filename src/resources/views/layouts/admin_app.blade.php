<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/layouts/admin_app.css') }}?v={{ time() }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <div class="header__logo">COACHTECH</div>

            <!-- 未ログイン時は右端にロゴだけ。ログイン後にナビメニュー左配置 -->
            @auth('admin')
                <nav class="absolute left-4 top-4 flex space-x-4">
                    <a href="{{ route('admin.attendance.index') }}" class="text-sm hover:underline">勤怠一覧</a>
                    <a href="{{ route('admin.staff.index') }}" class="text-sm hover:underline">スタッフ一覧</a>
                    <a href="{{ route('admin.stamp_correction_request.index') }}" class="text-sm hover:underline">申請一覧</a>
                    <form method="POST" action="{{ route('admin.auth.logout') }}" class="inline">
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
