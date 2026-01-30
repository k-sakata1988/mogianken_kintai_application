@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
@endsection

@section('content')
<div class="admin-attendance-wrapper">
    <div class="admin-attendance">
        <h1 class="admin-attendance__title">
        {{ $date->format('Y年n月j日') }}の勤怠
        </h1>
        <div class="admin-attendance__date-card">
            <a class="date-nav__prev" href="{{ route('admin.dashboard', ['date' => $date->copy()->subDay()->toDateString()]) }}">
            ← 前日
            </a>

            <div class="date-nav__center">
                <span class="date-icon">📅</span>
                <span>{{ $date->format('Y/m/d') }}</span>
            </div>

            <a class="date-nav__next" href="{{ route('admin.dashboard', ['date' => $date->copy()->addDay()->toDateString()]) }}">
            翌日 →
            </a>
        </div>

        <table class="admin-attendance__table">
            <thead>
                <tr>
                    <th>名前</th>
                    <th>出勤</th>
                    <th>退勤</th>
                    <th>休憩</th>
                    <th>合計</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($attendances as $attendance)
                    <tr>
                        <td>{{ $attendance->user->name }}</td>
                        <td>{{ optional($attendance->clock_in_time)->format('H:i') }}</td>
                        <td>{{ optional($attendance->clock_out_time)->format('H:i') }}</td>
                        <td>{{ $attendance->formatted_break_time }}</td>
                        <td>{{ $attendance->total_working_time ?? '0:00' }}</td>
                        <td>
                            <a href="{{ route('admin.attendance.show', $attendance->id) }}">
                                詳細
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">勤怠データがありません</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
