<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>勤怠管理</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>
<body>

<header class="header">
    <div class="header__inner">

        {{-- ロゴ --}}
        <a class="header__logo" href="{{ Auth::check() && Auth::user()->is_admin ? route('admin.dashboard') : '/' }}">
            <img src="{{ asset('images/COACHTECHヘッダーロゴ.png') }}" alt="Coachtech Logo">
        </a>

        @auth
            @if(Auth::check() && Auth::user()->is_admin)
            <div class="inner_group">
                <a class="inner_group--item" href="{{ route('admin.dashboard') }}">勤怠一覧</a>
                <a class="inner_group--item" href="#">スタッフ一覧</a>
            <a class="inner_group--item" href="#">申請一覧</a>
            <a class="inner_group--item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            ログアウト
            </a>
        </div>
        @elseif(Auth::check())

        <div class="inner_group">
            <a class="inner_group--item" href="{{ route('user.attendance.index') }}">勤怠</a>
            <a class="inner_group--item" href="{{ route('user.attendance.list') }}">勤怠一覧</a>
            <a class="inner_group--item" href="{{ route('user.stamp_correction_request.list') }}">申請</a>
            <a class="inner_group--item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            ログアウト
            </a>
        </div>
        @endif



        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
        </form>
        @endauth

    </div>
</header>

<main>
    @yield('content')
</main>

</body>
</html>
