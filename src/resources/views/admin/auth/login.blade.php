@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin-login.css') }}">
@endsection

@section('content')
<div class="admin-login__content">
    <div class="admin-login__heading">
        <h1>管理者ログイン</h1>
    </div>

    <form class="admin-form" method="POST" action="{{ route('login') }}">
        @csrf
        <input type="hidden" name="login_type" value="admin">

        <div class="admin-form__group">
            <div class="admin-form__group-title">
                <label>メールアドレス</label>
            </div>
            <div class="admin-form__input--text">
                <input type="email" name="email" value="{{ old('email') }}">
            </div>
            @error('email')
                <div class="form__error">{{ $message }}</div>
            @enderror
        </div>

        <div class="admin-form__group">
            <div class="admin-form__group-title">
                <label>パスワード</label>
            </div>
            <div class="admin-form__input--text">
                <input type="password" name="password">
            </div>
            @error('password')
                <div class="form__error">{{ $message }}</div>
            @enderror
        </div>

        <div class="admin-form__button">
            <button class="admin-form__button-submit">ログイン</button>
        </div>
    </form>
</div>
@endsection
