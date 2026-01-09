@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/request-list.css') }}">
@endsection

@section('content')
<div class="request-list">

    <h1 class="request-list__title">申請一覧</h1>

    <!-- tabs -->
    <div class="request-tabs">
        <a href="{{ route('user.stamp_correction_request.list', ['status' => 'pending']) }}"
           class="request-tab {{ $status === 'pending' ? 'active' : '' }}">
            承認待ち
        </a>

        <a href="{{ route('user.stamp_correction_request.list', ['status' => 'approved']) }}"
           class="request-tab {{ $status === 'approved' ? 'active' : '' }}">
            承認済み
        </a>
    </div>

    <div class="request-table-wrapper">
        <table class="request-table">
            <thead>
                <tr>
                    <th>状態</th>
                    <th>名前</th>
                    <th>対象日時</th>
                    <th>申請理由</th>
                    <th>申請日時</th>
                    <th>詳細</th>
                </tr>
            </thead>

            <tbody>
                @forelse($requests as $request)
                <tr>
                    <td>
                        @if($request->status === 'pending')
                            <span class="status pending">承認待ち</span>
                        @elseif($request->status === 'approved')
                            <span class="status approved">承認済み</span>
                        @else
                            <span class="status rejected">否認</span>
                        @endif
                    </td>

                    <td>{{ $request->requester?->name ?? '-' }}</td>
                    <td>{{ $request->attendance->date->format('Y/m/d') }}</td>
                    <td>{{ $request->reason }}</td>
                    <td>{{ $request->created_at->format('Y/m/d') }}</td>
                    <td>
                        <a href="{{ route('user.attendance.show', $request->attendance) }}">詳細</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">申請はありません</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
