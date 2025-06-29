<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>

</head>
<body>
    <header class="">
        <!-- ロゴ（常に表示） -->
        <div>
            <a href="{{ url('/admin') }}">
                <img src="{{ asset('images/logo.png') }}" alt="ロゴ" class="h-8">
            </a>
        </div>

        <!-- 未ログイン時は右端にロゴだけ。ログイン後にナビメニュー左配置 -->
        @auth('admin')
            <nav class="absolute left-4 top-4 flex space-x-4">
                <a href="{{ route('admin.attendances.index') }}" class="text-sm hover:underline">勤怠一覧</a>
                <a href="{{ route('admin.staffs.index') }}" class="text-sm hover:underline">スタッフ一覧</a>
                <a href="{{ route('admin.stamp_correction_requests.index') }}" class="text-sm hover:underline">申請一覧</a>
                <form method="POST" action="{{ route('admin.auth.logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-sm hover:underline">ログアウト</button>
                </form>
            </nav>
        @endauth
    </header>

    <main>
        @yield('content')
    </main>
</body>
</html>