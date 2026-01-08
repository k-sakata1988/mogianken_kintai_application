@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance-list.css') }}">
@endsection

@section('content')
<div class="attendance-list">

    <h1 class="attendance-list__title">勤怠一覧</h1>

    <div class="attendance-list__month">
        <a href="?month={{ $month->copy()->subMonth()->format('Y-m') }}">← 前月</a>
        <span>{{ $month->format('Y/m') }}</span>
        <a href="?month={{ $month->copy()->addMonth()->format('Y-m') }}">翌月 →</a>
    </div>

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
                <td>{{ $date->format('m/d(D)') }}</td>
                <td>{{ optional($attendance?->clock_in_time)->format('H:i') }}</td>
                <td>{{ optional($attendance?->clock_out_time)->format('H:i') }}</td>
                <td>{{ $attendance?->total_break_time ? gmdate('H:i', $attendance->total_break_time * 60) : '' }}</td>
                <td>{{ $attendance?->total_working_time ? gmdate('H:i', $attendance->total_working_time * 60) : '' }}</td>
                <td>
                    @if($attendance)
                        <a href="#">詳細</a>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

</div>
@endsection
