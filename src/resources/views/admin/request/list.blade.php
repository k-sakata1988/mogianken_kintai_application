    @extends('layouts.app')

    @section('css')
    <link rel="stylesheet" href="{{ asset('css/admin-request.css') }}">
    @endsection

    @section('content')
    <div class="admin-request">

        <div class="admin-title">
            <span class="bar"></span>
            <h1>申請一覧</h1>
        </div>

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

        <div class="request-card">
            <table class="request-table">
                <thead>
                    <tr>
                        <th>状態</th>
                        <th>名前</th>
                        <th>対象日</th>
                        <th>申請理由</th>
                        <th>申請日時</th>
                        <th>詳細</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($requests as $attendanceRequest)
                    <tr>
                        <td>
                            {{ $attendanceRequest->status === 'pending' ? '承認待ち' : '承認済み' }}
                        </td>

                        <td>
                            {{ $attendanceRequest->requester->name }}
                        </td>

                        <td>
                            {{ $attendanceRequest->attendance->date->format('Y/m/d') }}
                        </td>

                        <td>
                            {{ $attendanceRequest->reason }}
                        </td>

                        <td>
                            {{ $attendanceRequest->created_at->format('Y/m/d') }}
                        </td>

                        <td>
                            @if($attendanceRequest->status === 'approved')
                                {{ $attendanceRequest->updated_at->format('Y/m/d H:i') }}
                            @else
                                -
                            @endif
                        </td>

                        <td>
                            <a href="{{ route('admin.request.show', $attendanceRequest) }}">
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
