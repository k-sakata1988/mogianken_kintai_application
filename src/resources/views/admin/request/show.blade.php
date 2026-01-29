@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin-request-show.css') }}">
@endsection

@section('content')

<div class="attendance-detail-card">

    <h1>勤怠詳細</h1>

    <div class="detail-row">
        <div class="label">名前</div>
        <div class="value">
            {{ $attendanceRequest->attendance->user->name }}
        </div>
    </div>

    <div class="detail-row">
        <div class="label">日付</div>
        <div class="value">
            {{ $attendanceRequest->attendance->date->format('Y/m/d') }}
        </div>
    </div>

    <div class="detail-row">
        <div class="label">出勤・退勤</div>
        <div class="value">
            {{ $attendanceRequest->after_data['clock_in_time'] ?? '' }}
            〜
            {{ $attendanceRequest->after_data['clock_out_time'] ?? '' }}
        </div>
    </div>

    <div class="detail-row">
        <div class="label">休憩</div>
        <div class="value">
            {{ $attendanceRequest->after_data['break_start_1'] ?? '' }}
            @if(!empty($attendanceRequest->after_data['break_end_1']))
                〜 {{ $attendanceRequest->after_data['break_end_1'] }}
            @endif
        </div>
    </div>

    <div class="detail-row">
        <div class="label">休憩2</div>
        <div class="value">
            {{ $attendanceRequest->after_data['break_start_2'] ?? '' }}
            @if(!empty($attendanceRequest->after_data['break_end_2']))
                〜 {{ $attendanceRequest->after_data['break_end_2'] }}
            @endif
        </div>
    </div>

    <div class="detail-row">
        <div class="label">備考</div>
        <div class="value">
            {{ $attendanceRequest->reason }}
        </div>
    </div>

</div>

<div class="approve-button-wrapper">
    <form method="POST" action="{{ route('admin.request.approve', $attendanceRequest) }}">
        @csrf
        @method('PATCH')
        <button class="approve-button">
            承認
        </button>
    </form>

    <form method="POST"action="{{ route('admin.request.reject', $attendanceRequest) }}">
        @csrf
        @method('PATCH')
        <button class="reject-button">
            却下
        </button>
    </form>
</div>

@endsection
