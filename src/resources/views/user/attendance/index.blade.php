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
                ($status === 'working' ? '出勤中' :
                ($status === 'breaking' ? '休憩中' : '退勤済'))
            }}
        </span>
    </div>

    <div class="attendance__date" id="current-date">
        {{ now()->formatLocalized('%Y年%-m月%-d日(%a)') }}
    </div>
    <div class="attendance__time" id="current-time">
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

<script>
function updateDateTime() {
    const now = new Date();

    const dateOptions = { year: 'numeric', month: 'numeric', day: 'numeric', weekday: 'short' };
    const dateStr = now.toLocaleDateString('ja-JP', dateOptions); // 例: "2026/1/30(金)"

    const [year, month, dayWithWeek] = dateStr.split('/');
    const dayParts = dayWithWeek.split('('); // ["30", "金)"]
    const formattedDate = `${year}年${month}月${dayParts[0]}日(${dayParts[1]}`;

    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');

    document.getElementById('current-date').textContent = formattedDate;
    document.getElementById('current-time').textContent = `${hours}:${minutes}`;
}

updateDateTime();

setInterval(updateDateTime, 60 * 1000);
</script>
@endsection
