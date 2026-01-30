@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin-staff-attendance.css') }}">
@endsection

@section('content')
<div class="admin-staff-attendance">

    <div class="admin-title">
        <span class="bar"></span>
        <h1>{{ $user->name }}さんの勤怠</h1>
    </div>

    <div class="month-switch">
        <a href="?month={{ \Carbon\Carbon::parse($month)->subMonth()->format('Y-m') }}">
            ← 前月
        </a>

        <div class="current-month">
            {{ \Carbon\Carbon::parse($month)->format('Y年m月') }}
        </div>

        <a href="?month={{ \Carbon\Carbon::parse($month)->addMonth()->format('Y-m') }}">
            翌月 →
        </a>
    </div>

    <div class="attendance-card">
        <table class="attendance-table">
            <thead>
                <tr>
                    <th>日付</th>
                    <th>出勤</th>
                    <th>退勤</th>
                    <th>休憩</th>
                    <th>合計</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dates as $date)
                    @php
                        $attendance = $attendances[$date->format('Y-m-d')] ?? null;
                    @endphp
                    <tr>
                        <td>
                            {{ $date->format('m/d') }}
                            ({{ $date->isoFormat('ddd') }})
                        </td>
                        <td>{{ $attendance?->clock_in_time?->format('H:i') ?? '' }}</td>
                        <td>{{ $attendance?->clock_out_time?->format('H:i') ?? '' }}</td>
                        <td>{{ $attendance?->total_break_time ? gmdate('H:i', $attendance->total_break_time * 60) : '' }}</td>
                        <td>{{ $attendance?->total_working_time ? gmdate('H:i', $attendance->total_working_time * 60) : '' }}</td>
                        <td>
                            @if($attendance)
                                <a href="{{ route('admin.attendance.show', $attendance->id) }}">詳細</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="csv-area">
        <form method="GET" action="{{ route('admin.staff.attendance.csv', $user->id) }}">
            <input type="hidden" name="month" value="{{ $month }}">
            <button type="submit" class="btn-csv">CSV出力</button>
        </form>
    </div>

</div>
@endsection
