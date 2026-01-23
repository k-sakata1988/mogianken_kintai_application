@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin-request.css') }}">
@endsection

@section('content')
<div class="admin-request">

    {{-- タイトル --}}
    <div class="admin-title">
        <span class="bar"></span>
        <h1>申請一覧</h1>
    </div>

    {{-- タブ --}}
    <div class="request-tabs">
        <a href="{{ route('admin.request.list', ['status' => 'pending']) }}"
           class="{{ $status === 'pending' ? 'active' : '' }}">
            承認待ち
        </a>

        <a href="{{ route('admin.request.list', ['status' => 'approved']) }}"
           class="{{ $status === 'approved' ? 'active' : '' }}">
            承認済み
        </a>
    </div>

    {{-- 一覧 --}}
    <div class="request-card">
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
                    <td>{{ $request->status === 'pending' ? '承認待ち' : '承認済み' }}</td>
                    <td>{{ $request->requester->name }}</td>
                    <td>{{ $request->attendance->date->format('Y/m/d') }}</td>
                    <td>{{ $request->reason }}</td>
                    <td>{{ $request->created_at->format('Y/m/d') }}</td>
                    <td>
                        <a href="{{ route('admin.attendance.show', $request->attendance_id) }}">
                            詳細
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">該当する申請はありません</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
