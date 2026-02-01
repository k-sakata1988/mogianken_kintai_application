<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AttendanceRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

        DB::transaction(function () use ($attendanceRequest) {

            $attendance = $attendanceRequest->attendance;
            $after = $attendanceRequest->after_data;

            if (!empty($after['clock_in_time'])) {
                $attendance->clock_in_time = $attendance->date->format('Y-m-d') . ' ' . $after['clock_in_time'];
            }
            if (isset($after['clock_out_time'])) {
                $attendance->clock_out_time = $attendance->date->format('Y-m-d') . ' ' . $after['clock_out_time'];
            }

            $attendance->is_modified = true;
            $attendance->save();

            $attendance->breaks()->delete();
            foreach ($after['breaks'] ?? [] as $break) {
                if (!empty($break['start']) && !empty($break['end'])) {
                    $attendance->breaks()->create([
                        'break_start' => $attendance->date->format('Y-m-d') . ' ' . $break['start'],
                        'break_end'   => $attendance->date->format('Y-m-d') . ' ' . $break['end'],
                    ]);
                }
            }

            $attendanceRequest->status = 'approved';
            $attendanceRequest->approver_user_id = Auth::id();
            $attendanceRequest->save();
        });

        return redirect()
            ->route('admin.request.list')
            ->with('success', '申請を承認しました');
    }
}