@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin-login.css') }}">
@endsection

@section('content')
<div class="admin-login">
    <h1 class="admin-login__title">管理者ログイン</h1>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="admin-login__group">
            <label>メールアドレス</label>
            <input type="email" name="email" required>
        </div>

        <div class="admin-login__group">
            <label>パスワード</label>
            <input type="password" name="password" required>
        </div>

        <button class="admin-login__button">ログイン</button>
    </form>
</div>
@endsection
