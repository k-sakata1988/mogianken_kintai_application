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

    @php
    $breaks = $attendanceRequest->after_data['breaks'] ?? [];
    @endphp


    <div class="detail-row">
        <div class="label">休憩</div>
        <div class="value">
            {{ $breaks[0]['start'] ?? '' }}
            @if(!empty($breaks[0]['end']))
                〜 {{ $breaks[0]['end'] }}
            @endif
        </div>
    </div>

    <div class="detail-row">
        <div class="label">休憩2</div>
        <div class="value">
            {{ $breaks[1]['start'] ?? '' }}
            @if(!empty($breaks[1]['end']))
                〜 {{ $breaks[1]['end'] }}
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

@if($attendanceRequest->status === 'pending')
<div class="approve-button-wrapper">
    <form method="POST" action="{{ route('admin.request.approve', $attendanceRequest->id) }}">
        @csrf
        @method('PATCH')
        <button class="approve-button">
            承認
        </button>
    </form>
</div>
@else
    <div class="approve-button-wrapper">
        <div class="approved-label">
            承認済み
        </div>
    </div>
@endif
@endsection
