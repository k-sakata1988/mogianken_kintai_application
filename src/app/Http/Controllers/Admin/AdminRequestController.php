<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AttendanceRequest;
use Illuminate\Support\Facades\Auth;

class AdminRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');

        $requests = AttendanceRequest::with(['requester','attendance'])
            ->when($status === 'pending', function ($q) {
                $q->where('status', 'pending');
            })
            ->when($status === 'approved', function ($q) {
                $q->where('status', 'approved');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.request.list', compact('requests', 'status'));
    }

    public function show(AttendanceRequest $attendanceRequest)
    {
        $attendanceRequest->load([
            'requester',
            'attendance.user',
        ]);

        return view('admin.request.show', [
            'attendanceRequest' => $attendanceRequest,
        ]);
    }

    public function approve(AttendanceRequest $attendanceRequest)
    {
    
        if ($attendanceRequest->status !== 'pending') {
            return redirect()
                ->route('admin.request.list')
                ->with('error', 'すでに処理済みです');
        }

        $attendance = $attendanceRequest->attendance;
        $after = $attendanceRequest->after_data;

        $attendance->update([
            'clock_in_time'  => $after['clock_in_time']  ?? $attendance->clock_in_time,
            'clock_out_time' => $after['clock_out_time'] ?? $attendance->clock_out_time,
        ]);

        $attendance->breaks()->delete();

        if (!empty($after['break_start_1']) && !empty($after['break_end_1'])) {
            $attendance->breaks()->create([
                'break_start' => $after['break_start_1'],
                'break_end'   => $after['break_end_1'],
            ]);
        }

        if (!empty($after['break_start_2']) && !empty($after['break_end_2'])) {
            $attendance->breaks()->create([
                'break_start' => $after['break_start_2'],
                'break_end'   => $after['break_end_2'],
            ]);
        }

        $attendanceRequest->update([
            'status' => 'approved',
            'approver_user_id' => Auth::id(),
        ]);

        return redirect()
            ->route('admin.request.list')
            ->with('success', '申請を承認しました');
    }
}
