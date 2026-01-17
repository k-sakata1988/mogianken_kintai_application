@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin-detail.css') }}">
@endsection

@section('content')

<div class="admin-attendance-detail">

    <div class="admin-attendance-detail__title">
        <div class="admin-attendance-detail__bar"></div>
        <h1>勤怠詳細</h1>
    </div>

    <form method="POST" action="{{ route('admin.attendance.update', $attendance->id) }}">
        @csrf
        @method('PATCH')
        @if ($errors->any())
        <div class="admin-detail-errors">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="admin-attendance-detail__card">

            <div class="admin-detail-row">
                <div class="admin-detail-label">名前</div>
                <div class="admin-detail-value">
                    {{ optional($attendance->user)->name ?? '未設定' }}
                </div>
            </div>

            <div class="admin-detail-row">
                <div class="admin-detail-label">日付</div>
                <div class="admin-detail-value">
                    {{ optional($attendance->date)->format('Y年m月d日') }}
                </div>
            </div>

            <div class="admin-detail-row">
                <div class="admin-detail-label">出勤・退勤</div>
                <div class="admin-detail-value admin-time-range">
                    <input type="time" name="clock_in_time" value="{{ optional($attendance->clock_in_time)->format('H:i') }}">
                    <span>〜</span>
                    <input type="time" name="clock_out_time" value="{{ optional($attendance->clock_out_time)->format('H:i') }}">
                </div>
            </div>

            <div class="admin-detail-row">
                <div class="admin-detail-label">休憩</div>
                <div class="admin-detail-value admin-time-range">
                    <input type="time" name="break_start" value="{{ optional($latestBreak?->break_start)->format('H:i') }}">
                    <span>〜</span>
                    <input type="time" name="break_end" value="{{ optional($latestBreak?->break_end)->format('H:i') }}">
                </div>
            </div>

            <div class="admin-detail-row">
                <div class="admin-detail-label">休憩2</div>
                <div class="admin-detail-value admin-time-range">
                    <input type="time">
                    <span>〜</span>
                    <input type="time">
                </div>
            </div>

            <div class="admin-detail-row">
                <div class="admin-detail-label">備考</div>
                <div class="admin-detail-value">
                    <textarea name="remark">{{ $attendance->remark }}</textarea>
                </div>
                @error('remark')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

        </div>

        <div class="admin-attendance-detail__actions">
            <button type="submit" class="btn-black">修正</button>
        </div>
    </form>

</div>
@endsection