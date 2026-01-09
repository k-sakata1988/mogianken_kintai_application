@extends('layouts.app')

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

    <div class="attendance__date" id="current-date"></div>

    <div class="attendance__time" id="current-time"></div>


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

<script>
    function updateDateTime() {
        const now = new Date();

        const dateOptions = {
            year: 'numeric',
            month: 'numeric',
            day: 'numeric',
            weekday: 'short'
        };

        const date = now.toLocaleDateString('ja-JP', dateOptions);

        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');

        document.getElementById('current-date').textContent =
            date.replace(/\//g, '年').replace(/年(\d+)年/, '$1年').replace(/$/, '日');

        document.getElementById('current-time').textContent =
            `${hours}:${minutes}`;
    }

    updateDateTime();

    // 更新間隔
    setInterval(updateDateTime, 60 * 1000);
</script>
@endsection