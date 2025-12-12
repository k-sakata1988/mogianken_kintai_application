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
            <form action="{{route('logout')}}" method="post">
                @csrf
                <div class="inner_group">
                    <a class="inner_group--item" href="/attendance">勤怠</a>
                    <a class="inner_group--item" href="/attendance/list">勤怠一覧</a>
                    <a class="inner_group--item" href="/stamp_correction_request/list">申請</a>
                    <button class="inner_group--item logout-button">ログアウト</button>
                </div>
            </form>
            @endif
        </div>
    </header>

    <main>
        @yield('content')
    </main>
</body>
</html>