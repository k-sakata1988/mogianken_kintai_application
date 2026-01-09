@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance-detail.css') }}">
@endsection

@section('content')
<div class="attendance-detail">

    {{-- バリデーションエラー一覧 --}}
    @if ($errors->any())
    <div class="alert-error">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <h2 class="attendance-detail__title">
        <span class="attendance-detail__bar"></span>
        勤怠詳細
    </h2>

    {{-- 修正申請フォーム --}}
    <form method="POST" action="{{ route('user.attendance.request.store', $attendance) }}" class="attendance-detail__card">
        @csrf

        {{-- 名前 --}}
        <div class="detail-row">
            <div class="detail-label">名前</div>
            <div class="detail-value">
                {{ auth()->user()->name }}
            </div>
        </div>

        {{-- 日付 --}}
        <div class="detail-row">
            <div class="detail-label">日付</div>
            <div class="detail-value">
                {{ $attendance->date->format('Y年 n月j日') }}
            </div>
        </div>

        {{-- 出勤・退勤 --}}
        <div class="detail-row">
            <div class="detail-label">出勤・退勤</div>
            <div class="detail-value time-range">
                <input type="time" name="clock_in_time" value="{{ old('clock_in_time', optional($attendance->clock_in_time)->format('H:i')) }}" {{ $attendance->hasPendingRequest() ? 'disabled' : '' }}>

                <span>〜</span>

                <input type="time" name="clock_out_time" value="{{ old('clock_out_time', optional($attendance->clock_out_time)->format('H:i')) }}" {{ $attendance->hasPendingRequest() ? 'disabled' : '' }}>
            </div>

            @error('clock_in_time')
                <p class="error-text">{{ $message }}</p>
            @enderror
            @error('clock_out_time')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        {{-- 休憩（複数対応） --}}
        @foreach($attendance->breaks as $index => $break)
        <div class="detail-row">
            <div class="detail-label">
                休憩{{ $index === 0 ? '' : $index + 1 }}
            </div>

            <div class="detail-value time-range">
                <input type="time"  name="breaks[{{ $index }}][start]" value="{{ old("breaks.$index.start", optional($break->break_start)->format('H:i')) }}" {{ $attendance->hasPendingRequest() ? 'disabled' : '' }}>

                <span>〜</span>

                <input type="time" name="breaks[{{ $index }}][end]" value="{{ old("breaks.$index.end", optional($break->break_end)->format('H:i')) }}" {{ $attendance->hasPendingRequest() ? 'disabled' : '' }}>
            </div>

            @error("breaks.$index.start")
                <p class="error-text">{{ $message }}</p>
            @enderror
            @error("breaks.$index.end")
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>
        @endforeach

        {{-- 備考（修正理由） --}}
        <div class="detail-row">
            <div class="detail-label">備考</div>
            <div class="detail-value">
                <textarea name="reason"rows="3"placeholder="修正理由を入力"{{ $attendance->hasPendingRequest() ? 'disabled' : '' }}>{{ old('reason') }}</textarea>
            </div>

            @error('reason')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        {{-- ボタン --}}
        <div class="attendance-detail__actions">
            @if(!$attendance->hasPendingRequest())
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
