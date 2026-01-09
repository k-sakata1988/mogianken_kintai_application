<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>勤怠管理</title>
    <link rel="stylesheet" href="{{asset('css/sanitize.css')}}">
    <link rel="stylesheet" href="{{asset('css/common.css')}}">
    @yield('css')
</head>
<body>
    <header class="header">
        <div class="header__inner">
            <a class="header__logo" href="/">
                <img src="{{ asset('images/COACHTECHヘッダーロゴ.png') }}" alt="Coachtech Logo">
            </a>
            @if(Auth::check())
            <div class="inner_group">
                <a class="inner_group--item" href="{{ route('user.attendance.index') }}">
                    勤怠
                </a>

                <a class="inner_group--item" href="{{ route('user.attendance.list') }}">
                    勤怠一覧
                </a>

                <a class="inner_group--item" href="{{ route('user.stamp_correction_request.list') }}">
                    申請
                </a>

                {{-- ログアウト --}}
                <a class="inner_group--item"href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    ログアウト
                </a>

                <form id="logout-form"action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
                </form>
            </div>
            @endif
        </div>
    </header>

    <main>
        @yield('content')
    </main>
</body>
</html>