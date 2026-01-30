@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance-detail.css') }}">
@endsection

@section('content')
<div class="attendance-detail">

    <h2 class="attendance-detail__title">
        <span class="attendance-detail__bar"></span>
        勤怠詳細
    </h2>

    @php
        // 申請中の修正申請（あれば）
        $pendingRequest = $attendance->requests()
            ->where('status', 'pending')
            ->latest()
            ->first();
    @endphp

    <form method="POST"
          action="{{ route('user.attendance.request.store', $attendance) }}"
          class="attendance-detail__card">
        @csrf

        <div class="detail-row">
            <div class="detail-label">名前</div>
            <div class="detail-value">
                {{ auth()->user()->name }}
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">日付</div>
            <div class="detail-value">
                {{ $attendance->date->format('Y年 n月j日') }}
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">出勤・退勤</div>
            <div class="detail-value time-range">
                <input type="time"
                       name="clock_in_time"
                       value="{{ old('clock_in_time', optional($attendance->clock_in_time)->format('H:i')) }}"
                       {{ $pendingRequest ? 'disabled' : '' }}>

                <span>〜</span>

                <input type="time"
                       name="clock_out_time"
                       value="{{ old('clock_out_time', optional($attendance->clock_out_time)->format('H:i')) }}"
                       {{ $pendingRequest ? 'disabled' : '' }}>
            </div>
        </div>

        @php
            $breaks = $attendance->breaks->take(2)->values();
            for ($i = $breaks->count(); $i < 2; $i++) {
                $breaks->push(null);
            }
        @endphp

        @foreach($breaks as $index => $break)
        <div class="detail-row">
            <div class="detail-label">
                休憩{{ $index === 0 ? '' : $index + 1 }}
            </div>

            <div class="detail-value time-range">
                <input type="time"
                       name="breaks[{{ $index }}][start]"
                       value="{{ optional($break?->break_start)->format('H:i') }}"
                       {{ $pendingRequest ? 'disabled' : '' }}>

                <span>〜</span>

                <input type="time"
                       name="breaks[{{ $index }}][end]"
                       value="{{ optional($break?->break_end)->format('H:i') }}"
                       {{ $pendingRequest ? 'disabled' : '' }}>
            </div>
        </div>
        @endforeach

        <div class="detail-row">
            <div class="detail-label">備考</div>
            <div class="detail-value">
                @if($pendingRequest)
                    {{ $pendingRequest->reason }}
                @else
                    <textarea name="reason"
                              rows="3"
                              placeholder="修正理由を入力"></textarea>
                @endif
            </div>
        </div>

        <div class="attendance-detail__actions">
            @if(!$pendingRequest)
                <button class="btn-black" type="submit">
                    修正申請
                </button>
            @else
                <p class="pending-text">
                    ※ 承認待ちのため修正できません
                </p>
            @endif
        </div>

    </form>
</div>
@endsection
