@extends('user.layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
@php
    $status = $attendance->status ?? 'before_work';
@endphp

<div class="attendance">
    <div class="attendance__status">
        <span class="attendance__badge">
            {{
                $status === 'before_work' ? '勤務外' :
                ($status === 'working' ? '勤務中' :
                ($status === 'breaking' ? '休憩中' : '退勤済'))
            }}
        </span>
    </div>

    <div class="attendance__date">
        {{ now()->format('Y年n月j日(D)') }}
    </div>

    <div class="attendance__time">
        {{ now()->format('H:i') }}
    </div>

    <div class="attendance__actions">
        @if($status === 'before_work')
            <form method="POST" action="{{ route('user.attendance.clockIn') }}">
                @csrf
                <button class="attendance__button">出勤</button>
            </form>
        @endif

        @if($status === 'working')
            <form method="POST" action="{{ route('user.attendance.breakStart') }}">
                @csrf
                <button class="attendance__button">休憩入</button>
            </form>

            <form method="POST" action="{{ route('user.attendance.clockOut') }}">
                @csrf
                <button class="attendance__button">退勤</button>
            </form>
        @endif

        @if($status === 'breaking')
            <form method="POST" action="{{ route('user.attendance.breakEnd') }}">
                @csrf
                <button class="attendance__button">休憩戻</button>
            </form>
        @endif

        @if($status === 'finished')
            <p class="attendance__message">お疲れ様でした。</p>
        @endif
    </div>
</div>
@endsection